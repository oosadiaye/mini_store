<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('sku')->unique();
                $table->text('description')->nullable();
                $table->text('short_description')->nullable();
                $table->decimal('price', 10, 2);
                $table->decimal('cost_price', 10, 2)->nullable();
                $table->decimal('compare_at_price', 10, 2)->nullable();
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
                $table->string('barcode')->nullable();
                $table->boolean('track_inventory')->default(true);
                $table->integer('stock_quantity')->default(0);
                $table->integer('low_stock_threshold')->default(10);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('image_path');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('name'); // e.g., "Size: Large, Color: Red"
                $table->string('sku')->unique();
                $table->decimal('price', 10, 2);
                $table->integer('stock_quantity')->default(0);
                $table->string('barcode')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
    }
};
