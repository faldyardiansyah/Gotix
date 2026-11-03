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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kota_asal_id')->constrained('kota')->cascadeOnDelete();
            $table->foreignId('kota_tujuan_id')->constrained('kota')->cascadeOnDelete();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->cascadeOnDelete();
            $table->unsignedInteger('harga');
            $table->date('tanggal_berangkat');
            $table->time('jam_berangkat');
            $table->timestamps();

            $table->index(['kota_asal_id', 'kota_tujuan_id', 'tanggal_berangkat'], 'idx_jadwal_rute_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
