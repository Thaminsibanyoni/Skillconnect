<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $roleFilter = ''; // 'seeker', 'provider', or '' for all
    public $statusFilter = ''; // 'pending', 'approved', 'suspended', or '' for all
    public $search = '';

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingRoleFilter()
    {
        $this->resetPage();
    }
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }


    public function render()
    {
        // Fetch users who are not admins, applying filters
        $query = User::where('role', '!=', 'admin');

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
        ])->layout('layouts.admin'); // Use the admin layout
    }

    /**
     * Approve a user (typically a provider).
     */
    public function approveUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        // Ensure we are not trying to approve an admin or someone already approved
        if ($user->role !== 'admin' && $user->status !== 'approved') {
            $user->status = 'approved';
            $user->save();
            session()->flash('message', 'User approved successfully.'); // Optional: Flash message
        } else {
            session()->flash('error', 'Cannot approve this user.'); // Optional: Error message
        }
        // Optionally, you might want to refresh the component or emit an event
    }

    /**
     * Suspend a user.
     */
    public function suspendUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        // Ensure we are not trying to suspend an admin or someone already suspended
        if ($user->role !== 'admin' && $user->status !== 'suspended') {
            $user->status = 'suspended';
            $user->save();
            session()->flash('message', 'User suspended successfully.'); // Optional: Flash message
        } else {
            session()->flash('error', 'Cannot suspend this user.'); // Optional: Error message
        }
        // Optionally, you might want to refresh the component or emit an event
    }
}
