<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shopping Cart and Cart Items are already created in 2024_01_06_000001_create_carts_tables.php

        // Wishlist
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['customer_id', 'product_id']);
        });

        // Product Reviews
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained(); // verified purchase
            $table->integer('rating'); // 1-5
            $table->string('title')->nullable();
            $table->text('review');
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamps();
        });

        // Coupons/Discounts
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->decimal('minimum_purchase', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_count')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general'); // general, store, payment, shipping, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
