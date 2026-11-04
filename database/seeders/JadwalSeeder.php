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
     * Data jadwal sesuai data dosen (db_tiket2) — 6 jadwal dari Bandar Lampung,
     * tanggal disesuaikan ke masa depan agar bisa dipesan.
     */
    public function run(): void
    {
        $avanza = Kendaraan::where('nomor_polisi', 'BE 1212 AT')->first();
        $xenia  = Kendaraan::where('nomor_polisi', 'BE 4677 DT')->first();

        if (! $avanza || ! $xenia) {
            return;
        }

        $bandarLampung = Kota::where('nama_kota', 'Bandar Lampung')->first();
        if (! $bandarLampung) {
            return;
        }

        // 6 jadwal dari data dosen, tanggal digeser ke depan
        $ruteDosen = [
            // asal=Bandar Lampung (id1), tujuan=Metro (id2), Xenia, harga 60000, 08:21
            ['tujuan' => 'Metro',          'harga' => 60000, 'jam' => '08:21:00', 'kendaraan' => $xenia,  'hari_ke' => 1],
            // asal=Bandar Lampung (id1), tujuan=Lampung Tengah (id3), Avanza, harga 50000, 06:31
            ['tujuan' => 'Lampung Tengah', 'harga' => 50000, 'jam' => '06:31:00', 'kendaraan' => $avanza, 'hari_ke' => 1],
            // asal=Bandar Lampung (id1), tujuan=Lampung Barat (id9), Avanza, harga 70000, 08:00
            ['tujuan' => 'Lampung Barat',  'harga' => 70000, 'jam' => '08:00:00', 'kendaraan' => $avanza, 'hari_ke' => 2],
            // asal=Bandar Lampung (id1), tujuan=Lampung Selatan (id5), Xenia, harga 40000, 01:01
            ['tujuan' => 'Lampung Selatan','harga' => 40000, 'jam' => '08:00:00', 'kendaraan' => $xenia,  'hari_ke' => 2],
            // asal=Bandar Lampung (id1), tujuan=Pringsewu (id7), Avanza, harga 50000, 02:01
            ['tujuan' => 'Pringsewu',      'harga' => 50000, 'jam' => '09:00:00', 'kendaraan' => $avanza, 'hari_ke' => 3],
            // asal=Bandar Lampung (id1), tujuan=Tanggamus (id6), Xenia, harga 80000, 10:00
            ['tujuan' => 'Tanggamus',      'harga' => 80000, 'jam' => '10:00:00', 'kendaraan' => $xenia,  'hari_ke' => 3],
        ];

        foreach ($ruteDosen as $rute) {
            $kotaTujuan = Kota::where('nama_kota', $rute['tujuan'])->first();
            if (! $kotaTujuan) {
                continue;
            }

            $tanggal = Carbon::now()->addDays($rute['hari_ke'])->format('Y-m-d');

            Jadwal::firstOrCreate(
                [
                    'kota_asal_id'   => $bandarLampung->id,
                    'kota_tujuan_id' => $kotaTujuan->id,
                    'kendaraan_id'   => $rute['kendaraan']->id,
                    'tanggal_berangkat' => $tanggal,
                    'jam_berangkat'  => $rute['jam'],
                ],
                [
                    'harga' => $rute['harga'],
                ]
            );
        }
    }
}
