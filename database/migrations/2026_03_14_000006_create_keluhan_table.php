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
        Schema::create('keluhan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->nullOnDelete();
            $table->enum('kategori', ['Akun', 'Pesanan', 'Pembayaran', 'Pelayanan', 'Lainnya']);
            $table->string('deskripsi', 200);
            $table->text('balasan')->nullable();
            $table->foreignId('dibalas_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->dateTime('dibalas_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluhan');
    }
};
