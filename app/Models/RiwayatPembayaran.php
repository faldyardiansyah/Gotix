<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPembayaran extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pembayaran';

    protected $fillable = [
        'pesanan_id',
        'konfirmasi_pembayaran_id',
        'pengguna_id',
        'nomor_resi',
        'rekening_pengirim',
        'tanggal_transfer',
        'jam_transfer',
        'bukti_path',
        'status',
        'diverifikasi_oleh',
        'diverifikasi_pada',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_transfer' => 'date',
        'diverifikasi_pada' => 'datetime',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function konfirmasiPembayaran(): BelongsTo
    {
        return $this->belongsTo(KonfirmasiPembayaran::class, 'konfirmasi_pembayaran_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'diverifikasi_oleh');
    }
}

