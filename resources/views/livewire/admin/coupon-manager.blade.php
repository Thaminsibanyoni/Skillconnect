<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Coupon Management') }}
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

        {{-- Button to open modal --}}
        <div class="mb-4">
            <x-button wire:click="create()">
                {{ __('Add New Coupon') }}
            </x-button>
        </div>

        {{-- Coupons Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Code</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Value</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Usage</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expires</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Active</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $coupon->code }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($coupon->type) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $coupon->type === 'percent' ? $coupon->value.'%' : '$'.number_format($coupon->value, 2) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $coupon->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                                    {{ $coupon->is_active ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="edit({{ $coupon->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                                <button wire:click="delete({{ $coupon->id }})" wire:confirm="Are you sure you want to delete this coupon?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">No coupons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $coupons->links() }}
        </div>
    </div>

     {{-- Create/Edit Modal --}}
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $couponId ? 'Edit Coupon' : 'Add New Coupon' }}
        </x-slot>

        <x-slot name="content">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 {{-- Code --}}
                 <div>
                    <x-label for="code" value="{{ __('Coupon Code') }}" class="dark:text-gray-300"/>
                    <div class="flex rounded-md shadow-sm">
                        <x-input id="code" type="text" class="mt-1 block w-full flex-1 rounded-none rounded-l-md" wire:model.defer="code" />
                        <button wire:click="generateCode" type="button" class="mt-1 relative -ml-px inline-flex items-center space-x-2 rounded-r-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Generate
                        </button>
                    </div>
                    <x-input-error for="code" class="mt-2" />
                </div>
                {{-- Type --}}
                <div>
                    <x-label for="type" value="{{ __('Type') }}" class="dark:text-gray-300"/>
                    <select id="type" wire:model.live="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="percent">Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                    <x-input-error for="type" class="mt-2" />
                </div>
                {{-- Value --}}
                <div>
                    <x-label for="value">
                         @if($type === 'percent')
                            {{ __('Percentage Value (%)') }}
                        @else
                            {{ __('Fixed Value ($)') }}
                        @endif
                    </x-label>
                    <x-input id="value" type="number" step="{{ $type === 'percent' ? '0.01' : '0.01' }}" class="mt-1 block w-full" wire:model.defer="value" />
                    <x-input-error for="value" class="mt-2" />
                </div>
                 {{-- Min Order Amount --}}
                <div>
                    <x-label for="min_order_amount" value="{{ __('Min. Order Amount (Optional)') }}" class="dark:text-gray-300"/>
                    <x-input id="min_order_amount" type="number" step="0.01" class="mt-1 block w-full" wire:model.defer="min_order_amount" placeholder="e.g., 50.00"/>
                    <x-input-error for="min_order_amount" class="mt-2" />
                </div>
                 {{-- Usage Limit --}}
                <div>
                    <x-label for="usage_limit" value="{{ __('Total Usage Limit (Optional)') }}" class="dark:text-gray-300"/>
                    <x-input id="usage_limit" type="number" step="1" class="mt-1 block w-full" wire:model.defer="usage_limit" placeholder="Leave blank for unlimited"/>
                    <x-input-error for="usage_limit" class="mt-2" />
                </div>
                 {{-- Usage Limit Per User --}}
                <div>
                    <x-label for="usage_limit_per_user" value="{{ __('Usage Limit Per User (Optional)') }}" class="dark:text-gray-300"/>
                    <x-input id="usage_limit_per_user" type="number" step="1" class="mt-1 block w-full" wire:model.defer="usage_limit_per_user" placeholder="Leave blank for unlimited"/>
                    <x-input-error for="usage_limit_per_user" class="mt-2" />
                </div>
                 {{-- Expires At --}}
                <div class="md:col-span-2">
                    <x-label for="expires_at" value="{{ __('Expires At (Optional)') }}" class="dark:text-gray-300"/>
                    <x-input id="expires_at" type="datetime-local" class="mt-1 block w-full" wire:model.defer="expires_at" />
                    <x-input-error for="expires_at" class="mt-2" />
                </div>
                 {{-- Is Active --}}
                 <div class="md:col-span-2 flex items-center mt-4">
                     <label for="is_active" class="flex items-center">
                        <x-checkbox id="is_active" wire:model.defer="is_active" />
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Active') }}</span>
                    </label>
                    <x-input-error for="is_active" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="save()" wire:loading.attr="disabled">
                {{ $couponId ? __('Update Coupon') : __('Save Coupon') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
