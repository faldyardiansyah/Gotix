<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahkan index mandiri pada jadwal_id terlebih dahulu
        // agar foreign key pesanan_jadwal_id_foreign tetap memiliki index pendukung
        // saat unique_jadwal_nomor_kursi dihapus.
        Schema::table('pesanan', function (Blueprint $table) {
            $table->index('jadwal_id', 'idx_pesanan_jadwal_id');
        });

        // 2. Hapus unique index lama, buat generated column kursi_aktif, dan buat unique index baru.
        // kursi_aktif bernilai nomor_kursi jika status aktif (Belum Bayar, Dalam Proses, Lunas),
        // dan NULL jika status Dibatalkan sehingga kursi yang dibatalkan dapat dipesan kembali
        // tanpa batasan jumlah pembatalan sebelumnya.
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropUnique('unique_jadwal_nomor_kursi');
            $table->unsignedTinyInteger('kursi_aktif')
                ->storedAs("IF(status = 'Dibatalkan', NULL, nomor_kursi)")
                ->nullable()
                ->after('nomor_kursi');
            $table->unique(['jadwal_id', 'kursi_aktif'], 'unique_jadwal_kursi_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop unique index baru dan generated column kursi_aktif
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropUnique('unique_jadwal_kursi_aktif');
            $table->dropColumn('kursi_aktif');
        });

        // 2. Periksa apakah pemulihan unique constraint lama aman dilakukan
        $hasDuplicates = DB::table('pesanan')
            ->select('jadwal_id', 'nomor_kursi')
            ->groupBy('jadwal_id', 'nomor_kursi')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasDuplicates) {
            throw new RuntimeException(
                'Rollback migrasi dibatalkan secara aman: terdapat data historis pemesanan berulang pada kursi yang sama (dibatalkan/aktif). '.
                'Sesuai AGENTS.md, data transaksi tidak boleh dihapus secara sepihak. Pulihkan basis data dari backup jika rollback mutlak diperlukan.'
            );
        }

        // 3. Buat kembali unique index lama
        Schema::table('pesanan', function (Blueprint $table) {
            $table->unique(['jadwal_id', 'nomor_kursi'], 'unique_jadwal_nomor_kursi');
        });

        // 4. Hapus index pendukung jadwal_id setelah unique lama aktif kembali
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropIndex('idx_pesanan_jadwal_id');
        });
    }
};
