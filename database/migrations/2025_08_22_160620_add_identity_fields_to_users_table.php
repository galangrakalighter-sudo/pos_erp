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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telepon')->nullable()->after('client_id');
            $table->text('alamat')->nullable()->after('telepon');
            $table->string('bank')->nullable()->after('alamat');
            $table->string('no_rekening')->nullable()->after('bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telepon', 'alamat', 'bank', 'no_rekening']);
        });
    }
};
