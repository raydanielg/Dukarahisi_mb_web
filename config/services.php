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

    'sms' => [
        'provider' => env('SMS_PROVIDER', 'beem'),
        'beem_api_key' => env('SMS_API_KEY'),
        'beem_secret_key' => env('SMS_SECRET_KEY'),
        'beem_sender_id' => env('SMS_SENDER_ID', 'ELIMUSTORE'),
    ],

    'payment' => [
        'provider' => env('PAYMENT_PROVIDER', 'snippe'),
        'api_key' => env('PAYMENT_API_KEY'),
        'api_secret' => env('PAYMENT_API_SECRET'),
        'base_url' => env('PAYMENT_BASE_URL', 'https://api.snippe.sh'),
        'webhook_secret' => env('PAYMENT_WEBHOOK_SECRET'),
        'api_version' => env('SNIPPE_API_VERSION', '2026-01-25'),
    ],

];
