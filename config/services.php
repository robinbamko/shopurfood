<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */


    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id' => '312352149389737',
        'client_secret' => '90aa002474423588ccf91e75ea0298a1',
        'redirect' => 'https://shopurfood.mytaxisoft.com/auth/facebook/callback',
    ],
    'google' => [
        'client_id' => '469869848031-t80rtl17odtkg1r8es0i1ikb21n15c7t.apps.googleusercontent.com',
        'client_secret' => '3eYyQEA8Hg0DfocPXuFDsM0n',
        'redirect' => 'https://shopurfood.mytaxisoft.com/auth/google/callback',

    ],
	

];
