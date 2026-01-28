<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'nama_sales')) {
                $table->string('nama_sales')->nullable()->after('id_pesanan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'nama_sales')) {
                $table->dropColumn('nama_sales');
            }
        });
    }
};



