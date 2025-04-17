<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderManagement extends Component
{
    use WithPagination;

    public $statusFilter = ''; // e.g., 'pending', 'completed'
    public $search = '';

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Order::with(['seeker', 'provider', 'service']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('seeker', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('provider', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('service', function ($serviceQuery) {
                    $serviceQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('id', 'like', '%' . $this->search . '%'); // Allow searching by Order ID
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('livewire.admin.order-management', [
            'orders' => $orders,
        ])->layout('layouts.admin');
    }

    // Add methods for assigning orders later
}
