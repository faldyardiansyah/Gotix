<?php

namespace Database\Factories;

use App\Enums\KategoriKeluhan;
use App\Models\Keluhan;
use App\Models\Pengguna;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Keluhan>
 */
class KeluhanFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     *
     * @var class-string<Keluhan>
     */
    protected $model = Keluhan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengguna_id' => Pengguna::factory(),
            'pesanan_id' => Pesanan::factory(),
            'kategori' => fake()->randomElement([
                KategoriKeluhan::Akun,
                KategoriKeluhan::Pesanan,
                KategoriKeluhan::Pembayaran,
                KategoriKeluhan::Pelayanan,
                KategoriKeluhan::Lainnya,
            ]),
            'deskripsi' => fake('id_ID')->text(150),
            'balasan' => null,
            'dibalas_oleh' => null,
            'dibalas_pada' => null,
        ];
    }
}
