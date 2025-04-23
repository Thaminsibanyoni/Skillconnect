<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Cancelled') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                     <h3 class="text-lg font-medium text-gray-900">Payment Cancelled</h3>
                    <p class="mt-2 text-gray-600">
                        Your payment process was cancelled. You have not been charged.
                    </p>
                     <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-900">
                            Return to Dashboard
                        </a>
                         {{-- Optionally add link back to order/checkout --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
