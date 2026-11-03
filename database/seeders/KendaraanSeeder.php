<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftarKendaraan = [
            [
                'jenis_kendaraan' => 'Avanza',
                'nomor_polisi' => 'BE 1212 AT',
                'warna' => 'Hitam',
                'kapasitas_kursi' => 5,
            ],
            [
                'jenis_kendaraan' => 'Xenia',
                'nomor_polisi' => 'BE 4677 DT',
                'warna' => 'Biru',
                'kapasitas_kursi' => 5,
            ],
        ];

        foreach ($daftarKendaraan as $kendaraan) {
            Kendaraan::firstOrCreate(
                ['nomor_polisi' => $kendaraan['nomor_polisi']],
                $kendaraan
            );
        }
    }
}
