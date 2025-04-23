<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Subscription') }}
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

        {{-- Current Subscription Status --}}
        <div class="mb-6 p-4 border rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Current Subscription</h3>
            @if ($currentPlan)
                <p>You are currently subscribed to the <strong>{{ $currentPlan->name }}</strong> plan.</p>
                <p class="text-sm text-gray-600">Status: <span class="font-semibold">{{ ucfirst($user->subscription_status) }}</span></p>
                @if($user->subscription_expires_at)
                    <p class="text-sm text-gray-600">
                        {{ $user->subscription_status === 'cancelled' ? 'Expires on:' : 'Renews on:' }}
                        {{ $user->subscription_expires_at->format('F j, Y') }}
                    </p>
                @endif
                {{-- Add Cancel Button --}}
                @if($user->subscription_status === 'active')
                    <div class="mt-4">
                        <x-danger-button wire:click="cancelSubscription" wire:confirm="Are you sure you want to cancel your subscription?">
                            Cancel Subscription
                        </x-danger-button>
                    </div>
                @endif
                 {{-- TODO: Add button to change plan (might redirect to plans list or use gateway portal if available) --}}

            @else
                 <p class="text-gray-600">You do not have an active subscription.</p>
            @endif
        </div>

        {{-- Available Plans (Only show if no active subscription) --}}
        @if (!$currentPlan)
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Choose a Plan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($plans as $plan)
                        <div class="border rounded-lg p-4 flex flex-col justify-between shadow-md">
                            <div>
                                <h4 class="text-xl font-semibold">{{ $plan->name }}</h4>
                                <p class="text-2xl font-bold my-2">{{ $plan->currency }} {{ number_format($plan->price, 2) }} <span class="text-sm font-normal text-gray-500">/ {{ $plan->interval }}</span></p>
                                <p class="text-sm text-gray-600 mb-3">{{ $plan->description }}</p>
                                <ul class="mt-2 text-sm text-gray-600 list-disc pl-5 space-y-1 flex-grow mb-4">
                                    @foreach($plan->features ?? [] as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                     <li>{{ $plan->max_cities ? 'Up to '.$plan->max_cities.' cities' : 'Unlimited cities' }}</li>
                                </ul>
                            </div>
                            <div class="mt-4">
                                {{-- Buttons to initiate payment via different gateways --}}
                                {{-- These need an order/reference ID. For subscriptions, we might need a different flow --}}
                                {{-- or create a temporary 'subscription_order' --}}
                                <p class="text-xs text-gray-500 mb-2">Choose payment method:</p>
                                <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2">
                                     {{-- Link to specific gateway routes for this plan --}}
                                     <a href="{{ route('subscription.payment.payfast', ['plan' => $plan->slug]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        PayFast
                                     </a>
                                     <a href="{{ route('subscription.payment.flutterwave', ['plan' => $plan->slug]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Flutterwave
                                     </a>
                                     <a href="{{ route('subscription.payment.paypal', ['plan' => $plan->slug]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-900 focus:bg-blue-900 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        PayPal
                                     </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No subscription plans available.</p>
                    @endforelse
                </div>
            </div>
        @endif

    </div>
</div>
