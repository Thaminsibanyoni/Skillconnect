<?php

namespace App\Livewire\Admin;

use App\Models\City;
use App\Models\Province;
use App\Models\User;
use App\Traits\LogsAdminActivity; // Import the trait
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination, LogsAdminActivity; // Use the trait

    // Existing properties

    public $roleFilter = '';
    public $statusFilter = '';
    public $search = '';

    // Properties for managing service areas modal
    public $showAreasModal = false;
    public ?User $editingUser = null; // User being edited
    public $allProvinces = [];
    public $allCities = [];
    public $assignedCityIds = [];

    // Properties for managing roles modal
    public $showRoleModal = false;
    // editingUser is already defined
    public $allRoles = [];
    public $assignedRoles = []; // [role_id => boolean]


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

    public function mount()
    {
        // Pre-load provinces and cities for the modal
        $this->allProvinces = Province::orderBy('name')->get();
        $this->allCities = City::with('province')->orderBy('province_id')->orderBy('name')->get();
        $this->allRoles = \Spatie\Permission\Models\Role::orderBy('name')->get(); // Load all roles
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
            $this->logAdminActivity('approved_user', $user); // Log activity
            session()->flash('message', 'User approved successfully.');
        } else {
            session()->flash('error', 'Cannot approve this user.');
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
            $this->logAdminActivity('suspended_user', $user); // Log activity
            session()->flash('message', 'User suspended successfully.');
        } else {
            session()->flash('error', 'Cannot suspend this user.');
        }
        // Optionally, you might want to refresh the component or emit an event
    }

    // --- Service Area Management Methods ---

    public function manageAreas(int $userId)
    {
        $this->editingUser = User::find($userId);
        if (!$this->editingUser || $this->editingUser->role !== 'provider') {
            session()->flash('error', 'Can only manage areas for providers.');
            $this->editingUser = null;
            return;
        }

        // Initialize assignedCityIds based on current relationships
        $currentCityIds = $this->editingUser->cities()->pluck('cities.id')->flip()->toArray();
        $this->assignedCityIds = $this->allCities->mapWithKeys(function ($city) use ($currentCityIds) {
            return [$city->id => isset($currentCityIds[$city->id])];
        })->toArray();

        $this->resetErrorBag(); // Clear previous validation errors
        $this->showAreasModal = true;
    }

    public function saveAreas()
    {
        if (!$this->editingUser) {
            return;
        }

        try {
            // Collect checked city IDs
            $selectedIds = collect($this->assignedCityIds)
                                ->filter(fn ($value) => $value === true || $value === "1" || $value === 1)
                                ->keys()
                                ->toArray();

            // Check subscription limit
            $planSlug = $this->editingUser->subscription_plan;
            $plan = $planSlug ? \App\Models\SubscriptionPlan::where('slug', $planSlug)->first() : null;
            $maxCities = $plan?->max_cities; // Get limit from DB plan

            if (!is_null($maxCities) && count($selectedIds) > $maxCities) {
                 session()->flash('error', "This provider's plan allows a maximum of {$maxCities} service areas.");
                 // Keep modal open
                 $this->showAreasModal = true; // Explicitly keep modal open on error
                 return;
            }

            // Sync the cities relationship
            $this->editingUser->cities()->sync($selectedIds);

            $this->logAdminActivity('updated_provider_areas', $this->editingUser, ['city_ids' => $selectedIds]); // Log activity
            session()->flash('message', "{$this->editingUser->name}'s service areas updated.");
            $this->closeAreasModal();

        } catch (\Exception $e) {
             Log::error('Error saving provider service areas: ' . $e->getMessage(), [
                'provider_id' => $this->editingUser->id,
                'selected_ids' => $this->assignedCityIds
             ]);
             session()->flash('error', 'An error occurred while saving service areas.');
        }
    }

    public function closeAreasModal()
    {
        $this->showAreasModal = false;
        $this->editingUser = null;
        $this->assignedCityIds = [];
    }

    // --- Role Management Methods ---

    public function showRoleModal(int $userId)
    {
        $this->editingUser = User::find($userId);
        if (!$this->editingUser) {
            session()->flash('error', 'User not found.');
            return;
        }

        // Initialize assignedRoles based on current user roles
        $currentUserRoleIds = $this->editingUser->roles()->pluck('id')->flip()->toArray();
        $this->assignedRoles = $this->allRoles->mapWithKeys(function ($role) use ($currentUserRoleIds) {
            return [$role->id => isset($currentUserRoleIds[$role->id])];
        })->toArray();

        $this->resetErrorBag();
        $this->showRoleModal = true;
    }

     public function saveRoles()
    {
        if (!$this->editingUser) {
            return;
        }

        try {
            $selectedRoleIds = collect($this->assignedRoles)
                                ->filter(fn ($value) => $value === true || $value === "1" || $value === 1)
                                ->keys()
                                ->toArray();

            $rolesToAssign = $this->allRoles->whereIn('id', $selectedRoleIds)->pluck('name');

            // Sync roles using Spatie's method
            $this->editingUser->syncRoles($rolesToAssign);

            $this->logAdminActivity('updated_user_roles', $this->editingUser, ['roles' => $rolesToAssign->toArray()]);
            session()->flash('message', "{$this->editingUser->name}'s roles updated.");
            $this->closeRoleModal();

        } catch (\Exception $e) {
             Log::error('Error saving user roles: ' . $e->getMessage(), [
                'user_id' => $this->editingUser->id,
                'selected_roles' => $this->assignedRoles
             ]);
             session()->flash('error', 'An error occurred while saving roles.');
        }
    }

    public function closeRoleModal()
    {
        $this->showRoleModal = false;
        $this->editingUser = null;
        $this->assignedRoles = [];
    }
}
