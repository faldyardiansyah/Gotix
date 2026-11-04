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
                'jenis_kendaraan' => 'Toyota HiAce Premio',
                'nomor_polisi' => 'B 7701 TGA',
                'warna' => 'Putih Mutiara',
                'kapasitas_kursi' => 5,
            ],
            [
                'jenis_kendaraan' => 'Isuzu Elf Executive',
                'nomor_polisi' => 'D 8820 KLG',
                'warna' => 'Silver Metalik',
                'kapasitas_kursi' => 5,
            ],
            [
                'jenis_kendaraan' => 'Toyota Innova Zenix',
                'nomor_polisi' => 'AB 1928 YK',
                'warna' => 'Hitam Metalik',
                'kapasitas_kursi' => 5,
            ],
            [
                'jenis_kendaraan' => 'Toyota Avanza Veloz',
                'nomor_polisi' => 'L 3311 ZB',
                'warna' => 'Abu-abu',
                'kapasitas_kursi' => 5,
            ],
            [
                'jenis_kendaraan' => 'Toyota HiAce Commuter',
                'nomor_polisi' => 'B 2049 JKT',
                'warna' => 'Putih',
                'kapasitas_kursi' => 5,
            ],
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
