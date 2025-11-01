<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment provider that will be used
    | when no provider is explicitly specified.
    |
    */

    'default' => env('PAYMENT_PROVIDER', 'trendipay'),

    /*
    |--------------------------------------------------------------------------
    | Payment Provider Credentials
    |--------------------------------------------------------------------------
    |
    | Here you may configure the credentials for each payment provider.
    | Add your API keys to your .env file for security.
    |
    */

    'trendipay' => [
        'merchant_external_id' => env('TRENDIPAY_MERCHANT_EXTERNAL_ID'),
        'terminal_external_id' => env('TRENDIPAY_TERMINAL_EXTERNAL_ID'),
        'base_url' => env('TRENDIPAY_BASE_URL', 'https://test-api.bsl.com.gh'),
    ],

    // Kept as reference for future providers
    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
    ],
];
