<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env(
            'GOOGLE_REDIRECT_URI',
            'http://127.0.0.1:8000/auth/google/callback'
        ),
    ],

    'ghn' => [
        'token' => env('GHN_TOKEN'),
        'shop_id' => env('GHN_SHOP_ID'),
        'base_url' => env(
            'GHN_BASE_URL',
            'https://dev-online-gateway.ghn.vn'
        ),
    ],

    'payos' => [
        'client_id' => env('PAYOS_CLIENT_ID'),
        'api_key' => env('PAYOS_API_KEY'),
        'checksum_key' => env('PAYOS_CHECKSUM_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ONEPAY SANDBOX
    |--------------------------------------------------------------------------
    |
    | Không ghi khóa môi trường thật trực tiếp vào source code.
    |
    */
    'onepay' => [
        'payment_url' => env(
            'ONEPAY_PAYMENT_URL',
            'https://mtf.onepay.vn/paygate/vpcpay.op'
        ),
        'merchant_id' => env('ONEPAY_MERCHANT_ID'),
        'access_code' => env('ONEPAY_ACCESS_CODE'),
        'secure_secret' => env('ONEPAY_SECURE_SECRET'),
    ],

];
