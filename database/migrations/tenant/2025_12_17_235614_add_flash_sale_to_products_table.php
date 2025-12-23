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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'is_flash_sale')) {
                $table->boolean('is_flash_sale')->default(false)->after('is_featured');
            }
            if (!Schema::hasColumn('products', 'flash_sale_price')) {
                $table->decimal('flash_sale_price', 10, 2)->nullable()->after('is_flash_sale');
            }
            if (!Schema::hasColumn('products', 'flash_sale_start')) {
                $table->timestamp('flash_sale_start')->nullable()->after('flash_sale_price');
            }
            if (!Schema::hasColumn('products', 'flash_sale_end')) {
                $table->timestamp('flash_sale_end')->nullable()->after('flash_sale_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_flash_sale', 'flash_sale_price', 'flash_sale_start', 'flash_sale_end']);
        });
    }
};
