<?php

namespace App\Livewire\Admin;

use App\Models\Wallet;
use Livewire\Component;
use Livewire\WithPagination;

class WalletManagement extends Component
{
    use WithPagination;

    public $search = ''; // Search user name/email

    // Reset pagination when search changes
    public function updatingSearch() { $this->resetPage(); }

    public function render()
    {
        $query = Wallet::with(['user']); // Eager load user relationship

        if ($this->search) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Optionally filter by user role if needed later
        // $query->whereHas('user', fn($q) => $q->where('role', 'provider'));

        $wallets = $query->orderByDesc('balance')->paginate(15); // Order by balance

        return view('livewire.admin.wallet-management', [
            'wallets' => $wallets,
        ])->layout('layouts.admin');
    }

    // Add methods for manual adjustments or viewing history later if needed
}
