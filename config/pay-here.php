<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Base Url
    |--------------------------------------------------------------------------
    |
    | This is where you can configure PayHere base url, based on the
    | application environment. On production, the value should set to
    | https://www.payhere.lk/
    |
    */

    'base_url' => env('PAY_HERE_BASE_URL', 'https://sandbox.payhere.lk/'),

    /*
    |--------------------------------------------------------------------------
    | Merchant Credentials
    |--------------------------------------------------------------------------
    |
    | Merchant ID is issued per account by PayHere, You can copy it from your
    | PayHere settings page in the Domains & Credentials tab.
    |
    | To obtain a merchant secret, you need to add a domain to allowed
    | domains/apps.
    |
    */

    'merchant_credentials' => [
        'id' => env('PAY_HERE_MERCHANT_ID'),
        'secret' => env('PAY_HERE_MERCHANT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Business App Credentials
    |--------------------------------------------------------------------------
    |
    | You must create a business application to call PayHere services. Visit
    | the link bellow and follow the instruction to obtain business app id
    | and a secret.
    |
    | NOTE: Tick the following permission
    | - Payment Retrieval API
    | - Subscription Management API
    |
    */

    'business_app_credentials' => [
        'id' => env('PAY_HERE_BUSINESS_APP_ID'),
        'secret' => env('PAY_HERE_BUSINESS_APP_SECRET'),
    ],

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
    | PayHere Middleware
    |--------------------------------------------------------------------------
    |
    | This is the middleware group that PayHere payment notification webhook
    | and redirect on success/canceled routes uses.
    |
    */

    'middleware' => env('PAY_HERE_MIDDLEWARE', []),
];
