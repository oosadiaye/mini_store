<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix Users table (Add tenant_id FK if missing cascade)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });

        // Fix Accounting Tables
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('journal_entry_lines', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });

        // Fix Inventory Tables
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['created_by']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['product_id']);
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        
        // Ensure other tenant tables have FK for cleanup
        $tenantTables = [
            'warehouses', 'suppliers', 'incomes', 'expenses', 'journal_entries', 
            'stock_movements', 'notifications', 'orders', 'products', 'categories',
            'product_enquiries', 'brands', 'page_layouts', 'custom_domain_requests'
        ];

        foreach ($tenantTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                // Check if FK exists already? Eloquent doesn't make it easy.
                // We'll just try to add it, but wrap in try-catch to avoid errors if already present
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                    });
                } catch (\Exception $e) {
                    // Already exists or other issue
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not easily reversible without knowing previous state of each FK
    }
};
