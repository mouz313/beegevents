<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Platform Commission
    |--------------------------------------------------------------------------
    | Percentage of each booking's total price kept by the platform as a
    | commission fee. A global default rate can be overridden per booking type.
    */

    'default_rate' => (float) env('COMMISSION_RATE', 10),

    'types' => [
        'single' => (float) env('COMMISSION_RATE_SINGLE', env('COMMISSION_RATE', 10)),
        'multi' => (float) env('COMMISSION_RATE_MULTI', env('COMMISSION_RATE', 10)),
        'package' => (float) env('COMMISSION_RATE_PACKAGE', env('COMMISSION_RATE', 10)),
        'custom' => (float) env('COMMISSION_RATE_CUSTOM', env('COMMISSION_RATE', 10)),
    ],

];
