<?php

return [
    'default' => env('PAYMENT_GATEWAY', 'mustika'),

    'mustika' => [
        'base_url' => env('MUSTIKA_BASE_URL', 'https://mustikapayment.com'),
        'api_key' => env('MUSTIKA_API_KEY'),
        'merchant_id' => env('MUSTIKA_MERCHANT_ID'),
        'callback_secret' => env('MUSTIKA_CALLBACK_SECRET'),
        'callback_url' => env('MUSTIKA_CALLBACK_URL', env('APP_URL').'/payment/webhook/mustika'),
        'return_url' => env('MUSTIKA_RETURN_URL', env('APP_URL').'/payment/success'),
        'cancel_url' => env('MUSTIKA_CANCEL_URL', env('APP_URL').'/payment/failed'),
        'environment' => env('MUSTIKA_ENV', 'sandbox'),
        'timeout' => (int) env('MUSTIKA_TIMEOUT', 30),
        'resolved_ip' => env('MUSTIKA_RESOLVED_IP'),
    ],
];
