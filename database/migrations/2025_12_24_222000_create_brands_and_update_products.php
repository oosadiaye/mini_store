<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('tenant_id');
                $table->string('name');
                $table->string('slug')->nullable();
                $table->string('logo')->nullable();
                $table->string('url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id']);
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('stock_quantity');
            }
            if (!Schema::hasColumn('products', 'flash_sale_price')) {
                 $table->decimal('flash_sale_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'flash_sale_end_date')) {
                $table->timestamp('flash_sale_end_date')->nullable()->after('flash_sale_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand_id', 'expiry_date', 'flash_sale_price', 'flash_sale_end_date']);
        });
        Schema::dropIfExists('brands');
    }
};
