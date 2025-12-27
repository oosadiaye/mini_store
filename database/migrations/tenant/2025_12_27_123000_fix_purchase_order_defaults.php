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
        // Restore defaults for purchase_orders
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->decimal('subtotal', 15, 2)->default(0)->change();
                $table->decimal('tax', 15, 2)->default(0)->change();
                $table->decimal('shipping', 15, 2)->default(0)->change();
                // Check discount again
                if (Schema::hasColumn('purchase_orders', 'discount')) {
                    $table->decimal('discount', 15, 2)->default(0)->change();
                }
                $table->decimal('total', 15, 2)->default(0)->change();
                $table->decimal('amount_paid', 15, 2)->default(0)->change();
            });
        }

        // Restore defaults for purchase_order_items
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                $table->decimal('unit_cost', 15, 2)->default(0)->change();
                
                if (Schema::hasColumn('purchase_order_items', 'total_cost')) {
                    $table->decimal('total_cost', 15, 2)->default(0)->change();
                }
                
                if (Schema::hasColumn('purchase_order_items', 'total')) {
                     $table->decimal('total', 15, 2)->default(0)->change();
                }
            });
        }
        
        // Restore defaults for orders (POS)
        if (Schema::hasTable('orders')) {
             Schema::table('orders', function (Blueprint $table) {
                $table->decimal('subtotal', 15, 2)->default(0)->change();
                $table->decimal('tax', 15, 2)->default(0)->change();
                $table->decimal('shipping', 15, 2)->default(0)->change();
                if (Schema::hasColumn('orders', 'discount')) {
                    $table->decimal('discount', 15, 2)->default(0)->change();
                }
                $table->decimal('total', 15, 2)->default(0)->change();
             });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert defaults usually
    }
};
