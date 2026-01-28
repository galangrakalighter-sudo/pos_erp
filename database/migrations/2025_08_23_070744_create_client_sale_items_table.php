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
        Schema::create('client_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_sale_id')->constrained('client_sales')->onDelete('cascade');
            $table->string('item_name');
            $table->string('item_sku');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('discount_percent')->default(0);
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_sale_items');
    }
};
