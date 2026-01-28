<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_item_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->string('action');
            $table->json('changes')->nullable();
            $table->string('user')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_item_histories');
    }
}; 