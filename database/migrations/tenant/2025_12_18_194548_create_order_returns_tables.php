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
        // Sales Order Returns
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('requested'); // requested, approved, received, refunded, rejected
            $table->string('return_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('order_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_return_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_returned');
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('condition')->nullable(); // new, damaged, open_box
            $table->boolean('restock_inventory')->default(false);
            $table->timestamps();
        });

        // Purchase Order Returns
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('draft'); // draft, sent, completed
            $table->text('admin_notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_returned');
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('return_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('order_return_items');
        Schema::dropIfExists('order_returns');
    }
};
