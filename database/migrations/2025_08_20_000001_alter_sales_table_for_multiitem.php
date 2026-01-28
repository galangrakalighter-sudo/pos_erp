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
            // Hapus kolom yang tidak dipakai pada sistem multi-item
            if (Schema::hasColumn('sales', 'nama_pesanan')) $table->dropColumn('nama_pesanan');
            if (Schema::hasColumn('sales', 'harga_barang')) $table->dropColumn('harga_barang');
            if (Schema::hasColumn('sales', 'quantity')) $table->dropColumn('quantity');
            if (Schema::hasColumn('sales', 'kota')) $table->dropColumn('kota');
            if (Schema::hasColumn('sales', 'negara')) $table->dropColumn('negara');

            // Tambahkan kolom baru jika belum ada
            if (!Schema::hasColumn('sales', 'diskon_tipe')) $table->string('diskon_tipe')->nullable();
            if (!Schema::hasColumn('sales', 'diskon_nilai')) $table->integer('diskon_nilai')->nullable();
            if (!Schema::hasColumn('sales', 'total_quantity')) $table->integer('total_quantity')->nullable();
            if (!Schema::hasColumn('sales', 'total_diskon')) $table->integer('total_diskon')->nullable();
            if (!Schema::hasColumn('sales', 'total_harga')) $table->integer('total_harga')->nullable();

            // Pastikan kolom yang dipakai sudah nullable jika perlu
            if (Schema::hasColumn('sales', 'nama_bank')) $table->string('nama_bank')->nullable()->change();
            if (Schema::hasColumn('sales', 'id_bank')) $table->string('id_bank')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Tambahkan kembali kolom jika di-rollback
            if (!Schema::hasColumn('sales', 'nama_pesanan')) $table->string('nama_pesanan')->nullable();
            if (!Schema::hasColumn('sales', 'harga_barang')) $table->integer('harga_barang')->nullable();
            if (!Schema::hasColumn('sales', 'quantity')) $table->integer('quantity')->nullable();
            if (!Schema::hasColumn('sales', 'kota')) $table->string('kota')->nullable();
            if (!Schema::hasColumn('sales', 'negara')) $table->string('negara')->nullable();
        });
    }
};
