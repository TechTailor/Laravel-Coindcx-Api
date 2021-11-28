<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CoinDCX authentication
    |--------------------------------------------------------------------------
    |
    | Authentication key and secret for CoinDCX API.
    |
     */

    'auth' => [
        'key'        => env('COINDCX_KEY', ''),
        'secret'     => env('COINDCX_SECRET', '')
    ],

    /*
    |--------------------------------------------------------------------------
    | API URLs
    |--------------------------------------------------------------------------
    |
    | CoinDCX API endpoints
    |
     */

    'urls' => [
        'api'  => 'https://api.coindcx.com/',
        'public'  => 'https://public.coindcx.com/',
    ],

];
