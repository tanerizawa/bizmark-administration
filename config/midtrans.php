<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Configure Midtrans payment gateway settings
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
    
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    
    // Notification URL for callback
    'notification_url' => env('APP_URL') . '/api/payment/callback',
    
    // Payment methods enabled
    'enabled_payments' => [
        'credit_card',
        'mandiri_clickpay',
        'cimb_clicks',
        'bca_klikbca',
        'bca_klikpay',
        'bri_epay',
        'echannel',
        'mandiri_ecash',
        'permata_va',
        'bca_va',
        'bni_va',
        'bri_va',
        'other_va',
        'gopay',
        'shopeepay',
        'indomaret',
        'alfamart',
        'akulaku',
        'kredivo',
    ],
    
    // Payment expiry time (in minutes)
    'expiry_duration' => env('MIDTRANS_EXPIRY_DURATION', 1440), // 24 hours
    
    // Default currency
    'currency' => 'IDR',
];
