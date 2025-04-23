<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Carbon\Carbon; // For date/time handling

class BookingForm extends Component
{
    public User $provider; // Passed in via mount or view
    public $availableServices = [];

    // Form properties
    public $selectedServiceId = null;
    public $scheduleType = 'now'; // 'now' or 'later'
    public $scheduledDateTime = null;
    public $address = '';
    public $latitude = null; // Optional: For map integration later
    public $longitude = null; // Optional: For map integration later

    protected $rules = [
        'selectedServiceId' => 'required|exists:services,id',
        'scheduleType' => 'required|in:now,later',
        'scheduledDateTime' => 'required_if:scheduleType,later|nullable|date|after_or_equal:now',
        'address' => 'required|string|max:255',
        // Add validation for lat/lng if they become required
    ];

    public function mount(User $provider)
    {
        $this->provider = $provider;
        // Load only services offered by this specific provider
        $this->availableServices = $this->provider->services()->orderBy('name')->get();

        // Pre-select if only one service is available?
        // if ($this->availableServices->count() === 1) {
        //     $this->selectedServiceId = $this->availableServices->first()->id;
        // }
    }

    public function createOrder()
    {
        $this->validate();

        $seeker = Auth::user();

        // Ensure user is logged in and is a seeker
        if (!$seeker || $seeker->role !== 'seeker') {
            // Redirect to login or show error
            session()->flash('error', 'Please log in as a service seeker to book.');
            return redirect()->route('login');
        }

        // Prevent booking own services
        if ($seeker->id === $this->provider->id) {
             session()->flash('error', 'You cannot book your own services.');
             return;
        }

        try {
            $order = Order::create([
                'user_id' => $seeker->id,
                'provider_id' => $this->provider->id,
                'service_id' => $this->selectedServiceId,
                'status' => 'pending', // Initial status
                'scheduled_at' => ($this->scheduleType === 'later' && $this->scheduledDateTime)
                                    ? Carbon::parse($this->scheduledDateTime)
                                    : null, // Null if 'now'
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'total_amount' => null, // Amount might be set later or based on service
                // Commission fields will be set by observer upon completion
            ]);

            // Reset form?
            // $this->resetForm();

            // Redirect to order confirmation/payment page or show success message
            session()->flash('success', 'Booking request sent successfully! Order ID: ' . $order->id);
            // Potentially redirect: return redirect()->route('orders.show', $order);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Booking Error: ' . $e->getMessage(), [
                'seeker_id' => $seeker->id,
                'provider_id' => $this->provider->id,
                'service_id' => $this->selectedServiceId,
            ]);
            session()->flash('error', 'An error occurred while creating your booking. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.booking-form');
    }

    // Helper to reset form (optional)
    // private function resetForm() { ... }
}
