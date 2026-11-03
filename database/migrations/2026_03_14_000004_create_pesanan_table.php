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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->unsignedTinyInteger('nomor_kursi');
            $table->unsignedInteger('harga_snapshot');
            $table->enum('status', ['Belum Bayar', 'Dalam Proses', 'Lunas', 'Dibatalkan'])->default('Belum Bayar');
            $table->dateTime('batas_bayar');
            $table->timestamps();

            $table->unique(['jadwal_id', 'nomor_kursi'], 'unique_jadwal_nomor_kursi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
