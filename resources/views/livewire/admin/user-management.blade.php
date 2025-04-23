<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters and Search --}}
        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-label for="roleFilter" value="{{ __('Filter by Role') }}" />
                <select wire:model.live="roleFilter" id="roleFilter" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Roles</option>
                    <option value="seeker">Seeker</option>
                    <option value="provider">Provider</option>
                </select>
            </div>
            <div>
                <x-label for="statusFilter" value="{{ __('Filter by Status') }}" />
                <select wire:model.live="statusFilter" id="statusFilter" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div>
                <x-label for="search" value="{{ __('Search Name/Email') }}" />
                <x-input wire:model.live.debounce.300ms="search" id="search" class="block mt-1 w-full" type="text" placeholder="Search..." />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Registered At
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role === 'seeker' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($user->status === 'approved') bg-green-100 text-green-800 @endif
                                    @if($user->status === 'pending') bg-yellow-100 text-yellow-800 @endif
                                    @if($user->status === 'suspended') bg-red-100 text-red-800 @endif
                                ">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                {{-- Action Buttons --}}
                                @if($user->status !== 'approved')
                                    <button wire:click="approveUser({{ $user->id }})" wire:loading.attr="disabled" class="text-green-600 hover:text-green-900">Approve</button>
                                @endif
                                @if($user->status !== 'suspended')
                                    <button wire:click="suspendUser({{ $user->id }})" wire:loading.attr="disabled" class="text-red-600 hover:text-red-900">Suspend</button>
                                @endif
                                {{-- Add View/Edit buttons later --}}
                                {{-- <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-2">View</a> --}}
                                @if($user->role === 'provider')
                                    <button wire:click="manageAreas({{ $user->id }})" class="text-purple-600 hover:text-purple-900">Areas</button>
                                @endif
                                {{-- Add Roles Button --}}
                                <button wire:click="showRoleModal({{ $user->id }})" class="text-cyan-600 hover:text-cyan-900 ml-2">Roles</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }} {{-- Pagination links --}}
        </div>
    </div>

    {{-- Service Areas Modal --}}
    <x-dialog-modal wire:model.live="showAreasModal">
        <x-slot name="title">
            Manage Service Areas for {{ $editingUser?->name }}
        </x-slot>

        <x-slot name="content">
            @if($editingUser)
                <div class="text-sm text-gray-600 mb-4">Select the cities where this provider is approved to operate.</div>
                <div class="max-h-96 overflow-y-auto space-y-4">
                    @forelse ($allProvinces as $province)
                        <div class="py-2">
                             <h4 class="font-semibold text-gray-700 mb-1">{{ $province->name }}</h4>
                             <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pl-4">
                                @foreach($allCities->where('province_id', $province->id) as $city)
                                    <label for="city_{{ $city->id }}" class="flex items-center">
                                        <input id="city_{{ $city->id }}"
                                               wire:model="assignedCityIds.{{ $city->id }}"
                                               value="{{ $city->id }}"
                                               type="checkbox"
                                               class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <span class="ml-2 text-sm text-gray-600">{{ $city->name }}</span>
                                    </label>
                                @endforeach
                             </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No provinces or cities found. Please add them via Geography Management.</p>
                    @endforelse
                </div>
            @else
                 <p>Loading user data...</p>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeAreasModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="saveAreas()" wire:loading.attr="disabled">
                {{ __('Save Areas') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Assign Roles Modal --}}
    <x-dialog-modal wire:model.live="showRoleModal">
        <x-slot name="title">
            Manage Roles for {{ $editingUser?->name }}
        </x-slot>

        <x-slot name="content">
             <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($allRoles as $role)
                    <div class="flex items-center">
                        <input id="role_{{ $role->id }}" type="checkbox"
                               wire:model.defer="assignedRoles.{{ $role->id }}"
                               value="{{ $role->name }}"
                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ $role->name }}</label>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 col-span-full">No roles defined yet.</p>
                @endforelse
            </div>
             <x-input-error for="assignedRoles" class="mt-2" />
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeRoleModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="saveRoles()" wire:loading.attr="disabled">
                {{ __('Save Roles') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
