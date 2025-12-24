<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Outbound Messages Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for configuring the outbound messages system which handles
    | sending emails, SMS, and other notifications through a unified interface.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | These values are the default settings for the outbound messages system.
    | You can override these values in your .env file.
    |
    */

    'defaults' => [
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'name' => env('MAIL_FROM_NAME', 'Elderly Care'),
        ],
        'sms_from' => env('SMS_FROM_NUMBER', 'ElderCare'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the queue settings for processing outbound messages.
    |
    */

    'queue' => [
        'name' => env('OUTBOUND_QUEUE', 'messages'),
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'tries' => env('OUTBOUND_QUEUE_TRIES', 3),
        'retry_after' => env('OUTBOUND_QUEUE_RETRY_AFTER', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for outbound messages to prevent abuse.
    |
    */

    'rate_limiting' => [
        'enabled' => env('OUTBOUND_RATE_LIMITING_ENABLED', true),
        'max_attempts' => env('OUTBOUND_RATE_MAX_ATTEMPTS', 10),
        'decay_minutes' => env('OUTBOUND_RATE_DECAY_MINUTES', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Expiration
    |--------------------------------------------------------------------------
    |
    | Configure how long messages should be kept in the database.
    |
    */

    'retention_days' => [
        'sent' => env('OUTBOUND_RETENTION_SENT_DAYS', 90), // Keep sent messages for 90 days
        'failed' => env('OUTBOUND_RETENTION_FAILED_DAYS', 30), // Keep failed messages for 30 days
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the SMS provider settings.
    |
    */

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'), // log, twilio, aws, etc.
        'from' => env('SMS_FROM_NUMBER', 'ElderCare'),
        'test_mode' => env('SMS_TEST_MODE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the WhatsApp provider settings.
    |
    */

    'whatsapp' => [
        'enabled' => env('WHATSAPP_ENABLED', false),
        'driver' => env('WHATSAPP_DRIVER', 'log'), // log, twilio, etc.
        'from' => env('WHATSAPP_FROM_NUMBER'),
        'test_mode' => env('WHATSAPP_TEST_MODE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook settings for delivery status updates.
    |
    */

    'webhooks' => [
        'enabled' => env('OUTBOUND_WEBHOOKS_ENABLED', false),
        'secret' => env('OUTBOUND_WEBHOOK_SECRET'),
        'url' => env('OUTBOUND_WEBHOOK_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Templates
    |--------------------------------------------------------------------------
    |
    | Define default templates for common message types.
    |
    */

    'templates' => [
        'welcome' => 'emails.welcome',
        'password_reset' => 'emails.auth.password-reset',
        'donation_receipt' => 'emails.donations.receipt',
        'sponsor_update' => 'emails.sponsors.update',
    ],
];
