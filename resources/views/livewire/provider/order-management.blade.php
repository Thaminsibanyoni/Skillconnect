<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Service Requests') }}
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

        {{-- Filters --}}
        <div class="mb-4">
             <x-label for="statusFilter" value="{{ __('Filter by Status') }}" />
             <select wire:model.live="statusFilter" id="statusFilter" class="block mt-1 w-full md:w-1/3 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                 <option value="">All</option>
                 <option value="pending">Pending</option>
                 <option value="accepted">Accepted</option>
                 <option value="in_progress">In Progress</option>
                 <option value="completed">Completed</option>
                 <option value="cancelled">Cancelled</option>
                 <option value="rejected">Rejected</option>
             </select>
        </div>

        {{-- Orders Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seeker</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->seeker->name ?? 'N/A' }}</td>
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
                             <td class="px-6 py-4 text-sm text-gray-500">{{ $order->address }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->scheduled_at ? $order->scheduled_at->format('Y-m-d H:i') : 'Now' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($order->status === 'pending')
                                    <button wire:click="acceptOrder({{ $order->id }})" wire:loading.attr="disabled" class="text-green-600 hover:text-green-900">Accept</button>
                                    <button wire:click="rejectOrder({{ $order->id }})" wire:loading.attr="disabled" class="text-red-600 hover:text-red-900">Reject</button>
                                @elseif($order->status === 'accepted')
                                     <button wire:click="rejectOrder({{ $order->id }})" wire:loading.attr="disabled" class="text-red-600 hover:text-red-900">Cancel Acceptance</button>
                                     <button wire:click="startJob({{ $order->id }})" wire:loading.attr="disabled" class="text-blue-600 hover:text-blue-900">Start Job</button>
                                @elseif($order->status === 'in_progress')
                                     <button wire:click="completeJob({{ $order->id }})" wire:loading.attr="disabled" class="text-green-600 hover:text-green-900">Complete Job</button>
                                @else
                                    <span class="text-xs text-gray-400">No actions available</span>
                                @endif
                                {{-- Add View Details button later --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No orders found matching the criteria.</td>
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
</div>
