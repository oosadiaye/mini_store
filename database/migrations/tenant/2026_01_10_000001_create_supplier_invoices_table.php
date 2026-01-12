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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('shipping', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'void'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });

        Schema::create('supplier_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_invoice_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->timestamps();

            $table->foreign('supplier_invoice_id')->references('id')->on('supplier_invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoice_items');
        Schema::dropIfExists('supplier_invoices');
    }
};
