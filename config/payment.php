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

    'default' => env('PAYMENT_PROVIDER', 'paystack'),

    /*
    |--------------------------------------------------------------------------
    | Payment Provider Credentials
    |--------------------------------------------------------------------------
    |
    | Here you may configure the credentials for each payment provider.
    | Add your API keys to your .env file for security.
    |
    */

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
    ],

    'stripe' => [
        'public_key' => env('STRIPE_PUBLIC_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
    ],

    'flutterwave' => [
        'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
        'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
    ],
];
