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
     */
    public function run(): void
    {
        $passwordDemo = Hash::make('password');

        // 1 Admin
        Pengguna::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Gotix',
                'email' => 'admin@gotix.test',
                'telepon' => '081234567890',
                'alamat' => 'Jl. Raden Intan No. 1, Enggal, Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
                'peran' => PeranPengguna::Admin,
                'password' => $passwordDemo,
            ]
        );

        // 1 Operator
        Pengguna::firstOrCreate(
            ['username' => 'operator'],
            [
                'nama' => 'Operator Loket Gotix',
                'email' => 'operator@gotix.test',
                'telepon' => '081234567891',
                'alamat' => 'Jl. Teuku Umar No. 10, Kedaton, Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
                'peran' => PeranPengguna::Operator,
                'password' => $passwordDemo,
            ]
        );

        // 5 Member Demo
        $daftarMember = [
            [
                'nama' => 'Budi Santoso',
                'username' => 'member1',
                'email' => 'budi@gotix.test',
                'telepon' => '081270001001',
                'alamat' => 'Jl. ZA Pagar Alam No. 5, Rajabasa, Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
            ],
            [
                'nama' => 'Siti Rahma',
                'username' => 'member2',
                'email' => 'siti@gotix.test',
                'telepon' => '081270001002',
                'alamat' => 'Jl. Kartini No. 12, Tanjung Karang Pusat, Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::P,
            ],
            [
                'nama' => 'Agus Setiawan',
                'username' => 'member3',
                'email' => 'agus@gotix.test',
                'telepon' => '081270001003',
                'alamat' => 'Jl. Gajah Mada No. 44, Kedamaian, Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
            ],
            [
                'nama' => 'Dewi Lestari',
                'username' => 'member4',
                'email' => 'dewi@gotix.test',
                'telepon' => '081270001004',
                'alamat' => 'Jl. Diponegoro No. 8, Teluk Betung Utara, Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::P,
            ],
            [
                'nama' => 'Eko Prasetyo',
                'username' => 'member5',
                'email' => 'eko@gotix.test',
                'telepon' => '081270001005',
                'alamat' => 'Jl. Wolter Monginsidi No. 15, Teluk Betung Selatan, Bandar Lampung',
                'jenis_kelamin' => JenisKelamin::L,
            ],
        ];

        foreach ($daftarMember as $member) {
            Pengguna::firstOrCreate(
                ['username' => $member['username']],
                array_merge($member, [
                    'peran' => PeranPengguna::Member,
                    'password' => $passwordDemo,
                ])
            );
        }
    }
}
