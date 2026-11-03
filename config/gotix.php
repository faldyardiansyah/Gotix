<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Gotix
    |--------------------------------------------------------------------------
    |
    | Batas waktu pembayaran (dalam jam), kapasitas kursi standar kendaraan,
    | dan rekening tujuan transfer manual.
    |
    */

    'batas_bayar_jam' => (int) env('GOTIX_BATAS_BAYAR_JAM', 24),

    'kapasitas_kursi' => (int) env('GOTIX_KAPASITAS_KURSI', 5),

    'bank' => [
        'nama' => env('GOTIX_BANK_NAME', 'BCA'),
        'rekening' => env('GOTIX_BANK_ACCOUNT', '0000000000'),
        'pemilik' => env('GOTIX_BANK_HOLDER', 'PT Gotix Travel'),
    ],
];
