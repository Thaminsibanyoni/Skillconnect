<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Config; // Import Config facade

class OrderObserver
{

    /**
     * Handle the Order "updating" event.
     * Calculate commission when order status changes to 'completed'.
     */
    public function updating(Order $order): void
    {
        // Check if the status is being changed to 'completed' and was not already 'completed'
        if ($order->isDirty('status') && $order->status === 'completed' && $order->getOriginal('status') !== 'completed') {
            $commissionType = config('skillconnect.commission.type', 'percentage');
            $commissionRate = (float) config('skillconnect.commission.rate', 0);
            $totalAmount = (float) $order->total_amount;
            $commissionAmount = 0;

            if ($totalAmount > 0 && $commissionRate > 0) {
                if ($commissionType === 'percentage') {
                    $commissionAmount = ($totalAmount * $commissionRate) / 100;
                } elseif ($commissionType === 'fixed') {
                    $commissionAmount = $commissionRate;
                    // Ensure fixed commission doesn't exceed total amount
                    if ($commissionAmount > $totalAmount) {
                        $commissionAmount = $totalAmount;
                    }
                }
            }

            // Update the order model directly before it's saved
            $order->commission_rate = $commissionRate; // Store the rate used
            $order->commission_amount = $commissionAmount;

            // Note: Logic to create a 'commission' transaction and update provider wallet
            // should ideally happen in the 'updated' event or a dedicated service/job
            // after the order is successfully saved as 'completed'.
        }
    }


    /**
     * Handle the Order "created" event.
     * Notify the assigned provider or find available providers.
     */
    public function created(Order $order): void
    {
        if ($order->provider_id && $order->provider) {
            // Direct assignment: Notify the specific provider
            Log::info("Notifying assigned provider {$order->provider_id} for order #{$order->id}");
            $order->provider->notify(new \App\Notifications\NewOrderNotification($order));
        } elseif (!$order->provider_id) {
            // Unassigned order: Find and notify nearby available providers
            Log::info("Order #{$order->id} created without provider. Finding suitable providers...");
            // Dispatch the job to handle finding and notifying providers asynchronously
             \App\Jobs\FindAndNotifyProviders::dispatch($order->withoutRelations()); // Pass order without relations
        }
    }

    // Removed the findAndNotifyProviders method from here as it's now in the Job

    /**
     * Handle the Order "updated" event.
    //     $orderLat = $order->latitude;
    //     $orderLng = $order->longitude;

    //     if (!$serviceId || is_null($orderLat) || is_null($orderLng)) {
    //         Log::error("Cannot find providers for order #{$order->id}: Missing service ID or location.");
    //         return;
    //     }

    //     // 3. Query potential providers:
    //     //    - Have the 'provider' role
    //     //    - Are 'approved'
    //     //    - Are 'is_online'
    //     //    - Offer the required service (check service_user pivot table)
    //     //    - Are within a certain radius of the order location (requires geo-query)
    //     $radius = 10; // Example: 10 km radius
    //     $providers = \App\Models\User::query()
    //         ->where('role', 'provider')
    //         ->where('status', 'approved')
    //         ->where('is_online', true)
    //         ->whereHas('services', fn($q) => $q->where('services.id', $serviceId))
    //         // Basic Bounding Box location query (replace with proper spatial query later)
    //         ->whereBetween('latitude', [$orderLat - ($radius / 111), $orderLat + ($radius / 111)]) // Approx conversion
    //         ->whereBetween('longitude', [
    //             $orderLng - ($radius / (111 * cos(deg2rad($orderLat)))),
    //             $orderLng + ($radius / (111 * cos(deg2rad($orderLat))))
    //         ])
    //         ->limit(10) // Limit the number of providers notified initially
    //         ->get();

    //     Log::info("Found " . $providers->count() . " potential providers for order #{$order->id}");

        // 4. Notify each found provider
        if ($providers->isNotEmpty()) {
            // Use Laravel's Notification facade to send to multiple users
            \Illuminate\Support\Facades\Notification::send($providers, new \App\Notifications\OrderAvailableNotification($order));
            Log::info("Sent OrderAvailableNotification for order #{$order->id} to " . $providers->count() . " providers.");
        } else {
            // Handle case where no providers are found (e.g., notify admin, queue order, set order status to 'no_providers')
            Log::warning("No suitable providers found for order #{$order->id}.");
            // Optionally update order status
            // $order->status = 'no_providers_available';
            // $order->saveQuietly(); // Avoid triggering observer again
        // } // End of removed method
    // } // End of removed method

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
