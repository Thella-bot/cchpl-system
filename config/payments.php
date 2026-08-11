<?php

return [
    // M-Pesa Daraja API Configuration
    'mpesa' => [
        'shortcode' => env('MPESA_SHORTCODE'),
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'passkey' => env('MPESA_PASSKEY'),
        'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),
        'callback_url' => env('APP_URL').'/api/v1/webhooks/mpesa',
    ],

    // EcoCash API Configuration
    'ecocash' => [
        'merchant_id' => env('ECOCASH_MERCHANT_ID'),
        'merchant_key' => env('ECOCASH_MERCHANT_KEY'),
        'environment' => env('ECOCASH_ENVIRONMENT', 'sandbox'),
        'callback_url' => env('APP_URL').'/api/v1/webhooks/ecocash',
    ],
];
