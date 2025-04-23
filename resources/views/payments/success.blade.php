<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Thank You!</h3>
                    <p class="mt-2 text-gray-600">
                        Your payment was successful. We are processing your order. You will receive updates shortly.
                        (Note: Order status is confirmed via server-to-server notification).
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-900">
                            Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
