<?php

namespace App\Livewire\Admin;

use App\Traits\LogsAdminActivity;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissions extends Component
{
    use WithPagination, LogsAdminActivity;

    // Properties for creating Role/Permission
    public $newRoleName = '';
    public $newPermissionName = '';

    // Properties for managing permissions for a role
    public $editingRole = null;
    public $rolePermissions = []; // [permission_id => boolean]

    protected $rules = [
        'newRoleName' => 'required|string|max:50|unique:roles,name',
        'newPermissionName' => 'required|string|max:50|unique:permissions,name',
    ];

    public function createRole()
    {
        $this->validateOnly('newRoleName');
        $role = Role::create(['name' => $this->newRoleName, 'guard_name' => 'web']);
        $this->logAdminActivity('created_role', $role);
        session()->flash('message', 'Role created successfully.');
        $this->reset('newRoleName');
    }

    public function createPermission()
    {
        $this->validateOnly('newPermissionName');
        $permission = Permission::create(['name' => $this->newPermissionName, 'guard_name' => 'web']);
        $this->logAdminActivity('created_permission', $permission);
        session()->flash('message', 'Permission created successfully.');
        $this->reset('newPermissionName');
    }

    public function editRolePermissions(Role $role)
    {
        $this->editingRole = $role;
        $currentPermissions = $role->permissions()->pluck('id')->flip()->toArray();
        $allPermissions = Permission::orderBy('name')->get();

        $this->rolePermissions = $allPermissions->mapWithKeys(function ($permission) use ($currentPermissions) {
            return [$permission->id => isset($currentPermissions[$permission->id])];
        })->toArray();
    }

    public function saveRolePermissions()
    {
        if (!$this->editingRole) return;

        $selectedPermissionIds = collect($this->rolePermissions)
                                    ->filter(fn ($value) => $value === true || $value === "1" || $value === 1)
                                    ->keys()
                                    ->toArray();

        $permissions = Permission::whereIn('id', $selectedPermissionIds)->get();
        $this->editingRole->syncPermissions($permissions);

        $this->logAdminActivity('updated_role_permissions', $this->editingRole, ['permission_ids' => $selectedPermissionIds]);
        session()->flash('message', 'Permissions updated for role ' . $this->editingRole->name);
        $this->cancelEditRolePermissions();
    }

     public function cancelEditRolePermissions()
    {
        $this->editingRole = null;
        $this->rolePermissions = [];
    }


    public function render()
    {
        $roles = Role::withCount('permissions')->paginate(10, ['*'], 'rolesPage');
        $permissions = Permission::orderBy('name')->paginate(10, ['*'], 'permissionsPage');
        $allPermissionsForModal = $this->editingRole ? Permission::orderBy('name')->get() : collect(); // Only load if modal is open

        return view('livewire.admin.roles-permissions', [
            'roles' => $roles,
            'permissions' => $permissions,
            'allPermissionsForModal' => $allPermissionsForModal
        ])->layout('layouts.admin');
    }
}
