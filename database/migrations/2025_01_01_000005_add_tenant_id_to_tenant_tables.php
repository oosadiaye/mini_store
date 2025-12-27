<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
        'cart_items', // If separate table
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
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'tenant_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('tenant_id')->nullable()->after('id')->index();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                 Schema::table($tableName, function (Blueprint $table) {
                    $table->dropIndex(['tenant_id']);
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};
