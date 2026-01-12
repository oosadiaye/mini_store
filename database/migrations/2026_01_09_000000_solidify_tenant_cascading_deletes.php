<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * List of tables that have a tenant_id column and should cascade delete.
     */
    protected $tables = [
        'categories', 
        'products', 
        'product_variants', 
        'product_images', 
        'product_combos',
        'product_warehouse',
        'brands', 
        'coupons', 
        'carts', 
        'cart_items',
        'reviews', 
        'payment_types', 
        'product_enquiries',
        'posts', 
        'pages', 
        'page_sections', 
        'page_layouts',
        'storefront_settings', 
        'storefront_templates', 
        'banners', 
        'customers', 
        'customer_addresses', 
        'orders', 
        'order_items', 
        'order_shipping', 
        'order_returns', 
        'order_return_items',
        'incomes', 
        'expenses', 
        'journal_entries', 
        'journal_entry_lines', 
        'chart_of_accounts',
        'purchase_orders', 
        'purchase_order_items', 
        'purchase_returns', 
        'purchase_return_items',
        'suppliers', 
        'warehouses', 
        'warehouse_stocks', 
        'stock_transfers',
        'roles', 
        'notifications',
    ];

    public function up(): void
    {
        // First, clean up any orphaned records that might prevent FK creation
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                DB::table($table)
                    ->whereNotIn('tenant_id', function ($query) {
                        $query->select('slug')->from('tenants');
                    })
                    ->delete();
            }
        }

        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                // Use try-catch for each table to avoid halting on existing keys
                try {
                    Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                        try {
                            $table->dropIndex(['tenant_id']);
                        } catch (\Exception $e) {}

                        $table->foreign('tenant_id')
                            ->references('id')
                            ->on('tenants')
                            ->onDelete('cascade')
                            ->onUpdate('cascade');
                    });
                } catch (\Exception $e) {
                    echo "WARNING: Could not apply FK to $tableName: " . $e->getMessage() . "\n";
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->dropForeign([$tableName . '_tenant_id_foreign']);
                });
            }
        }
    }
};
