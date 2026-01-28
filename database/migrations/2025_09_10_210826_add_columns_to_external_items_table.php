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
        Schema::table('external_items', function (Blueprint $table) {
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('item_name');
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_items', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn([
                'client_id',
                'item_name',
                'sku',
                'description',
                'quantity',
                'unit_price',
                'subtotal'
            ]);
        });
    }
};
