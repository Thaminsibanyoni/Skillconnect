<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pay for Order #') }}{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">

                    <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                        <p><strong>Service:</strong> {{ $order->service->name ?? 'N/A' }}</p>
                        <p><strong>Provider:</strong> {{ $order->provider->name ?? 'Pending Assignment' }}</p>
                        <p><strong>Date:</strong> {{ $order->scheduled_at ? $order->scheduled_at->format('Y-m-d H:i') : $order->created_at->format('Y-m-d H:i') }}</p>
                        <p><strong>Address:</strong> {{ $order->address ?? 'N/A' }}</p>
                        <p class="text-lg font-semibold text-gray-900"><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
                    </div>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Choose Payment Method</h3>
                        <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
                             {{-- Link to specific gateway routes for this order --}}
                             <a href="{{ route('payment.payfast.pay', $order) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Pay with PayFast
                             </a>
                             <a href="{{ route('payment.flutterwave.pay', $order) }}" class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Pay with Flutterwave
                             </a>
                             <a href="{{ route('payment.paypal.pay', $order) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-900 focus:bg-blue-900 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Pay with PayPal
                             </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
