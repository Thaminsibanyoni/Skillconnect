<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Notifications\GeneralAdminNotification; // Create this notification class
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class NotificationManager extends Component
{
    public $targetGroup = 'all'; // 'all', 'seekers', 'providers'
    public $title = '';
    public $message = '';

    protected $rules = [
        'targetGroup' => 'required|in:all,seekers,providers',
        'title' => 'required|string|max:100',
        'message' => 'required|string|max:500',
    ];

    public function sendNotification()
    {
        $this->validate();

        $query = User::query();

        if ($this->targetGroup === 'seekers') {
            $query->where('role', 'seeker');
        } elseif ($this->targetGroup === 'providers') {
            $query->where('role', 'provider');
        }
        // 'all' targets everyone (including admins if they exist and are needed)

        $users = $query->get();

        if ($users->isEmpty()) {
            session()->flash('error', 'No users found for the selected target group.');
            return;
        }

        // Use Laravel's Notification facade to send to multiple users
        // We need a generic notification class for this
        Notification::send($users, new GeneralAdminNotification($this->title, $this->message));

        session()->flash('message', 'Notification sent successfully to ' . $users->count() . ' user(s).');
        $this->reset(['title', 'message', 'targetGroup']);
    }

    public function render()
    {
        return view('livewire.admin.notification-manager')
               ->layout('layouts.admin');
    }
}
