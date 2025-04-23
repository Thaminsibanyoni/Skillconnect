<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderAvailableNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB; // Import DB facade for potential raw queries

class FindAndNotifyProviders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->withoutRelations(); // Avoid serializing relations
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Re-fetch order to ensure freshness, or rely on the serialized one
        $order = $this->order;

        // Ensure order still needs a provider
        if ($order->provider_id || $order->status !== 'pending') {
            Log::info("Order #{$order->id} no longer requires provider assignment (checked in job).");
            return;
        }

        // 1. Get required service ID
        $serviceId = $order->service_id;
        // 2. Get order location (lat/lng)
        $orderLat = $order->latitude;
        $orderLng = $order->longitude;

        if (!$serviceId || is_null($orderLat) || is_null($orderLng)) {
            Log::error("Cannot find providers for order #{$order->id}: Missing service ID or location in job.");
            // TODO: Handle this case - maybe notify admin or seeker?
            return;
        }

        // 3. Query potential providers:
        $radius = config('skillconnect.provider_search_radius', 10); // Example: 10 km radius
        $maxProvidersToNotify = config('skillconnect.max_providers_notify', 10);

        // IMPORTANT: Geo-query refinement needed here for production.
        // 1. Determine the City/Zone of the order ($orderLat, $orderLng). This might involve:
        //    - Adding city_id to orders table (requires migration & logic during booking).
        //    - Or, performing a reverse geocode lookup here (can be slow, better in Job).
        //    - Or, querying cities table for the nearest city center (less accurate).
        // $targetCityId = $this->getCityIdFromCoordinates($orderLat, $orderLng); // Example helper needed

        // 2. Replace bounding box with a proper spatial distance query using DB functions
        //    (e.g., ST_Distance_Sphere in MySQL/PostGIS) or a package like grimzy/laravel-mysql-spatial.
        //    This allows ordering by distance.

        $providers = User::query()
            ->where('role', 'provider')
            ->where('status', 'approved')
            ->where('is_online', true)
            ->whereHas('services', fn($q) => $q->where('services.id', $serviceId))
            // ->whereHas('cities', fn($q) => $q->where('cities.id', $order->city_id)) // City check might be redundant/alternative to spatial
            // Spatial Query (Example for MySQL >= 5.7)
            // Assumes 'latitude' and 'longitude' columns exist on users table
            // Convert radius from km to degrees approx (very rough, use proper library/conversion if accuracy is critical)
            // $radiusInDegrees = $radius / 111.32; // Approx km per degree latitude
            // ->whereRaw(
            //     'ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) <= ?',
            //     [$orderLng, $orderLat, $radius * 1000] // ST_Distance_Sphere uses meters
            // )
            // ->orderByRaw(
            //     'ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) asc',
            //      [$orderLng, $orderLat]
            // )
            // Placeholder - Requires spatial column type & index for performance. Using basic filtering for now.
            // Add a basic bounding box filter as a simpler alternative (less accurate)
             ->whereBetween('latitude', [$orderLat - ($radius / 111.0), $orderLat + ($radius / 111.0)]) // Approx degrees latitude
             ->whereBetween('longitude', [
                 $orderLng - ($radius / (111.32 * cos(deg2rad($orderLat)))),
                 $orderLng + ($radius / (111.32 * cos(deg2rad($orderLat))))
             ]) // Approx degrees longitude
            ->withCount(['ordersAsProvider' => fn($q) => $q->whereIn('status', ['accepted', 'in_progress'])])
            ->orderBy('orders_as_provider_count', 'asc')
            ->limit($maxProvidersToNotify)
            ->get();

        Log::info("Found " . $providers->count() . " potential providers for order #{$order->id}");

        // 4. Notify each found provider
        if ($providers->isNotEmpty()) {
            // Use Laravel's Notification facade to send to multiple users
            Notification::send($providers, new OrderAvailableNotification($order));
            Log::info("Sent OrderAvailableNotification for order #{$order->id} to " . $providers->count() . " providers.");
        } else {
            // Handle case where no providers are found (e.g., notify admin, queue order, set order status to 'no_providers')
            Log::warning("No suitable providers found for order #{$order->id}.");
            // Optionally update order status
            // $order->status = 'no_providers_available';
            // $order->saveQuietly(); // Avoid triggering observer again
        }
    }
}
