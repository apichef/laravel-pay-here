<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayHere Database Connection
    |--------------------------------------------------------------------------
    |
    | This is the database connection you want PayHere to use while storing &
    | reading your payment data. By default PayHere assumes you use your
    | default connection. However, you can change that to anything you want.
    |
    */

    'database_connection' => env('DB_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Merchant Credentials
    |--------------------------------------------------------------------------
    |
    | Merchant ID is issued per account on PayHere, You can copy it from your
    | PayHere settings page in Domains & Credentials.
    |
    | To obtain a merchant secret, you need to add a domain to allowed
    | domains/apps.
    |
    */
    'credentials' => [
        'merchant_id' => env('PAY_HERE_MERCHANT_ID'),
        'merchant_secret' => env('PAY_HERE_MERCHANT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PayHere Middleware Group
    |--------------------------------------------------------------------------
    |
    | This is the middleware group that PayHere payment notification webhook
    | uses.
    |
    */

    'middleware_group' => env('PAY_HERE_MIDDLEWARE_GROUP', 'web'),
];
