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
        'rate' => env('COMMISSION_RATE', 15.00),      // Percentage value or fixed amount
    ],

    // Add other platform-specific settings here later

];
