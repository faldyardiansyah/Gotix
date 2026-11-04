<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Kendaraan;
use App\Models\Kota;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semuaKendaraan = Kendaraan::all();
        if ($semuaKendaraan->isEmpty()) {
            return;
        }

        $pasanganRute = [
            // Rute Populer Nasional
            ['asal' => 'Jakarta', 'tujuan' => 'Bandung', 'harga' => 120000, 'jam' => ['07:30:00', '13:00:00', '17:30:00']],
            ['asal' => 'Bandung', 'tujuan' => 'Jakarta', 'harga' => 120000, 'jam' => ['08:00:00', '14:00:00', '19:00:00']],
            ['asal' => 'Yogyakarta', 'tujuan' => 'Surabaya', 'harga' => 180000, 'jam' => ['08:00:00', '15:30:00']],
            ['asal' => 'Surabaya', 'tujuan' => 'Yogyakarta', 'harga' => 180000, 'jam' => ['09:00:00', '16:00:00']],
            ['asal' => 'Semarang', 'tujuan' => 'Solo', 'harga' => 75000, 'jam' => ['07:00:00', '11:00:00', '16:00:00']],
            ['asal' => 'Solo', 'tujuan' => 'Semarang', 'harga' => 75000, 'jam' => ['08:30:00', '13:30:00', '17:30:00']],
            ['asal' => 'Surabaya', 'tujuan' => 'Malang', 'harga' => 65000, 'jam' => ['06:30:00', '10:00:00', '14:30:00', '18:30:00']],
            ['asal' => 'Malang', 'tujuan' => 'Surabaya', 'harga' => 65000, 'jam' => ['07:00:00', '11:00:00', '15:00:00', '19:00:00']],
            ['asal' => 'Medan', 'tujuan' => 'Pekanbaru', 'harga' => 240000, 'jam' => ['09:00:00', '20:00:00']],
            ['asal' => 'Pekanbaru', 'tujuan' => 'Medan', 'harga' => 240000, 'jam' => ['09:00:00', '20:00:00']],
            ['asal' => 'Jakarta', 'tujuan' => 'Bandar Lampung', 'harga' => 220000, 'jam' => ['08:00:00', '20:30:00']],
            ['asal' => 'Bandar Lampung', 'tujuan' => 'Jakarta', 'harga' => 220000, 'jam' => ['09:00:00', '21:00:00']],
            ['asal' => 'Denpasar', 'tujuan' => 'Surabaya', 'harga' => 260000, 'jam' => ['18:00:00']],
            // Rute Regional Lampung
            ['asal' => 'Bandar Lampung', 'tujuan' => 'Metro', 'harga' => 45000, 'jam' => ['08:00:00', '13:00:00', '16:30:00']],
            ['asal' => 'Metro', 'tujuan' => 'Bandar Lampung', 'harga' => 45000, 'jam' => ['08:30:00', '13:30:00', '17:00:00']],
            ['asal' => 'Bandar Lampung', 'tujuan' => 'Gunung Sugih', 'harga' => 55000, 'jam' => ['09:00:00', '14:00:00']],
            ['asal' => 'Bandar Lampung', 'tujuan' => 'Liwa', 'harga' => 80000, 'jam' => ['08:00:00', '15:00:00']],
            ['asal' => 'Bandar Lampung', 'tujuan' => 'Pringsewu', 'harga' => 40000, 'jam' => ['09:30:00', '15:30:00']],
        ];

        // Buat jadwal untuk 7 hari ke depan
        for ($hari = 0; $hari <= 7; $hari++) {
            $tanggal = Carbon::now()->addDays($hari)->format('Y-m-d');

            foreach ($pasanganRute as $idxRute => $rute) {
                $kotaAsal = Kota::where('nama_kota', $rute['asal'])->first();
                $kotaTujuan = Kota::where('nama_kota', $rute['tujuan'])->first();

                if (! $kotaAsal || ! $kotaTujuan) {
                    continue;
                }

                foreach ($rute['jam'] as $idxJam => $jam) {
                    $kendaraan = $semuaKendaraan[($idxRute + $idxJam) % $semuaKendaraan->count()];

                    Jadwal::firstOrCreate([
                        'kota_asal_id' => $kotaAsal->id,
                        'kota_tujuan_id' => $kotaTujuan->id,
                        'kendaraan_id' => $kendaraan->id,
                        'harga' => $rute['harga'],
                        'tanggal_berangkat' => $tanggal,
                        'jam_berangkat' => $jam,
                    ]);
                }
            }
        }
    }
}
