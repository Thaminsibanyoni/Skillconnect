<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Payout Requests') }}
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
            <select wire:model.live="statusFilter" id="statusFilter" class="block mt-1 w-full md:w-1/4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="processed">Processed</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        {{-- Requests Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($requests as $request)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->id }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $request->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">${{ number_format($request->amount, 2) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($request->status === 'processed') bg-green-100 text-green-800 @endif
                                    @if($request->status === 'pending') bg-yellow-100 text-yellow-800 @endif
                                    @if($request->status === 'approved') bg-blue-100 text-blue-800 @endif
                                    @if($request->status === 'rejected') bg-red-100 text-red-800 @endif
                                ">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->processed_at ? $request->processed_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="px-4 py-4 text-sm text-gray-500">{{ $request->notes ?? '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                @if($request->status === 'pending')
                                    <button wire:click="approveRequest({{ $request->id }})" class="text-green-600 hover:text-green-900">Approve</button>
                                    <button wire:click="showRejectModal({{ $request->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                                @elseif($request->status === 'approved')
                                     <button wire:click="markAsProcessed({{ $request->id }})" wire:confirm="This will deduct ${{ number_format($request->amount, 2) }} from the provider's wallet and mark as processed. Are you sure?" class="text-purple-600 hover:text-purple-900">Mark Processed</button>
                                @else
                                    {{-- No actions for processed/rejected --}}
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No payout requests found matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>

     {{-- Reject Payout Modal --}}
    <x-dialog-modal wire:model.live="showRejectModal">
        <x-slot name="title">
            Reject Payout Request #{{ $rejectingRequestId }}
        </x-slot>

        <x-slot name="content">
            <p class="mb-4 text-sm text-gray-600">Please provide a reason for rejecting this payout request.</p>
            <div>
                <x-label for="rejectReason" value="{{ __('Rejection Reason') }}" />
                <textarea id="rejectReason" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="rejectReason" rows="3"></textarea>
                <x-input-error for="rejectReason" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeRejectModal()" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="rejectRequest()" wire:loading.attr="disabled">
                {{ __('Reject Request') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>

</div>
