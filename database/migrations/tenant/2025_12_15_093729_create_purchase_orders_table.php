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
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id('id'); // UUID
                $table->uuid('supplier_id');
                $table->uuid('warehouse_id');
                $table->string('status')->default('draft'); // draft, ordered, received, cancelled
                $table->date('order_date')->nullable();
                $table->date('expected_delivery_date')->nullable();
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
    
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
