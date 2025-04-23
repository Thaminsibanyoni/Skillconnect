<?php

namespace App\Livewire\Provider;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManagement extends Component
{
    use WithPagination;

    public $statusFilter = 'pending'; // Default to show pending orders

    // Reset pagination when filter changes
    public function updatingStatusFilter() { $this->resetPage(); }

    public function acceptOrder(int $orderId)
    {
        $provider = Auth::user();
        $order = Order::where('id', $orderId)
                      ->where('provider_id', $provider->id)
                      ->where('status', 'pending') // Can only accept pending orders
                      ->firstOrFail(); // Fails if not found or not pending/assigned

        $order->status = 'accepted';
        $order->save();

        // Notify seeker
        if ($order->seeker) {
            $order->seeker->notify(new \App\Notifications\OrderAcceptedNotification($order));
        }

        session()->flash('message', 'Order accepted successfully.');
        // No need to explicitly refresh, Livewire handles it
    }

    public function rejectOrder(int $orderId)
    {
        $provider = Auth::user();
        $order = Order::where('id', $orderId)
                      ->where('provider_id', $provider->id)
                      ->whereIn('status', ['pending', 'accepted']) // Can reject pending or accepted
                      ->firstOrFail();

        $order->status = 'rejected';
        // Optionally nullify provider_id if rejected? Depends on business logic
        // $order->provider_id = null;
        $order->save();

        // Notify seeker
         if ($order->seeker) {
            $order->seeker->notify(new \App\Notifications\OrderRejectedNotification($order));
        }

        // TODO: Potentially re-assign or notify other providers if rejected

        session()->flash('message', 'Order rejected.');
    }

    public function startJob(int $orderId)
    {
        $provider = Auth::user();
        $order = Order::where('id', $orderId)
                      ->where('provider_id', $provider->id)
                      ->where('status', 'accepted') // Can only start accepted orders
                      ->firstOrFail();

        $order->status = 'in_progress';
        $order->save();

        // Notify seeker
        if ($order->seeker) {
            $order->seeker->notify(new \App\Notifications\OrderStartedNotification($order));
        }

        session()->flash('message', 'Job started successfully.');
    }

    public function completeJob(int $orderId)
    {
        $provider = Auth::user();
        $order = Order::where('id', $orderId)
                      ->where('provider_id', $provider->id)
                      ->where('status', 'in_progress') // Can only complete in-progress orders
                      ->firstOrFail();

        // TODO: Potentially add validation here (e.g., check if payment was received if required before completion)

        $order->status = 'completed';
        $order->save(); // This triggers the OrderObserver to calculate commission

        // Notify seeker
        if ($order->seeker) {
            $order->seeker->notify(new \App\Notifications\OrderCompletedNotification($order));
        }

        // TODO: Trigger payout logic/transaction creation for provider (minus commission)

        session()->flash('message', 'Job marked as completed successfully.');
    }

    public function claimOrder(int $orderId)
    {
        $provider = Auth::user();
        if (!$provider || $provider->role !== 'provider' || $provider->status !== 'approved') {
            session()->flash('error', 'Only approved providers can claim orders.');
            return;
        }

        // Use DB Transaction to prevent race conditions
        DB::beginTransaction();
        try {
            // Lock the order row for update to prevent concurrent claims
            $order = Order::where('id', $orderId)
                          ->whereNull('provider_id') // Must be unassigned
                          ->where('status', 'pending') // Must be pending
                          ->lockForUpdate() // Pessimistic lock
                          ->first();

            if (!$order) {
                // Order was already claimed or status changed
                DB::rollBack();
                session()->flash('error', 'Order is no longer available to claim.');
                return;
            }

            // Check if provider offers the required service and serves the area (TODO: Add area check)
            if (!$provider->services()->where('services.id', $order->service_id)->exists()) {
                 DB::rollBack();
                 session()->flash('error', 'You do not offer the required service for this order.');
                 return;
            }
            // TODO: Add check here if $provider serves the $order->city_id (requires city_id on order)

            // Assign provider and update status
            $order->provider_id = $provider->id;
            $order->status = 'accepted'; // Or maybe a specific 'claimed' status first?
            $order->save();

            DB::commit();

            // Notify seeker
            if ($order->seeker) {
                $order->seeker->notify(new \App\Notifications\OrderAcceptedNotification($order));
            }
            // TODO: Notify other providers who might have been notified that the order is taken?

            session()->flash('message', 'Order claimed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error claiming order: ' . $e->getMessage(), ['order_id' => $orderId, 'provider_id' => $provider->id]);
            session()->flash('error', 'An error occurred while claiming the order.');
        }
    }


    public function render()
    {
        $provider = Auth::user();
        $query = Order::with(['seeker', 'service']) // Eager load seeker and service
                      ->where('provider_id', $provider->id);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->latest()->paginate(10);

        return view('livewire.provider.order-management', [
            'orders' => $orders,
        ])->layout('layouts.app'); // Use the main app layout for now
    }
}
