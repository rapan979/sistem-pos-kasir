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

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-vW7qdP51IaneD6x-rRQEW_fV'),
        'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    ],

    'qris' => [
        'mode' => env('QRIS_MODE', 'merchant'),
        'merchant_name' => env('QRIS_MERCHANT_NAME', 'Es Teh Perkasa'),
        'merchant_city' => env('QRIS_MERCHANT_CITY', 'SIDOARJO'),
        'base_string' => env('QRIS_BASE_STRING', '00020101021126610014COM.GO-JEK.WWW01189360091437746865670210G7746865670303UMI51440014ID.CO.QRIS.WWW0215ID10265601449370303UMI5204899953033605802ID5925RAKHA RAQILLA SYIHAB, Dig6008SIDOARJO61056125862070703A016304C36F'),
    ],

];
