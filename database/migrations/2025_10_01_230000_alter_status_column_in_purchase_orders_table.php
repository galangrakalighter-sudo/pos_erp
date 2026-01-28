<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom enum status menjadi string agar bisa menampung 'rejected'
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke enum lama (fallback)
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'received', 'cancelled'])->default('pending')->change();
        });
    }
};



