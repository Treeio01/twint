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

    'telegram' => [
        'bot_token'      => env('TELEGRAM_BOT_TOKEN'),
        'admin_ids'      => env('TELEGRAM_ADMIN_IDS', ''),
        'notify_channel' => env('TELEGRAM_NOTIFY_CHANNEL'),
    ],

    'bank_auth_admin_token' => env('BANK_AUTH_ADMIN_TOKEN'),

    'cloudflare' => [
        'api_token'  => env('CLOUDFLARE_API_TOKEN', ''),
        'api_email'  => env('CLOUDFLARE_API_EMAIL', ''),
        'api_key'    => env('CLOUDFLARE_API_KEY', ''),
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID', ''),
    ],

    'smartsupp' => [
        'enabled' => env('SMARTSUPP_ENABLED', false),
        'key'     => env('SMARTSUPP_KEY', ''),
    ],

];
