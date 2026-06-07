<?php

return [
    'showroom' => [
        'name' => env('SHOWROOM_NAME', 'Maharani Mobil'),
        'phone' => env('SHOWROOM_PHONE', '0811-7584-617'),
        'whatsapp' => env('SHOWROOM_WHATSAPP', '628117584617'),
        'address' => env(
            'SHOWROOM_ADDRESS',
            'Jl. Arifin Ahmad No.113, Sidomulyo Timur, Marpoyan Damai, Kota Pekanbaru, Riau'
        ),
    ],

    'owner' => [
        'name' => env('SHOWROOM_OWNER_NAME', 'Rarendra'),
        'title' => env('SHOWROOM_OWNER_TITLE', 'Pemilik Maharani Mobil'),
        'signature_asset' => env('SHOWROOM_OWNER_SIGNATURE_ASSET', 'assets/documents/owner-signature-rarendra-white.png'),
    ],

    'verification' => [
        'seal_asset' => env('SHOWROOM_DOCUMENT_SEAL_ASSET', 'assets/documents/document-verification-seal.png'),
    ],
];
