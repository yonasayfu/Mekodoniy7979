<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'telebirr' => [
        'app_id' => env('TELEBIRR_APP_ID'),
        'app_key' => env('TELEBIRR_APP_KEY'),
        'public_key' => env('TELEBIRR_PUBLIC_KEY'),
        'merchant_id' => env('TELEBIRR_MERCHANT_ID'),
        'merchant_private_key' => env('TELEBIRR_MERCHANT_PRIVATE_KEY'),
        'notify_url' => env('TELEBIRR_NOTIFY_URL'),
        'return_url' => env('TELEBIRR_RETURN_URL'),
        'base_url' => env('TELEBIRR_BASE_URL', 'https://app.telebirr.com/api/'),
        'webhook_token' => env('TELEBIRR_WEBHOOK_TOKEN'),
        'simulate' => env('TELEBIRR_SIMULATE', true),
    ],

    'telebirr_recurring' => [
        'app_id' => env('TELEBIRR_RECURRING_APP_ID'),
        'app_key' => env('TELEBIRR_RECURRING_APP_KEY'),
        'merchant_id' => env('TELEBIRR_RECURRING_MERCHANT_ID'),
        'notify_url' => env('TELEBIRR_RECURRING_NOTIFY_URL'),
        'base_url' => env('TELEBIRR_RECURRING_BASE_URL', 'https://app.telebirr.com/api/'),
        'subscribe_endpoint' => env('TELEBIRR_RECURRING_SUBSCRIBE_ENDPOINT', 'autopay/subscribe'),
        'charge_endpoint' => env('TELEBIRR_RECURRING_CHARGE_ENDPOINT', 'autopay/charge'),
        'cancel_endpoint' => env('TELEBIRR_RECURRING_CANCEL_ENDPOINT', 'autopay/cancel'),
        'simulate' => env('TELEBIRR_RECURRING_SIMULATE', true),
    ],

    'cbe_recurring' => [
        'api_url' => env('CBE_RECURRING_API_URL'),
        'api_key' => env('CBE_RECURRING_API_KEY'),
        'subscribe_endpoint' => env('CBE_RECURRING_SUBSCRIBE_ENDPOINT', 'subscriptions'),
        'charge_endpoint' => env('CBE_RECURRING_CHARGE_ENDPOINT', 'charges'),
        'cancel_endpoint' => env('CBE_RECURRING_CANCEL_ENDPOINT', 'subscriptions/cancel'),
        'simulate' => env('CBE_RECURRING_SIMULATE', true),
    ],

];
