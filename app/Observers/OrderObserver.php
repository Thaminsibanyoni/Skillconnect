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
     * Notify the provider if assigned.
     */
    public function created(Order $order): void
    {
        // Check if a provider is assigned immediately upon creation
        if ($order->provider_id && $order->provider) {
            // Delay notification slightly to ensure transaction commits? Or use ShouldQueue on Notification
            $order->provider->notify(new \App\Notifications\NewOrderNotification($order));
        }
        // TODO: Add logic here or elsewhere to notify nearby available providers
        // if no provider is assigned initially. This might involve querying providers
        // based on location/service and broadcasting to the 'providers' channel or individually.
    }

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
