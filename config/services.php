<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */
    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'publishable_key' => env('SUPABASE_PUBLISHABLE_KEY'),
    ],


    'branch' => [
        'api_key' => env('BRANCH_API_KEY'),
        // Acceso exclusivo para desarrollo local
        'local_login_enabled' => env('BRANCH_LOCAL_LOGIN_ENABLED', false),
        'local_login_email' => env('BRANCH_LOCAL_LOGIN_EMAIL', ''),
        'local_login_password' => env('BRANCH_LOCAL_LOGIN_PASSWORD', ''),
    ],


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

    'banco_central' => [
        'url' => env('BANCO_CENTRAL_URL', 'http://127.0.0.1:8002'),
        'token' => env('BANCO_CENTRAL_TOKEN'),
    ],


];
