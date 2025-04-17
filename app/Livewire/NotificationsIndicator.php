<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On; // Import the On attribute

class NotificationsIndicator extends Component
{
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadUnreadCount();
    }

    // Listen for broadcasted events on the private user channel
    #[On('echo-private:App.Models.User.{auth()->id()},notification')]
    public function handleNewNotification($event)
    {
        // Increment count when a new notification is received via broadcast
        // A more robust approach might involve checking notification type or re-fetching count
        $this->unreadCount++;
        // Optionally dispatch a browser event for JS-based UI updates (e.g., toast)
        // $this->dispatch('new-notification-received', message: $event['message'] ?? 'New notification!');
    }

    public function loadUnreadCount()
    {
        if (Auth::check()) {
            $this->unreadCount = Auth::user()->unreadNotifications()->count();
        } else {
            $this->unreadCount = 0;
        }
    }

    public function markAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->loadUnreadCount(); // Refresh count
        }
    }

    public function render()
    {
        return view('livewire.notifications-indicator');
    }
}
