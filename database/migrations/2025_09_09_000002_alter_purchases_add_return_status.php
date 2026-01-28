<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambahkan nilai 'returned' ke enum status pada tabel purchases
        if (Schema::hasTable('purchases')) {
            // MySQL enum alter
            DB::statement("ALTER TABLE purchases MODIFY COLUMN status ENUM('pending','approved','rejected','completed','returned') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('purchases')) {
            // Kembalikan tanpa 'returned'
            DB::statement("ALTER TABLE purchases MODIFY COLUMN status ENUM('pending','approved','rejected','completed') DEFAULT 'pending'");
        }
    }
};





