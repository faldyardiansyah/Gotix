<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riwayat_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->restrictOnDelete();
            $table->foreignId('konfirmasi_pembayaran_id')->nullable()->constrained('konfirmasi_pembayaran')->nullOnDelete();
            $table->foreignId('pengguna_id')->constrained('pengguna')->restrictOnDelete();
            $table->string('nomor_resi');
            $table->string('rekening_pengirim');
            $table->date('tanggal_transfer');
            $table->time('jam_transfer');
            $table->string('bukti_path');
            $table->string('status')->default('Dalam Proses'); // Dalam Proses, Diterima, Ditolak
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->dateTime('diverifikasi_pada')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();

            $table->index(['pesanan_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pembayaran');
    }
};

