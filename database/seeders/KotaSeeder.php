<?php

namespace Database\Seeders;

use App\Models\Kota;
use Illuminate\Database\Seeder;

class KotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarKota = [
            'Bandar Lampung',
            'Metro',
            'Kalianda',
            'Bakauheni',
            'Natar',
            'Gedong Tataan',
            'Pringsewu',
            'Kota Agung',
            'Talang Padang',
            'Liwa',
            'Krui',
            'Kotabumi',
            'Bukit Kemuning',
            'Blambangan Umpu',
            'Baradatu',
            'Menggala',
            'Unit 2',
            'Panaragan',
            'Sukadana',
            'Way Jepara',
            'Bandar Sribhawono',
            'Gunung Sugih',
            'Bandar Jaya',
            'Kalirejo',
            'Mesuji',
            'Simpang Pematang',
            'Padang Cermin',
            'Gading Rejo',
            'Pagelaran',
        ];

        foreach ($daftarKota as $namaKota) {
            Kota::firstOrCreate(['nama_kota' => $namaKota]);
        }
    }
}
