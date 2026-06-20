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

    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
    ],

    'kilakona' => [
        'api_key'   => env('KILAKONA_API_KEY',    ''),
        'api_secret'=> env('KILAKONA_API_SECRET', ''),
        'sender_id' => env('KILAKONA_SENDER_ID',  'SOKONI'),
        'base_url'  => env('KILAKONA_BASE_URL',   ''),
    ],

    'palmpesa' => [
        'api_token'          => env('PALMPESA_API_TOKEN'),
        'user_id'            => env('PALMPESA_USER_ID'),
        'base_url'           => env('PALMPESA_BASE_URL', 'https://palmpesa.drmlelwa.co.tz'),
        'commission_percent' => env('PLATFORM_COMMISSION_PERCENT', 10),
    ],

];
