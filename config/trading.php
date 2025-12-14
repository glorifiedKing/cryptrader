<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Commission Rate
    |--------------------------------------------------------------------------
    |
    | The commission rate applied to each trade as a decimal.
    | Default is 0.015 (1.5% of the trade value).
    |
    */

    'commission_rate' => env('TRADING_COMMISSION_RATE', 0.015),

    /*
    |--------------------------------------------------------------------------
    | Commission Paid By
    |--------------------------------------------------------------------------
    |
    | Determines who pays the commission fee.
    | Options: 'buyer', 'seller', 'split'
    |
    */

    'commission_paid_by' => env('TRADING_COMMISSION_PAID_BY', 'buyer'),

    /*
    |--------------------------------------------------------------------------
    | Supported Symbols
    |--------------------------------------------------------------------------
    |
    | List of cryptocurrency symbols supported by the platform.
    |
    */

    'supported_symbols' => explode(',', env('TRADING_SUPPORTED_SYMBOLS', 'BTC,ETH')),

    /*
    |--------------------------------------------------------------------------
    | Initial Balance
    |--------------------------------------------------------------------------
    |
    | The initial balance for new users.
    |
    */

    'initial_balance' => env('TRADING_INITIAL_BALANCE', 10),

];
