<?php

namespace App\Livewire\Provider;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $approvedCities = [];

    public function mount()
    {
        // Load cities with their provinces
        $this->approvedCities = Auth::user()
                                    ->cities()
                                    ->with('province') // Eager load province
                                    ->get();
    }

    public function render()
    {
        // TODO: Add provider-specific stats (earnings, ratings, pending jobs etc.)
        return view('livewire.provider.dashboard')
               ->layout('layouts.app'); // Use main app layout for now
    }
}
