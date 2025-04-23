<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscription Plan Management') }}
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

        {{-- Button to open modal --}}
        <div class="mb-4">
            <x-button wire:click="createPlan()">
                {{ __('Add New Plan') }}
            </x-button>
        </div>

        {{-- Plans Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interval</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Cities</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($plans as $plan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $plan->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->slug }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $plan->currency }} {{ number_format($plan->price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->interval_count }} {{ Str::plural($plan->interval, $plan->interval_count) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->max_cities ?? 'Unlimited' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $plan->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="editPlan({{ $plan->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="deletePlan({{ $plan->id }})" wire:confirm="Are you sure? This cannot be undone." class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No subscription plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $plans->links() }}
        </div>
    </div>

     {{-- Create/Edit Plan Modal --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $planId ? 'Edit Subscription Plan' : 'Add New Subscription Plan' }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Name --}}
                <div class="mt-4">
                    <x-label for="name" value="{{ __('Plan Name') }}" />
                    <x-input id="name" type="text" class="mt-1 block w-full" wire:model.live.debounce.500ms="name" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                 {{-- Slug --}}
                <div class="mt-4">
                    <x-label for="slug" value="{{ __('Slug (Identifier)') }}" />
                    <x-input id="slug" type="text" class="mt-1 block w-full" wire:model.defer="slug" />
                    <x-input-error for="slug" class="mt-2" />
                </div>
                 {{-- Price --}}
                <div class="mt-4">
                    <x-label for="price" value="{{ __('Price') }}" />
                    <x-input id="price" type="number" step="0.01" class="mt-1 block w-full" wire:model.defer="price" />
                    <x-input-error for="price" class="mt-2" />
                </div>
                 {{-- Currency --}}
                <div class="mt-4">
                    <x-label for="currency" value="{{ __('Currency (e.g., ZAR)') }}" />
                    <x-input id="currency" type="text" maxlength="3" class="mt-1 block w-full" wire:model.defer="currency" />
                    <x-input-error for="currency" class="mt-2" />
                </div>
                 {{-- Interval --}}
                <div class="mt-4">
                    <x-label for="interval" value="{{ __('Billing Interval') }}" />
                    <select id="interval" wire:model.defer="interval" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="day">Day</option>
                        <option value="week">Week</option>
                        <option value="month">Month</option>
                        <option value="year">Year</option>
                    </select>
                    <x-input-error for="interval" class="mt-2" />
                </div>
                 {{-- Interval Count --}}
                <div class="mt-4">
                    <x-label for="interval_count" value="{{ __('Interval Count') }}" />
                    <x-input id="interval_count" type="number" step="1" min="1" class="mt-1 block w-full" wire:model.defer="interval_count" />
                    <x-input-error for="interval_count" class="mt-2" />
                </div>
                 {{-- Max Cities --}}
                <div class="mt-4">
                    <x-label for="max_cities" value="{{ __('Max Cities (Leave blank for unlimited)') }}" />
                    <x-input id="max_cities" type="number" step="1" min="0" class="mt-1 block w-full" wire:model.defer="max_cities" />
                    <x-input-error for="max_cities" class="mt-2" />
                </div>
                 {{-- Active Status --}}
                <div class="mt-4">
                    <x-label for="is_active" value="{{ __('Active') }}" />
                     <select id="is_active" wire:model.defer="is_active" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                    <x-input-error for="is_active" class="mt-2" />
                </div>
                 {{-- Description --}}
                <div class="mt-4 md:col-span-2">
                    <x-label for="description" value="{{ __('Description') }}" />
                    <textarea id="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="description" rows="3"></textarea>
                    <x-input-error for="description" class="mt-2" />
                </div>
                 {{-- Features --}}
                <div class="mt-4 md:col-span-2">
                    <x-label for="features_input" value="{{ __('Features (Comma-separated)') }}" />
                    <textarea id="features_input" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="features_input" rows="3"></textarea>
                    <x-input-error for="features_input" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="savePlan()" wire:loading.attr="disabled">
                {{ $planId ? __('Update Plan') : __('Save Plan') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
