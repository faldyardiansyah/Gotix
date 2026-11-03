<?php

namespace Database\Factories;

use App\Enums\JenisKelamin;
use App\Enums\PeranPengguna;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory.
     *
     * @var class-string<Pengguna>
     */
    protected $model = Pengguna::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake('id_ID')->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'telepon' => fake('id_ID')->phoneNumber(),
            'alamat' => fake('id_ID')->address(),
            'jenis_kelamin' => fake()->randomElement([JenisKelamin::L, JenisKelamin::P]),
            'peran' => PeranPengguna::Member,
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => PeranPengguna::Admin,
        ]);
    }

    public function operator(): static
    {
        return $this->state(fn (array $attributes) => [
            'peran' => PeranPengguna::Operator,
        ]);
    }
}
