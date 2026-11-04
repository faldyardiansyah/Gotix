<?php

namespace Database\Seeders;

use App\Models\Kota;
use Illuminate\Database\Seeder;

class KotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Data kota sesuai data dosen (db_tiket2) — hanya kota Lampung.
     */
    public function run(): void
    {
        $daftarKota = [
            'Bandar Lampung',
            'Metro',
            'Lampung Tengah',
            'Lampung Timur',
            'Lampung Selatan',
            'Tanggamus',
            'Pringsewu',
            'Pesawaran',
            'Lampung Barat',
            'Lampung Utara',
            'Mesuji',
            'Tulang Bawang',
            'Tulang Bawang Barat',
            'Way Kanan',
            'Gunung Sugih',
            'Kotabumi',
            'Kalianda',
            'Liwa',
            'Sukadana',
            'Wiralaga Mulya',
            'Gedong Tataan',
            'Krui',
            'Menggala',
            'Tulang Bawang Tengah',
            'Kota Agung',
            'Blambangan Umpu',
            'Tanjung Karang',
        ];

        foreach ($daftarKota as $namaKota) {
            Kota::firstOrCreate(['nama_kota' => $namaKota]);
        }
    }
}
