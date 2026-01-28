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
        Schema::table('sales', function (Blueprint $table) {
            // Hapus kolom bank
            $table->dropColumn(['nama_bank', 'id_bank']);
            
            // Tambah kolom telepon dan alamat
            $table->string('telepon')->nullable()->after('jenis_transaksi');
            $table->text('alamat')->nullable()->after('telepon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Kembalikan kolom bank
            $table->string('nama_bank')->nullable()->after('jenis_transaksi');
            $table->string('id_bank')->nullable()->after('nama_bank');
            
            // Hapus kolom telepon dan alamat
            $table->dropColumn(['telepon', 'alamat']);
        });
    }
};
