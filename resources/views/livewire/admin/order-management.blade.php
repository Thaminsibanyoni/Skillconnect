<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Management') }}
        </h2>
    </x-slot>

    <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
        {{-- Flash Messages --}}
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        {{-- Filters and Search --}}
        <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-label for="statusFilter" value="{{ __('Filter by Status') }}" />
                <select wire:model.live="statusFilter" id="statusFilter" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <x-label for="search" value="{{ __('Search (ID, Seeker, Provider, Service)') }}" />
                <x-input wire:model.live.debounce.300ms="search" id="search" class="block mt-1 w-full" type="text" placeholder="Search..." />
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seeker</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->seeker->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->provider->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->service->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                 <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($order->status === 'completed') bg-green-100 text-green-800 @endif
                                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800 @endif
                                    @if($order->status === 'accepted') bg-blue-100 text-blue-800 @endif
                                    @if($order->status === 'in_progress') bg-purple-100 text-purple-800 @endif
                                    @if($order->status === 'rejected' || $order->status === 'cancelled') bg-red-100 text-red-800 @endif
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->total_amount ? '$'.number_format($order->total_amount, 2) : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->scheduled_at ? $order->scheduled_at->format('Y-m-d H:i') : 'Now' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->commission_amount ? '$'.number_format($order->commission_amount, 2) : '-' }}
                                @if($order->commission_rate && $order->commission_amount)
                                    <span class="text-xs text-gray-400">({{ $order->commission_rate }}%)</span> {{-- Assuming percentage for now --}}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">View</a> {{-- TODO: Link to order details --}}
                                @if($order->status === 'pending' && !$order->provider_id)
                                    <button wire:click="showAssignModal({{ $order->id }})" class="text-blue-600 hover:text-blue-900">Assign</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>

    {{-- Assign Provider Modal --}}
    <x-dialog-modal wire:model.live="showAssignModal">
        <x-slot name="title">
            Assign Provider to Order #{{ $assigningOrderId }}
        </x-slot>

        <x-slot name="content">
            @if($assigningOrder)
                <p class="mb-2 text-sm">Service: <strong>{{ $assigningOrder->service?->name }}</strong></p>
                <p class="mb-4 text-sm">Location: <strong>{{ $assigningOrder->city?->name ?? 'N/A' }}</strong> ({{ $assigningOrder->address }})</p>
            @endif

            <div class="mt-4">
                <x-label for="selectedProviderId" value="{{ __('Select Available Provider') }}" />
                <select id="selectedProviderId" wire:model="selectedProviderId" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">-- Select Provider --</option>
                    @forelse($availableProviders as $provider)
                        <option value="{{ $provider->id }}">{{ $provider->name }} (ID: {{ $provider->id }})</option>
                    @empty
                         <option value="" disabled>No suitable online providers found</option>
                    @endforelse
                </select>
                <x-input-error for="selectedProviderId" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeAssignModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-button class="ms-3" wire:click="assignProvider()" wire:loading.attr="disabled" wire:target="assignProvider">
                {{ __('Assign Provider') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

</div>
