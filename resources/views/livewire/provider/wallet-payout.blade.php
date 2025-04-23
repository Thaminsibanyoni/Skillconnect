<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Wallet & Payouts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Balance & Request Form --}}
            <div class="md:col-span-1">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Current Balance</h3>
                    <p class="text-3xl font-semibold text-gray-900">${{ number_format($balance, 2) }}</p>

                    <hr class="my-6">

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Request Payout</h3>

                    @if (session()->has('message'))
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($pendingPayoutExists)
                         <p class="text-sm text-yellow-700 bg-yellow-100 p-3 rounded">You have a payout request currently pending review.</p>
                    @else
                        <form wire:submit.prevent="requestPayout">
                            <div>
                                <x-label for="payoutAmount" value="{{ __('Amount to Withdraw') }}" />
                                <x-input id="payoutAmount" type="number" step="0.01" class="mt-1 block w-full" wire:model.defer="payoutAmount" />
                                <x-input-error for="payoutAmount" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">Minimum $1.00. Max ${{ number_format($balance, 2) }}</p>
                            </div>

                            {{-- TODO: Add field for payout method details (e.g., bank account) --}}
                            {{-- This requires adding fields to user profile or a separate payout_methods table --}}
                            <div class="mt-4 text-sm text-gray-600">
                                Payout method selection/details will be added here.
                            </div>

                            <div class="mt-6">
                                <x-button type="submit" wire:loading.attr="disabled" wire:target="requestPayout" :disabled="$balance <= 0">
                                    {{ __('Request Payout') }}
                                </x-button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Payout History --}}
            <div class="md:col-span-2">
                 <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                     <h3 class="text-lg font-medium text-gray-900 mb-4">Payout History</h3>
                     <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($payoutHistory as $request)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('Y-m-d') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($request->amount, 2) }}</td>
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
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->processed_at ? $request->processed_at->format('Y-m-d H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No payout requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                     {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $payoutHistory->links(data: ['scrollTo' => false]) }} {{-- Disable scroll on pagination --}}
                    </div>
                 </div>
            </div>

        </div>
    </div>
</div>
