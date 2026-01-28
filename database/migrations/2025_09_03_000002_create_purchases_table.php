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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('purchase_number')->unique();
            $table->string('supplier_name');
            $table->string('supplier_contact')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->date('purchase_date');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
