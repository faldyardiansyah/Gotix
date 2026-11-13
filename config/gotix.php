<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Pembayaran (Payment Deadlines)
    |--------------------------------------------------------------------------
    |
    | Durasi batas waktu pembayaran (dalam jam) sebelum pesanan dibatalkan
    | secara otomatis oleh scheduler jika berstatus Belum Bayar.
    |
    */

    'batas_bayar_jam' => (int) env('GOTIX_BATAS_BAYAR_JAM', 24),
    'batas_bayar_loket_jam' => (int) env('GOTIX_BATAS_BAYAR_LOKET_JAM', 1),

    /*
    |--------------------------------------------------------------------------
    | Kapasitas Kursi Default (Fallback Seat Capacity)
    |--------------------------------------------------------------------------
    |
    | Jumlah kursi default armada jika data kendaraan tidak spesifik.
    |
    */

    'kapasitas_kursi' => (int) env('GOTIX_KAPASITAS_KURSI', 5),

    /*
    |--------------------------------------------------------------------------
    | Rekening Tujuan Transfer Manual
    |--------------------------------------------------------------------------
    |
    | Informasi rekening bank resmi Gotix untuk transfer pembayaran manual.
    | Mendukung key nama/rekening/pemilik dan alias nomor/atas_nama untuk
    | konsistensi di seluruh tampilan.
    |
    */

    'bank' => [
        'nama' => env('GOTIX_BANK_NAME', 'Bank Central Asia (BCA)'),
        'rekening' => env('GOTIX_BANK_ACCOUNT', '8291039182'),
        'nomor' => env('GOTIX_BANK_ACCOUNT', '8291039182'),
        'pemilik' => env('GOTIX_BANK_HOLDER', 'PT Gotix Transportasi Indonesia'),
        'atas_nama' => env('GOTIX_BANK_HOLDER', 'PT Gotix Transportasi Indonesia'),
    ],

    // Backward-compatibility aliases untuk template lama
    'bank_name' => env('GOTIX_BANK_NAME', 'Bank Central Asia (BCA)'),
    'bank_account' => env('GOTIX_BANK_ACCOUNT', '8291039182'),
    'bank_holder' => env('GOTIX_BANK_HOLDER', 'PT Gotix Transportasi Indonesia'),

    /*
    |--------------------------------------------------------------------------
    | Batasan Unggah Bukti Pembayaran
    |--------------------------------------------------------------------------
    |
    | Ukuran maksimum file (dalam kilobyte) dan ekstensi MIME yang diizinkan
    | untuk berkas struk transfer pembayaran manual.
    |
    */

    'pembayaran' => [
        'max_file_kb' => (int) env('GOTIX_BUKTI_MAX_KB', 5120),
        'mimes' => ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batasan Karakter Pengaduan & Keluhan Penumpang
    |--------------------------------------------------------------------------
    |
    | Batas minimum dan maksimum karakter untuk deskripsi keluhan member
    | serta balasan dari admin.
    |
    */

    'keluhan' => [
        'deskripsi_min' => (int) env('GOTIX_KELUHAN_DESKRIPSI_MIN', 10),
        'deskripsi_max' => (int) env('GOTIX_KELUHAN_DESKRIPSI_MAX', 1000),
        'balasan_min' => (int) env('GOTIX_KELUHAN_BALASAN_MIN', 3),
        'balasan_max' => (int) env('GOTIX_KELUHAN_BALASAN_MAX', 1000),
        'alasan_penolakan_max' => (int) env('GOTIX_ALASAN_PENOLAKAN_MAX', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Perlindungan Rate Limiting Autentikasi
    |--------------------------------------------------------------------------
    |
    | Konfigurasi pembatasan percobaan masuk (login) dan pendaftaran (register)
    | guna memitigasi serangan brute-force dan spam pendaftaran akun.
    |
    */

    'auth' => [
        'login_max_attempts' => (int) env('GOTIX_LOGIN_MAX_ATTEMPTS', 5),
        'login_decay_seconds' => (int) env('GOTIX_LOGIN_DECAY_SECONDS', 60),
        'register_max_attempts' => (int) env('GOTIX_REGISTER_MAX_ATTEMPTS', 5),
        'register_decay_seconds' => (int) env('GOTIX_REGISTER_DECAY_SECONDS', 60),
    ],
];
