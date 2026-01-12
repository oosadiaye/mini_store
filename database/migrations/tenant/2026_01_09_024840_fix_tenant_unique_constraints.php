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
        // categories is already fixed based on dump

        // Fix Products
        Schema::table('products', function (Blueprint $table) {
            // Drop existing global unique indexes
            // Utilising array syntax to let Laravel resolve the standard index name (e.g. products_slug_unique)
            $table->dropUnique(['slug']); 
            $table->dropUnique(['sku']);
            
            // Add composite unique indexes
            $table->unique(['tenant_id', 'slug']);
            $table->unique(['tenant_id', 'sku']);
        });

        // Fix Product Variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique('product_variants_sku_unique');
            $table->unique(['tenant_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'sku']);
            $table->unique('sku', 'product_variants_sku_unique');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'sku']);
            $table->dropUnique(['tenant_id', 'slug']);
            $table->unique('sku', 'sku');
            $table->unique('slug', 'slug');
        });
    }
};
