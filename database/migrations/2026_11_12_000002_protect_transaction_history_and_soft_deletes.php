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
        // 1. Tambahkan kolom deleted_at (SoftDeletes) ke tabel-tabel master data
        Schema::table('kota', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('kendaraan', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pengguna', function (Blueprint $table) {
            $table->softDeletes();
        });

        // 2. Ubah foreign key pada tabel jadwal dari CASCADE menjadi RESTRICT
        // agar penghapusan kota atau kendaraan tidak menghapus jadwal secara otomatis
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropForeign('jadwal_kota_asal_id_foreign');
            $table->dropForeign('jadwal_kota_tujuan_id_foreign');
            $table->dropForeign('jadwal_kendaraan_id_foreign');
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreign('kota_asal_id')->references('id')->on('kota')->restrictOnDelete();
            $table->foreign('kota_tujuan_id')->references('id')->on('kota')->restrictOnDelete();
            $table->foreign('kendaraan_id')->references('id')->on('kendaraan')->restrictOnDelete();
        });

        // 3. Ubah foreign key pada tabel pesanan dari CASCADE menjadi RESTRICT
        // agar penghapusan jadwal atau akun pengguna tidak menghapus riwayat tiket transaksi
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign('pesanan_jadwal_id_foreign');
            $table->dropForeign('pesanan_pengguna_id_foreign');
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreign('jadwal_id')->references('id')->on('jadwal')->restrictOnDelete();
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->restrictOnDelete();
        });

        // 4. Ubah foreign key pada konfirmasi_pembayaran dari CASCADE/SET NULL menjadi RESTRICT
        // agar riwayat bukti transfer dan audit verifikator tidak terhapus atau kehilangan jejak
        Schema::table('konfirmasi_pembayaran', function (Blueprint $table) {
            $table->dropForeign('konfirmasi_pembayaran_pesanan_id_foreign');
            $table->dropForeign('konfirmasi_pembayaran_diverifikasi_oleh_foreign');
        });

        Schema::table('konfirmasi_pembayaran', function (Blueprint $table) {
            $table->foreign('pesanan_id')->references('id')->on('pesanan')->restrictOnDelete();
            $table->foreign('diverifikasi_oleh')->references('id')->on('pengguna')->restrictOnDelete();
        });

        // 5. Ubah foreign key pada keluhan dari CASCADE/SET NULL menjadi RESTRICT
        // agar audit keluhan pengguna dan jejak admin penjawab tetap terlindungi
        Schema::table('keluhan', function (Blueprint $table) {
            $table->dropForeign('keluhan_pengguna_id_foreign');
            $table->dropForeign('keluhan_dibalas_oleh_foreign');
        });

        Schema::table('keluhan', function (Blueprint $table) {
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->restrictOnDelete();
            $table->foreign('dibalas_oleh')->references('id')->on('pengguna')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Kembalikan foreign key pada keluhan
        Schema::table('keluhan', function (Blueprint $table) {
            $table->dropForeign(['pengguna_id']);
            $table->dropForeign(['dibalas_oleh']);
        });

        Schema::table('keluhan', function (Blueprint $table) {
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->cascadeOnDelete();
            $table->foreign('dibalas_oleh')->references('id')->on('pengguna')->nullOnDelete();
        });

        // 2. Kembalikan foreign key pada konfirmasi_pembayaran
        Schema::table('konfirmasi_pembayaran', function (Blueprint $table) {
            $table->dropForeign(['pesanan_id']);
            $table->dropForeign(['diverifikasi_oleh']);
        });

        Schema::table('konfirmasi_pembayaran', function (Blueprint $table) {
            $table->foreign('pesanan_id')->references('id')->on('pesanan')->cascadeOnDelete();
            $table->foreign('diverifikasi_oleh')->references('id')->on('pengguna')->nullOnDelete();
        });

        // 3. Kembalikan foreign key pada pesanan
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['jadwal_id']);
            $table->dropForeign(['pengguna_id']);
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreign('jadwal_id')->references('id')->on('jadwal')->cascadeOnDelete();
            $table->foreign('pengguna_id')->references('id')->on('pengguna')->cascadeOnDelete();
        });

        // 4. Kembalikan foreign key pada jadwal
        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropForeign(['kota_asal_id']);
            $table->dropForeign(['kota_tujuan_id']);
            $table->dropForeign(['kendaraan_id']);
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->foreign('kota_asal_id')->references('id')->on('kota')->cascadeOnDelete();
            $table->foreign('kota_tujuan_id')->references('id')->on('kota')->cascadeOnDelete();
            $table->foreign('kendaraan_id')->references('id')->on('kendaraan')->cascadeOnDelete();
        });

        // 5. Periksa apakah terdapat data master yang sedang dalam status soft-deleted
        $hasSoftDeleted = DB::table('kota')->whereNotNull('deleted_at')->exists()
            || DB::table('kendaraan')->whereNotNull('deleted_at')->exists()
            || DB::table('jadwal')->whereNotNull('deleted_at')->exists()
            || DB::table('pengguna')->whereNotNull('deleted_at')->exists();

        if ($hasSoftDeleted) {
            throw new RuntimeException(
                'Rollback migrasi dibatalkan secara aman: terdapat data master yang sedang dalam status soft-deleted. '.
                'Menghapus kolom deleted_at akan memulihkan entitas nonaktif tanpa persetujuan. '.
                'Pulihkan basis data dari backup jika rollback mutlak diperlukan.'
            );
        }

        Schema::table('pengguna', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('kendaraan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('kota', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
