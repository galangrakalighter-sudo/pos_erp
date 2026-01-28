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
        Schema::create('client_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->integer('total_items');
            $table->integer('total_quantity');
            $table->integer('total_amount');
            $table->string('payment_method');
            $table->integer('amount_paid');
            $table->integer('change_amount');
            $table->string('status')->default('completed');
            $table->date('sale_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_sales');
    }
};
