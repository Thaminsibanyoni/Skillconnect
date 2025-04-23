<?php

namespace App\Livewire\Seeker;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use WithPagination;

    // Add filters later if needed (e.g., by status)

    public function render()
    {
        $seeker = Auth::user();
        $orders = Order::with(['provider', 'service']) // Eager load provider and service
                       ->where('user_id', $seeker->id)
                       ->latest() // Show newest first
                       ->paginate(10);

        return view('livewire.seeker.order-history', [
            'orders' => $orders,
        ])->layout('layouts.app'); // Use main app layout
    }

    // Add methods for cancelling orders if applicable
}
