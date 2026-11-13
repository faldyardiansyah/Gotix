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
        Schema::table('pesanan', function (Blueprint $table) {
            // Ubah foreign key pengguna_id agar mendukung nilai NULL
            $table->dropForeign('pesanan_pengguna_id_foreign');
        });

        Schema::table('pesanan', function (Blueprint $table) {
            // 1. Ubah pengguna_id menjadi nullable
            $table->unsignedBigInteger('pengguna_id')->nullable()->change();

            // 2. Tambah kolom informasi penumpang walk-in
            $table->string('nama_penumpang', 100)->nullable()->after('pengguna_id');
            $table->string('telepon_penumpang', 20)->nullable()->after('nama_penumpang');

            // 3. Tambah kolom pencatatan operator loket
            $table->unsignedBigInteger('dibuat_oleh_operator_id')->nullable()->after('telepon_penumpang');

            // 4. Pasang kembali foreign key pengguna_id dengan ON DELETE RESTRICT
            $table->foreign('pengguna_id')
                ->references('id')
                ->on('pengguna')
                ->restrictOnDelete();

            // 5. Tambah foreign key dibuat_oleh_operator_id ke tabel pengguna dengan ON DELETE RESTRICT
            $table->foreign('dibuat_oleh_operator_id')
                ->references('id')
                ->on('pengguna')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['dibuat_oleh_operator_id']);
            $table->dropForeign(['pengguna_id']);

            $table->dropColumn([
                'nama_penumpang',
                'telepon_penumpang',
                'dibuat_oleh_operator_id',
            ]);
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->unsignedBigInteger('pengguna_id')->nullable(false)->change();

            $table->foreign('pengguna_id')
                ->references('id')
                ->on('pengguna')
                ->restrictOnDelete();
        });
    }
};

