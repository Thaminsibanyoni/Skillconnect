<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Commission Settings
    |--------------------------------------------------------------------------
    |
    | Configure the default commission structure for the platform.
    | 'type' can be 'percentage' or 'fixed'.
    | 'rate' is the percentage value (e.g., 15 for 15%) or the fixed amount.
    |
    */
    'commission' => [
        'type' => env('COMMISSION_TYPE', 'percentage'), // 'percentage' or 'fixed'
        'rate' => env('COMMISSION_RATE', 15.00),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans (Stripe Price IDs)
    |--------------------------------------------------------------------------
    |
    | Define the Stripe Price IDs for your subscription plans.
    | Add details like allowed service areas per plan.
    | Keys should be simple identifiers (e.g., 'basic', 'premium').
    | Values for 'price_id' MUST match Price IDs created in your Stripe dashboard.
    |
    */
    'subscriptions' => [
        'basic' => [
            'name' => 'Basic Provider',
            'price_id' => env('STRIPE_BASIC_PLAN_PRICE_ID', 'price_basic_test'), // Replace with actual Stripe Price ID
            'max_cities' => env('SUBSCRIPTION_BASIC_MAX_CITIES', 3),
            'features' => ['Standard Listing', 'Up to 3 Cities'],
        ],
        'premium' => [
            'name' => 'Premium Provider',
            'price_id' => env('STRIPE_PREMIUM_PLAN_PRICE_ID', 'price_premium_test'), // Replace with actual Stripe Price ID
            'max_cities' => env('SUBSCRIPTION_PREMIUM_MAX_CITIES', 10), // Or null for unlimited?
            'features' => ['Featured Listing', 'Up to 10 Cities', 'Lower Commission?'], // Example features
        ],
        // Add more plans as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider Settings
    |--------------------------------------------------------------------------
    */
    'provider_search_radius' => env('PROVIDER_SEARCH_RADIUS_KM', 10), // Default search radius in KM
    'max_providers_notify' => env('MAX_PROVIDERS_NOTIFY', 10), // Max providers to notify for one order

    // Add other platform-specific settings here later

];
