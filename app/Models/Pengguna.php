<?php

namespace App\Models;

use App\Enums\JenisKelamin;
use App\Enums\PeranPengguna;
use Database\Factories\PenggunaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    /** @use HasFactory<PenggunaFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Nama tabel terkait dengan model.
     *
     * @var string
     */
    protected $table = 'pengguna';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'username',
        'email',
        'telepon',
        'alamat',
        'jenis_kelamin',
        'peran',
        'password',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Konversi tipe data atribut.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'jenis_kelamin' => JenisKelamin::class,
            'peran' => PeranPengguna::class,
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke pesanan yang dibuat pengguna.
     *
     * @return HasMany<Pesanan, $this>
     */
    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'pengguna_id');
    }

    /**
     * Relasi ke pesanan loket yang dibuat oleh operator ini.
     *
     * @return HasMany<Pesanan, $this>
     */
    public function pesananLoketDibuat(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'dibuat_oleh_operator_id');
    }

    /**
     * Relasi ke keluhan yang diajukan pengguna.
     *
     * @return HasMany<Keluhan, $this>
     */
    public function keluhan(): HasMany
    {
        return $this->hasMany(Keluhan::class, 'pengguna_id');
    }

    /**
     * Relasi ke konfirmasi pembayaran yang diverifikasi pengguna.
     *
     * @return HasMany<KonfirmasiPembayaran, $this>
     */
    public function konfirmasiVerifikasi(): HasMany
    {
        return $this->hasMany(KonfirmasiPembayaran::class, 'diverifikasi_oleh');
    }

    /**
     * Relasi ke keluhan yang dibalas oleh pengguna.
     *
     * @return HasMany<Keluhan, $this>
     */
    public function balasanKeluhan(): HasMany
    {
        return $this->hasMany(Keluhan::class, 'dibalas_oleh');
    }

    public function isAdmin(): bool
    {
        return $this->peran === PeranPengguna::Admin;
    }

    public function isOperator(): bool
    {
        return $this->peran === PeranPengguna::Operator;
    }

    public function isMember(): bool
    {
        return $this->peran === PeranPengguna::Member;
    }
}
