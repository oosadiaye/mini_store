<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Safety check: These are tenant tables that rely on 'products'.
        // If 'products' doesn't exist (e.g., running on Central DB), we must skip.
        if (!Schema::hasTable('products')) {
            return;
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->string('order_number')->unique(); 
                $table->foreignId('customer_id')->constrained();
                $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded'])->default('pending');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax', 10, 2)->default(0);
                $table->decimal('shipping', 10, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('payment_method')->nullable();
                $table->string('payment_status')->default('pending'); 
                $table->string('payment_transaction_id')->nullable();
                $table->text('customer_notes')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id']);
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained();
                $table->foreignId('product_variant_id')->nullable()->constrained(); // Now safely assumes product_variants exists from earlier migration (2024_01_02)
                $table->string('product_name');
                $table->string('variant_name')->nullable();
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->decimal('total', 10, 2);
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id']);
            });
        }

        if (!Schema::hasTable('order_shipping')) {
            Schema::create('order_shipping', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->string('address_line1');
                $table->string('address_line2')->nullable();
                $table->string('city');
                $table->string('state')->nullable();
                $table->string('postal_code');
                $table->string('country');
                $table->string('tracking_number')->nullable();
                $table->string('carrier')->nullable();
                $table->timestamp('shipped_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id']);
            });
        }
    }


    public function down(): void
    {
        Schema::dropIfExists('order_shipping');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
};
