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
        Schema::table('stock_item_histories', function (Blueprint $table) {
            $table->string('nama_item')->nullable()->after('stock_item_id');
            $table->integer('tersedia')->default(0)->after('nama_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_item_histories', function (Blueprint $table) {
            $table->dropColumn(['nama_item', 'tersedia']);
        });
    }
};
