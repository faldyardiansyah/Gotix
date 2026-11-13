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
        // 1. Modifikasi kolom deskripsi dari VARCHAR(200) menjadi TEXT
        Schema::table('keluhan', function (Blueprint $table) {
            $table->text('deskripsi')->change();
        });

        // 2. Tambah nilai enum 'Armada' ke kolom kategori keluhan
        // Menggunakan raw SQL ALTER TABLE untuk kompatibilitas MariaDB 10.4.32 / MySQL
        DB::statement("ALTER TABLE `keluhan` MODIFY COLUMN `kategori` ENUM('Akun', 'Pesanan', 'Pembayaran', 'Pelayanan', 'Armada', 'Lainnya') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum tanpa nilai 'Armada'
        DB::statement("ALTER TABLE `keluhan` MODIFY COLUMN `kategori` ENUM('Akun', 'Pesanan', 'Pembayaran', 'Pelayanan', 'Lainnya') NOT NULL");

        // Kembalikan deskripsi menjadi VARCHAR(200)
        Schema::table('keluhan', function (Blueprint $table) {
            $table->string('deskripsi', 200)->change();
        });
    }
};

