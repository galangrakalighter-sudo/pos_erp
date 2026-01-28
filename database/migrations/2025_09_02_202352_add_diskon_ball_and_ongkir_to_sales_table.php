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
            // Tambah field untuk diskon ball
            $table->enum('diskon_ball_tipe', ['rupiah', 'persen'])->nullable()->after('diskon_nilai');
            $table->decimal('diskon_ball_nilai', 10, 2)->default(0)->after('diskon_ball_tipe');
            
            // Tambah field untuk ongkir
            $table->string('nama_ekspedisi')->nullable()->after('diskon_ball_nilai');
            $table->decimal('ongkir', 10, 2)->default(0)->after('nama_ekspedisi');
            
            // Tambah field untuk total diskon ball dan total ongkir
            $table->decimal('total_diskon_ball', 10, 2)->default(0)->after('ongkir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'diskon_ball_tipe',
                'diskon_ball_nilai', 
                'nama_ekspedisi',
                'ongkir',
                'total_diskon_ball'
            ]);
        });
    }
};
