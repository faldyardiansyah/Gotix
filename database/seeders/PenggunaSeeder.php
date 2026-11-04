<?php

namespace Database\Seeders;

use App\Enums\JenisKelamin;
use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Hanya 3 akun uji sesuai data dosen — tanpa member fiktif tambahan.
     */
    public function run(): void
    {
        // Admin
        Pengguna::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama'          => 'Administrator',
                'email'         => 'admin@gotix.test',
                'telepon'       => '081234567890',
                'alamat'        => 'Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
                'peran'         => PeranPengguna::Admin,
                'password'      => Hash::make('admin123'),
            ]
        );

        // Operator
        Pengguna::firstOrCreate(
            ['username' => 'operator'],
            [
                'nama'          => 'Operator Loket',
                'email'         => 'operator@gotix.test',
                'telepon'       => '081234567891',
                'alamat'        => 'Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
                'peran'         => PeranPengguna::Operator,
                'password'      => Hash::make('operator123'),
            ]
        );

        // Member
        Pengguna::firstOrCreate(
            ['username' => 'member'],
            [
                'nama'          => 'Member Demo',
                'email'         => 'member@gotix.test',
                'telepon'       => '081234567892',
                'alamat'        => 'Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
                'peran'         => PeranPengguna::Member,
                'password'      => Hash::make('member123'),
            ]
        );
    }
}
