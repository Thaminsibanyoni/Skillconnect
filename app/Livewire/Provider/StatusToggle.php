<?php

namespace App\Livewire\Provider;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StatusToggle extends Component
{
    public bool $isOnline;

    public function mount()
    {
        // Initialize status from the authenticated user
        $this->isOnline = Auth::user()?->is_online ?? false;
    }

    public function toggleStatus()
    {
        $user = Auth::user();

        // Only allow providers to toggle status
        if ($user && $user->role === 'provider' && $user->status === 'approved') {
            $this->isOnline = !$this->isOnline;
            $user->is_online = $this->isOnline;
            $user->save();

            // Dispatch browser event to trigger JS for Echo channel joining/leaving
            $this->dispatch('provider-status-changed', online: $this->isOnline);

        } else {
            // Handle cases where user is not a provider or not approved
            session()->flash('status-error', 'Cannot change online status.');
            // Ensure local state matches DB state if update fails
            $this->isOnline = $user?->is_online ?? false;
        }
    }

    public function render()
    {
        // Only render if user is an approved provider
        if (Auth::check() && Auth::user()->role === 'provider' && Auth::user()->status === 'approved') {
            return view('livewire.provider.status-toggle');
        }

        // Return empty view or alternative content if not an approved provider
        return <<<'HTML'
            <div></div>
        HTML;
    }
}
