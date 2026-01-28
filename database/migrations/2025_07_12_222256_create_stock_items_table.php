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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('sku');
            $table->string('kondisi'); // Baru/Lama
            $table->string('lokasi');
            $table->integer('tersedia');
            $table->integer('disimpan');
            $table->integer('harga');
            $table->date('diperbaharui');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
