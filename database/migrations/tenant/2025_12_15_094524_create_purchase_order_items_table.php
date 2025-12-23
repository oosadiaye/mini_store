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
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id('id'); // UUID
                $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
                $table->uuid('product_id');
                $table->string('variant_sku')->nullable(); // Optional if dealing with variants via SKU string or ID
                $table->integer('quantity_ordered');
                $table->integer('quantity_received')->default(0);
                $table->decimal('unit_cost', 10, 2); // Cost per item at time of order
                $table->decimal('total_cost', 10, 2);
                $table->timestamps();
    
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
