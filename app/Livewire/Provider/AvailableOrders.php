<?php

namespace App\Livewire\Provider;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class AvailableOrders extends Component
{
    use WithPagination;

    // Add filters later if needed (e.g., by distance, service type)

    public function claimOrder(int $orderId)
    {
        $provider = Auth::user();
        if (!$provider || $provider->role !== 'provider' || $provider->status !== 'approved') {
            session()->flash('error', 'Only approved providers can claim orders.');
            return;
        }

        DB::beginTransaction();
        try {
            $order = Order::where('id', $orderId)
                          ->whereNull('provider_id')
                          ->where('status', 'pending')
                          ->lockForUpdate()
                          ->first();

            if (!$order) {
                DB::rollBack();
                session()->flash('error', 'Order is no longer available to claim.');
                $this->dispatch('refreshAvailableOrders'); // Refresh list
                return;
            }

            // Check if provider offers the required service
            if (!$provider->services()->where('services.id', $order->service_id)->exists()) {
                 DB::rollBack();
                 session()->flash('error', 'You do not offer the required service for this order.');
                 return;
            }

            // TODO: Add check here if $provider serves the $order->city_id (requires city_id on order)
            // $orderCityId = $order->city_id; // Assuming order has city_id
            // if (!$provider->cities()->where('cities.id', $orderCityId)->exists()) {
            //      DB::rollBack();
            //      session()->flash('error', 'You do not serve the area for this order.');
            //      return;
            // }


            // Assign provider and update status
            $order->provider_id = $provider->id;
            $order->status = 'accepted';
            $order->save();

            DB::commit();

            // Notify seeker
            if ($order->seeker) {
                $order->seeker->notify(new \App\Notifications\OrderAcceptedNotification($order));
            }
            // TODO: Notify other providers who might have been notified that the order is taken?

            session()->flash('message', 'Order claimed successfully! It is now in your "My Orders" list.');
            $this->dispatch('refreshAvailableOrders'); // Refresh list

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error claiming order: ' . $e->getMessage(), ['order_id' => $orderId, 'provider_id' => $provider->id]);
            session()->flash('error', 'An error occurred while claiming the order.');
        }
    }

    #[On('refreshAvailableOrders')] // Listen for refresh event
    public function render()
    {
        $provider = Auth::user();
        $providerServiceIds = $provider->services()->pluck('services.id');
        // $providerCityIds = $provider->cities()->pluck('cities.id'); // TODO: Use this for filtering

        // Find pending orders, unassigned, matching provider's services and areas
        $query = Order::with(['seeker', 'service.serviceCategory']) // Eager load needed info
                      ->whereNull('provider_id')
                      ->where('status', 'pending')
                      ->whereIn('service_id', $providerServiceIds);
                      // TODO: Add ->whereIn('city_id', $providerCityIds) once orders have city_id

        // TODO: Add proximity filter based on provider's current location?

        $availableOrders = $query->latest()->paginate(10, ['*'], 'availablePage'); // Use different page name

        return view('livewire.provider.available-orders', [
            'availableOrders' => $availableOrders,
        ])->layout('layouts.app');
    }
}
