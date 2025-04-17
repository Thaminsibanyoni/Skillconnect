<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionManagement extends Component
{
    use WithPagination;

    public $typeFilter = ''; // e.g., 'payment', 'payout'
    public $statusFilter = ''; // 'pending', 'completed', 'failed'
    public $search = ''; // Search user name/email, order ID, reference

    // Reset pagination when filters change
    public function updatingSearch() { $this->resetPage(); }
    public function updatingTypeFilter() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function render()
    {
        $query = Transaction::with(['user', 'order']); // Eager load relationships

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhere('transaction_reference', 'like', '%' . $this->search . '%')
                  ->orWhere('order_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $transactions = $query->latest()->paginate(15); // Order by newest first

        return view('livewire.admin.transaction-management', [
            'transactions' => $transactions,
        ])->layout('layouts.admin');
    }

    // Add methods for viewing details or managing transactions later if needed
}
