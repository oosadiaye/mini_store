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
        // Update purchase_orders table
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                // Modify columns to be decimal(15, 2)
                $table->decimal('subtotal', 15, 2)->change();
                $table->decimal('tax', 15, 2)->change();
                $table->decimal('shipping', 15, 2)->change();
                // Discount column might be missing, check before changing or adding
                if (Schema::hasColumn('purchase_orders', 'discount')) {
                    $table->decimal('discount', 15, 2)->change();
                }
                $table->decimal('total', 15, 2)->change();
                $table->decimal('amount_paid', 15, 2)->change();
            });
        }

        // Update purchase_order_items table
        if (Schema::hasTable('purchase_order_items')) {
            Schema::table('purchase_order_items', function (Blueprint $table) {
                // Modify columns to be decimal(15, 2)
                $table->decimal('unit_cost', 15, 2)->change();
                
                if (Schema::hasColumn('purchase_order_items', 'total_cost')) {
                    $table->decimal('total_cost', 15, 2)->change();
                }
                
                if (Schema::hasColumn('purchase_order_items', 'total')) {
                     $table->decimal('total', 15, 2)->change();
                }
            });
        }
        
        // Also update Order and OrderItems (POS/Sales) just in case
        if (Schema::hasTable('orders')) {
             Schema::table('orders', function (Blueprint $table) {
                $table->decimal('subtotal', 15, 2)->change();
                $table->decimal('tax', 15, 2)->change();
                $table->decimal('shipping', 15, 2)->change();
                if (Schema::hasColumn('orders', 'discount')) {
                    $table->decimal('discount', 15, 2)->change();
                }
                $table->decimal('total', 15, 2)->change();
             });
        }
        
        if (Schema::hasTable('order_items')) {
             Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('price', 15, 2)->change();
                $table->decimal('total', 15, 2)->change();
                if (Schema::hasColumn('order_items', 'tax_amount')) {
                    $table->decimal('tax_amount', 15, 2)->change();
                }
             });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting precision decrease is risky (data loss), usually we don't revert to smaller precision.
        // But for completeness:
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->decimal('subtotal', 10, 2)->change();
                $table->decimal('tax', 10, 2)->change();
                $table->decimal('shipping', 10, 2)->change();
                $table->decimal('discount', 10, 2)->change();
                $table->decimal('total', 10, 2)->change();
                $table->decimal('amount_paid', 10, 2)->change();
            });
        }
        // ... (Repeat for other tables if strictly needed, but deemed unnecessary for hotfix rollback)
    }
};
