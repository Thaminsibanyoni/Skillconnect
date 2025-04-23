<?php

namespace App\Livewire\Provider;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionManager extends Component
{
    public $plans = [];
    public ?User $user;
    public $currentPlan = null; // Holds the DB plan model if subscribed

    public function mount()
    {
        $this->user = Auth::user();
        $this->plans = SubscriptionPlan::where('is_active', true)->orderBy('price')->get();

        if ($this->user->subscription_plan && $this->user->subscription_status === 'active') {
            // Find the plan details in DB based on the stored slug
            $this->currentPlan = SubscriptionPlan::where('slug', $this->user->subscription_plan)->first();
        }
    }

    // Placeholder for cancelling - would need more logic
    public function cancelSubscription()
    {
        // In a real scenario:
        // 1. Maybe call payment gateway API to cancel recurring payment if applicable.
        // 2. Update user's subscription status and expiry date.
        $this->user->update([
            'subscription_status' => 'cancelled',
            // Keep expires_at or set it based on cancellation policy
        ]);
        $this->mount(); // Refresh data
        session()->flash('message', 'Subscription cancelled (simulated).');
    }


    public function render()
    {
        return view('livewire.provider.subscription-manager')
               ->layout('layouts.app');
    }
}
