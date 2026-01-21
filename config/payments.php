<?php

declare(strict_types=1);

return [
    'telebirr' => [
        'account_code' => env('TELEBIRR_ACCOUNT_CODE', '1234-5678-90'),
        'receiver_name' => env('TELEBIRR_RECEIVER_NAME', 'Mekodonia Home Connect'),
        'reference_hint' => env('TELEBIRR_REFERENCE_HINT', 'Include your name & sponsorship relationship'),
    ],

    'bank_accounts' => [
        [
            'bank' => 'Commercial Bank of Ethiopia',
            'branch' => 'Addis Ababa - Merkato',
            'account_name' => 'Mekodonia Home Connect',
            'account_number' => '0123-4567-890',
            'iban' => 'ET21CBEF012345678900123',
        ],
        [
            'bank' => 'Awash Bank',
            'branch' => 'Addis Ababa - Bole',
            'account_name' => 'Mekodonia Home Connect',
            'account_number' => '9876-5432-109',
            'iban' => 'ET41AWBN098765432101987',
        ],
    ],
];
