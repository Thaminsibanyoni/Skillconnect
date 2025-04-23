<?php

namespace App\Livewire\Provider;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageServices extends Component
{
    public $allServices = [];
    public $providerServices = []; // Array to hold IDs of selected services

    public function mount()
    {
        $this->allServices = Service::with('serviceCategory')->orderBy('name')->get();
        // Initialize providerServices as an associative array [service_id => boolean]
        $providerServiceIds = Auth::user()->services()->pluck('services.id')->flip()->toArray(); // Get IDs as keys
        $this->providerServices = $this->allServices->mapWithKeys(function ($service) use ($providerServiceIds) {
            return [$service->id => isset($providerServiceIds[$service->id])]; // Set true if provider has the service, false otherwise
        })->toArray();
    }

    public function saveServices()
    {
        $provider = Auth::user();

        // Ensure user is a provider
        if (!$provider || $provider->role !== 'provider') {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        try {
            // Sync the selected services using the many-to-many relationship
            // Collect the keys (service IDs) where the value is true (checkbox is checked)
            $selectedIds = collect($this->providerServices)
                                ->filter(fn ($value) => $value === true || $value === "1" || $value === 1) // Check for true/checked values
                                ->keys()
                                ->toArray();

            $provider->services()->sync($selectedIds);

            session()->flash('message', 'Your offered services have been updated.');
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error('Error saving provider services: ' . $e->getMessage(), [
                'provider_id' => $provider->id,
                'selected_ids' => $this->providerServices
             ]);
             session()->flash('error', 'An error occurred while saving your services.');
        }
    }


    public function render()
    {
        return view('livewire.provider.manage-services')
               ->layout('layouts.app'); // Use main app layout for now
    }
}
