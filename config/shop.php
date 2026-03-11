<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shop Settings
    |--------------------------------------------------------------------------
    */
    'name'     => env('APP_NAME', 'MultiVendor Shop'),
    'currency' => env('SHOP_CURRENCY', 'USD'),
    'currency_symbol' => env('SHOP_CURRENCY_SYMBOL', '$'),

    'default_commission_rate' => env('DEFAULT_COMMISSION_RATE', 10),
    'tax_rate'                => env('SHOP_TAX_RATE', 0),

    'flat_shipping_rate'       => env('FLAT_SHIPPING_RATE', 5.99),
    'free_shipping_threshold'  => env('FREE_SHIPPING_THRESHOLD', 50),

    'products_per_page' => 12,
    'reviews_require_purchase' => true,
    'reviews_require_approval' => true,

    'max_cart_quantity' => 100,
    'max_product_images' => 8,
];
