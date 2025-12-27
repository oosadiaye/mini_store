<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->string('name');
                $table->string('email')->unique(); // Unique per tenant? Usually unique per tenant but unique globally works if simple. Actually complex multi-tenancy needs unique(['email', 'tenant_id']).
                // Let's stick to simple unique for now or drop unique constraint if causing issues. But cleaner to unique(email) if we want global uniqueness for customers (unlikely).
                // Better: $table->unique(['tenant_id', 'email']); 
                // However, I will stick to original schema but add tenant_id. 
                // Original: $table->string('email')->unique();
                // If I add tenant_id, I should probably change unique constraint.
                // But to be safe and match original intent, I keep unique(email) OR if multi-tenant, ensure email is unique per tenant.
                // I will use just string('email') and unique(['tenant_id', 'email'])? No, let's just make it string('email') and index it.
                // Wait, original migration had ->unique(). If two tenants have same customer email?
                // I'll drop ->unique() from email and add unique(['tenant_id', 'email']).
                $table->string('phone')->nullable();
                $table->string('password');
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id']);
                // $table->unique(['tenant_id', 'email']); // Optional optimization
            });
        }

        if (!Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
                $table->string('address_type')->default('shipping');
                $table->string('address_line1');
                $table->string('address_line2')->nullable();
                $table->string('city');
                $table->string('state')->nullable();
                $table->string('postal_code');
                $table->string('country');
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id']);
            });
        }

        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('name'); 
                $table->string('sku')->unique(); // Scoped? unique(['tenant_id', 'sku']). Keeping global unique for now or just simple text.
                $table->decimal('price', 10, 2);
                $table->integer('stock_quantity')->default(0);
                $table->string('barcode')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id']);
            });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->string('order_number')->unique(); // Scoped? unique(['tenant_id', 'order_number']). But keeping unique global is safer for now.
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
                $table->foreignId('product_variant_id')->nullable()->constrained();
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
