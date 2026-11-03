<?php

namespace App\Models;

use Database\Factories\KonfirmasiPembayaranFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KonfirmasiPembayaran extends Model
{
    /** @use HasFactory<KonfirmasiPembayaranFactory> */
    use HasFactory;

    /**
     * Nama tabel terkait dengan model.
     *
     * @var string
     */
    protected $table = 'konfirmasi_pembayaran';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pesanan_id',
        'nomor_resi',
        'rekening_pengirim',
        'tanggal_transfer',
        'jam_transfer',
        'bukti_path',
        'diverifikasi_oleh',
        'diverifikasi_pada',
        'alasan_penolakan',
    ];

    /**
     * Konversi tipe data atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_transfer' => 'date',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    /**
     * Relasi ke pesanan yang dikonfirmasi pembayarannya.
     *
     * @return BelongsTo<Pesanan, $this>
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Relasi ke petugas yang memverifikasi pembayaran.
     *
     * @return BelongsTo<Pengguna, $this>
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'diverifikasi_oleh');
    }
}
