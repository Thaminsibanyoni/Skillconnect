<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User; // Import User
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB; // Import DB
use Illuminate\Support\Facades\Log; // Import Log

class OrderManagement extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $search = '';

    // Assign Provider Modal Properties
    public $showAssignModal = false;
    public $assigningOrderId = null;
    public ?Order $assigningOrder = null; // To hold the order object
    public $availableProviders = [];
    public $selectedProviderId = null;


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

    public function showAssignModal($orderId)
    {
        $this->reset(['selectedProviderId', 'availableProviders']); // Reset previous modal state
        $this->assigningOrderId = $orderId;
        $this->assigningOrder = Order::with('service', 'city')->find($orderId);

        if (!$this->assigningOrder || $this->assigningOrder->provider_id || $this->assigningOrder->status !== 'pending') {
            session()->flash('error', 'Order cannot be assigned.');
            $this->assigningOrderId = null;
            $this->assigningOrder = null;
            return;
        }

        // Find suitable providers (online, approved, offers service, serves city)
        $query = User::query()
            ->where('role', 'provider')
            ->where('status', 'approved')
            ->where('is_online', true) // Only online providers
            ->whereHas('services', fn($q) => $q->where('services.id', $this->assigningOrder->service_id));

        if ($this->assigningOrder->city_id) {
             $query->whereHas('cities', fn($q) => $q->where('cities.id', $this->assigningOrder->city_id));
        }
        // TODO: Add proximity filter here if city_id is null?

        $this->availableProviders = $query->orderBy('name')->get(['id', 'name']); // Get only ID and name

        if ($this->availableProviders->isEmpty()) {
             session()->flash('error', 'No suitable online providers found for this order currently.');
             // Optionally keep modal closed or show message in modal
             $this->assigningOrderId = null;
             $this->assigningOrder = null;
             return;
        }

        $this->resetErrorBag();
        $this->showAssignModal = true;
    }

    public function assignProvider()
    {
        $this->validate([
            'selectedProviderId' => 'required|exists:users,id',
        ]);

        if (!$this->assigningOrder) {
             session()->flash('error', 'Order not found.');
             $this->closeAssignModal();
             return;
        }

        DB::beginTransaction();
        try {
            // Re-fetch order with lock to prevent race conditions
             $order = Order::where('id', $this->assigningOrderId)
                          ->whereNull('provider_id')
                          ->where('status', 'pending')
                          ->lockForUpdate()
                          ->first();

             if (!$order) {
                 DB::rollBack();
                 session()->flash('error', 'Order was already assigned or is no longer pending.');
                 $this->closeAssignModal();
                 return;
             }

             $provider = User::find($this->selectedProviderId);
             if (!$provider || $provider->role !== 'provider') {
                  DB::rollBack();
                  session()->flash('error', 'Invalid provider selected.');
                  return; // Keep modal open
             }

             // Assign provider and update status
             $order->provider_id = $this->selectedProviderId;
             $order->status = 'accepted';
             $order->save();

             DB::commit();

             // Notify provider and seeker
             $provider->notify(new \App\Notifications\NewOrderNotification($order)); // Notify assigned provider
             if ($order->seeker) {
                 $order->seeker->notify(new \App\Notifications\OrderAcceptedNotification($order));
             }

             session()->flash('message', 'Provider assigned successfully.');
             $this->closeAssignModal();

        } catch (\Exception $e) {
             DB::rollBack();
             Log::error('Error assigning provider: ' . $e->getMessage(), [
                'order_id' => $this->assigningOrderId,
                'provider_id' => $this->selectedProviderId
             ]);
             session()->flash('error', 'An error occurred while assigning the provider.');
             // Keep modal open potentially
        }
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->assigningOrderId = null;
        $this->assigningOrder = null;
        $this->availableProviders = [];
        $this->selectedProviderId = null;
        $this->resetErrorBag();
    }

    // Add methods for viewing details or managing transactions later if needed
}
