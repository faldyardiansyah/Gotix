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
        $asal = Kota::where('nama_kota', 'Bandar Lampung')->first();
        $kendaraanAvanza = Kendaraan::where('nomor_polisi', 'BE 1212 AT')->first();
        $kendaraanXenia = Kendaraan::where('nomor_polisi', 'BE 4677 DT')->first();

        if (! $asal || ! $kendaraanAvanza || ! $kendaraanXenia) {
            return;
        }

        $ruteTujuan = [
            ['nama_kota' => 'Metro', 'harga' => 45000],
            ['nama_kota' => 'Gunung Sugih', 'harga' => 55000],
            ['nama_kota' => 'Liwa', 'harga' => 80000],
            ['nama_kota' => 'Kalianda', 'harga' => 50000],
            ['nama_kota' => 'Pringsewu', 'harga' => 40000],
            ['nama_kota' => 'Kota Agung', 'harga' => 65000],
        ];

        $jamBerangkat = ['08:00:00', '13:00:00', '16:30:00'];

        // Buat jadwal untuk 5 hari ke depan
        for ($hari = 1; $hari <= 5; $hari++) {
            $tanggal = Carbon::now()->addDays($hari)->format('Y-m-d');

            foreach ($ruteTujuan as $index => $tujuanData) {
                $tujuan = Kota::where('nama_kota', $tujuanData['nama_kota'])->first();
                if (! $tujuan) {
                    continue;
                }

                $kendaraan = ($index % 2 === 0) ? $kendaraanAvanza : $kendaraanXenia;
                $jam = $jamBerangkat[$index % count($jamBerangkat)];

                Jadwal::firstOrCreate([
                    'kota_asal_id' => $asal->id,
                    'kota_tujuan_id' => $tujuan->id,
                    'kendaraan_id' => $kendaraan->id,
                    'harga' => $tujuanData['harga'],
                    'tanggal_berangkat' => $tanggal,
                    'jam_berangkat' => $jam,
                ]);
            }
        }
    }
}
