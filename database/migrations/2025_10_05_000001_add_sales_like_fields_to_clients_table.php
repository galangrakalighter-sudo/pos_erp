<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->enum('diskon_tipe', ['rupiah','persen'])->nullable()->after('nama_sales');
            $table->decimal('diskon_nilai', 15, 2)->default(0)->after('diskon_tipe');
            $table->enum('diskon_ball_tipe', ['rupiah','persen'])->nullable()->after('diskon_nilai');
            $table->decimal('diskon_ball_nilai', 15, 2)->default(0)->after('diskon_ball_tipe');
            $table->string('nama_ekspedisi')->nullable()->after('diskon_ball_nilai');
            $table->decimal('ongkir', 15, 2)->default(0)->after('nama_ekspedisi');
            $table->text('notes')->nullable()->after('ongkir');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['diskon_tipe','diskon_nilai','diskon_ball_tipe','diskon_ball_nilai','nama_ekspedisi','ongkir','notes']);
        });
    }
};






























