<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Customers
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('customer_addresses')) {
            Schema::create('customer_addresses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained()->onDelete('cascade');
                $table->string('address_type')->default('shipping'); // shipping, billing
                $table->string('address_line1');
                $table->string('address_line2')->nullable();
                $table->string('city');
                $table->string('state')->nullable();
                $table->string('postal_code');
                $table->string('country');
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        // Orders
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->foreignId('customer_id')->constrained();
                $table->enum('status', ['pending', 'processing', 'completed', 'cancelled', 'refunded'])->default('pending');
                $table->decimal('subtotal', 10, 2);
                $table->decimal('tax', 10, 2)->default(0);
                $table->decimal('shipping', 10, 2)->default(0);
                $table->decimal('discount', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->string('payment_method')->nullable();
                $table->string('payment_status')->default('pending'); // pending, paid, failed
                $table->string('payment_transaction_id')->nullable();
                $table->text('customer_notes')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained();
                $table->foreignId('product_variant_id')->nullable()->constrained();
                $table->string('product_name');
                $table->string('variant_name')->nullable();
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->decimal('total', 10, 2);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('order_shipping')) {
            Schema::create('order_shipping', function (Blueprint $table) {
                $table->id();
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
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_shipping');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
    }
};
