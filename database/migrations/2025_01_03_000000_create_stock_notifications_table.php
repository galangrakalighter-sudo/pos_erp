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
        Schema::create('stock_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // admin atau client yang menerima notif
            $table->unsignedBigInteger('stock_item_id'); // ID dari stock item
            $table->enum('item_type', ['admin', 'client']); // tipe item (admin stock atau client stock)
            $table->enum('notification_type', ['low_stock', 'out_of_stock'])->default('low_stock');
            $table->string('item_name'); // nama item untuk display
            $table->string('sku'); // SKU item
            $table->integer('current_stock');
            $table->integer('threshold');
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('dismissed_at')->nullable();
            $table->unsignedBigInteger('dismissed_by')->nullable(); // user yang dismiss
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'is_dismissed']);
            $table->index(['stock_item_id', 'item_type']);
            $table->index(['is_dismissed', 'created_at']);
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dismissed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_notifications');
    }
};
