<?php

return [
    'booking_fee' => (int) env('BOOKING_FEE_AMOUNT', 2500000),

    'settlement' => [
        'bank' => env('SHOWROOM_SETTLEMENT_BANK', 'Bank Mandiri'),
        'account_name' => env('SHOWROOM_SETTLEMENT_ACCOUNT_NAME', 'Diki Susanto'),
        'account_number' => env('SHOWROOM_SETTLEMENT_ACCOUNT_NUMBER', '1080093012152'),
    ],
];
