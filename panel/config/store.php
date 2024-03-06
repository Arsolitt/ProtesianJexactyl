<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jexactyl Storefront Settings
    |--------------------------------------------------------------------------
    |
    | This configuration file is used to interact with the app in order to
    | get and set configurations for the Jexactyl Storefront.
    |
    */
    'settings' => [
        'currencies' => [
            'EUR' => 'Евро',
            'USD' => 'Доллар',
            'JPY' => 'Japanese Yen',
            'GBP' => 'Pound Sterling',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'RUB' => 'Рубль',
        ],
        'currency' => env('STORE_CURRENCY', 'RUB'),
        'cost' => env('STORE_COST', 100),
    ],
    'gateways' => [
        'yookassa' => [
            'shopID' => env('YOOKASSA_SHOP_ID', ''),
            'secretKey' => env('YOOKASSA_SECRET_KEY', ''),
        ],
    ]
];
