<?php

namespace App\Livewire\Provider;

use App\Models\PayoutRequest;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination; // For listing requests

class WalletPayout extends Component
{
    use WithPagination;

    public $balance = 0.00;
    public $payoutAmount;
    public $pendingPayoutExists = false;

    protected $rules = [
        'payoutAmount' => 'required|numeric|min:1|max:', // Max will be set dynamically
    ];

    public function mount()
    {
        $this->loadBalanceAndPending();
    }

    public function loadBalanceAndPending()
    {
        $user = Auth::user();
        $wallet = $user->wallet()->firstOrCreate(['user_id' => $user->id]);
        $this->balance = $wallet->balance;

        // Check if there's already a pending request
        $this->pendingPayoutExists = PayoutRequest::where('user_id', $user->id)
                                                ->where('status', 'pending')
                                                ->exists();
    }

    public function requestPayout()
    {
        $user = Auth::user();
        $this->loadBalanceAndPending(); // Refresh balance and pending status

        if ($this->pendingPayoutExists) {
            session()->flash('error', 'You already have a pending payout request.');
            return;
        }

        // Dynamically set max validation rule based on current balance
        $this->rules['payoutAmount'] .= $this->balance;
        $this->validate();

        // Create the payout request
        PayoutRequest::create([
            'user_id' => $user->id,
            'amount' => $this->payoutAmount,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Payout requested successfully. It will be processed by an admin.');
        $this->reset('payoutAmount');
        $this->loadBalanceAndPending(); // Refresh pending status
    }

    public function render()
    {
        $user = Auth::user();
        // Fetch past payout requests for history display
        $payoutHistory = PayoutRequest::where('user_id', $user->id)
                                      ->latest()
                                      ->paginate(5, ['*'], 'payoutPage'); // Use named pagination

        return view('livewire.provider.wallet-payout', [
            'payoutHistory' => $payoutHistory
        ])->layout('layouts.app'); // Assuming providers use the main app layout for now
    }
}
