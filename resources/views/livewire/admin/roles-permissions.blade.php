<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Roles & Permissions Management') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-100 rounded">
                {{ session('message') }}
            </div>
        @endif
         @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-100 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Create Role/Permission Forms --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Create New</h3>
                <div class="space-y-4">
                    {{-- Create Role --}}
                    <form wire:submit.prevent="createRole" class="flex items-end space-x-2">
                        <div class="flex-grow">
                            <x-label for="newRoleName" value="{{ __('New Role Name') }}" class="dark:text-gray-300"/>
                            <x-input id="newRoleName" type="text" class="mt-1 block w-full" wire:model.defer="newRoleName" />
                            <x-input-error for="newRoleName" class="mt-2" />
                        </div>
                        <x-button type="submit">Create Role</x-button>
                    </form>
                     {{-- Create Permission --}}
                    <form wire:submit.prevent="createPermission" class="flex items-end space-x-2">
                         <div class="flex-grow">
                            <x-label for="newPermissionName" value="{{ __('New Permission Name') }}" class="dark:text-gray-300"/>
                            <x-input id="newPermissionName" type="text" class="mt-1 block w-full" wire:model.defer="newPermissionName" />
                            <x-input-error for="newPermissionName" class="mt-2" />
                        </div>
                        <x-button type="submit">Create Permission</x-button>
                    </form>
                </div>
            </div>

            {{-- Assign Permissions to Users (Placeholder/Separate Component Recommended) --}}
            {{-- <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Assign Roles to User</h3>
                </div> --}}
        </div>

        <hr class="my-8 dark:border-gray-700">

        {{-- Roles List --}}
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Roles</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Guard</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Permissions Count</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($roles as $role)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $role->name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $role->guard_name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $role->permissions_count }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="editRolePermissions({{ $role->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit Permissions</button>
                                    {{-- Delete Role button (add confirmation) --}}
                                    {{-- <button wire:click="deleteRole({{ $role->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button> --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $roles->links(data: ['scrollTo' => false], pageName: 'rolesPage') }}
            </div>
        </div>

        <hr class="my-8 dark:border-gray-700">

        {{-- Permissions List --}}
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Permissions</h3>
             <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Guard</th>
                            {{-- <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th> --}}
                        </tr>
                    </thead>
                     <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($permissions as $permission)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $permission->name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $permission->guard_name }}</td>
                                {{-- <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="deletePermission({{ $permission->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="mt-4">
                {{ $permissions->links(data: ['scrollTo' => false], pageName: 'permissionsPage') }}
            </div>
        </div>

    </div>

     {{-- Edit Role Permissions Modal --}}
    <x-dialog-modal wire:model.live="editingRole">
        <x-slot name="title">
            Edit Permissions for Role: {{ $editingRole?->name }}
        </x-slot>

        <x-slot name="content">
             <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                @forelse($allPermissionsForModal as $permission)
                    <div class="flex items-center">
                        <input id="perm_{{ $permission->id }}" type="checkbox"
                               wire:model.defer="rolePermissions.{{ $permission->id }}"
                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <label for="perm_{{ $permission->id }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $permission->name }}</label>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 col-span-full">No permissions defined yet.</p>
                @endforelse
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="cancelEditRolePermissions()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="saveRolePermissions()" wire:loading.attr="disabled">
                {{ __('Save Permissions') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
