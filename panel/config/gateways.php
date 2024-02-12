<?php

/*
|--------------------------------------------------------------------------
| Jexactyl Gateways
|--------------------------------------------------------------------------
| This configuration file is used to determine the settings for Jexactyl's
| payment gateways (Stripe and PayPal by default).
*/

return [
    /*
    |--------------------------------------------------------------------------
    | Preferred Currency
    |--------------------------------------------------------------------------
    | This value determines what currency to process orders in.
    |
    */
    'currency' => env('STORE_CURRENCY', 'RUB'),

    /*
    |--------------------------------------------------------------------------
    | Cost per 100 credits
    |--------------------------------------------------------------------------
    | This value determines how much 100 credits costs. Defaults to 100 RUB.
    |
    */
    'cost' => env('STORE_COST', 100),

    /*
    |--------------------------------------------------------------------------
    | PayPal Configuration
    |--------------------------------------------------------------------------
    | These values determine the configuration for the PayPal purchase system.
    |
    */
    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    | These values determine the configuration for the Stripe purchase system.
    |
    */
    'stripe' => [
        'secret' => env('STRIPE_CLIENT_SECRET', ''),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
    ],
    /*
    |--------------------------------------------------------------------------
    | Yookassa Configuration
    |--------------------------------------------------------------------------
    | These values determine the configuration for the Yookassa purchase system.
    |
    */
    'yookassa' => [
        'shop' => env('YOOKASSA_SHOP_ID', ''),
        'secret' => env('YOOKASSA_SECRET_KEY', ''),
    ],
    /*
    |--------------------------------------------------------------------------
    | Lava Configuration
    |--------------------------------------------------------------------------
    | These values determine the configuration for the Lava purchase system.
    |
    */
    'lava' => [
        'shop' => env('LAVA_SHOP_ID', ''),
        'secret' => env('LAVA_SECRET_KEY', ''),
    ],
    /*
    |--------------------------------------------------------------------------
    | Freekassa Configuration
    |--------------------------------------------------------------------------
    | These values determine the configuration for the Freekassa purchase system.
    |
    */
    'freekassa' => [
        'shop' => env('FREEKASSA_SHOP_ID', ''),
        'secret' => env('FREEKASSA_SECRET_KEY', ''),
    ],
];
