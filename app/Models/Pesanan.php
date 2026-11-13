<?php

namespace App\Models;

use App\Enums\StatusPesanan;
use Database\Factories\PesananFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    /** @use HasFactory<PesananFactory> */
    use HasFactory;

    /**
     * Nama tabel terkait dengan model.
     *
     * @var string
     */
    protected $table = 'pesanan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kode_pesanan',
        'jadwal_id',
        'pengguna_id',
        'nama_penumpang',
        'telepon_penumpang',
        'dibuat_oleh_operator_id',
        'nomor_kursi',
        'harga_snapshot',
        'status',
        'batas_bayar',
    ];

    /**
     * Konversi tipe data atribut.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'nomor_kursi' => 'integer',
            'kursi_aktif' => 'integer',
            'harga_snapshot' => 'integer',
            'status' => StatusPesanan::class,
            'batas_bayar' => 'datetime',
        ];
    }

    /**
     * Relasi ke jadwal yang dipesan.
     *
     * @return BelongsTo<Jadwal, $this>
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class, 'jadwal_id')->withTrashed();
    }

    /**
     * Relasi ke pengguna pemesan tiket.
     *
     * @return BelongsTo<Pengguna, $this>
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id')->withTrashed();
    }

    /**
     * Relasi ke operator pembuat pesanan loket.
     *
     * @return BelongsTo<Pengguna, $this>
     */
    public function operatorLoket(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'dibuat_oleh_operator_id')->withTrashed();
    }

    /**
     * Relasi ke konfirmasi pembayaran tiket.
     *
     * @return HasOne<KonfirmasiPembayaran, $this>
     */
    public function konfirmasiPembayaran(): HasOne
    {
        return $this->hasOne(KonfirmasiPembayaran::class, 'pesanan_id');
    }

    /**
     * Relasi ke riwayat bukti pembayaran pesanan.
     *
     * @return HasMany<RiwayatPembayaran, $this>
     */
    public function riwayatPembayaran(): HasMany
    {
        return $this->hasMany(RiwayatPembayaran::class, 'pesanan_id')->latest();
    }

    /**
     * Relasi ke keluhan terkait pesanan ini.
     *
     * @return HasMany<Keluhan, $this>
     */
    public function keluhan(): HasMany
    {
        return $this->hasMany(Keluhan::class, 'pesanan_id');
    }
}
