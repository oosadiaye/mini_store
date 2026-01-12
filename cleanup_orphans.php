<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = [
    'categories', 'products', 'product_variants', 'product_images', 
    'product_combos', 'product_warehouse', 'brands', 'coupons', 
    'carts', 'cart_items', 'reviews', 'payment_types', 
    'product_enquiries', 'posts', 'pages', 'page_sections', 
    'page_layouts', 'storefront_settings', 'storefront_templates', 
    'banners', 'customers', 'customer_addresses', 'orders', 
    'order_items', 'order_shipping', 'order_returns', 
    'order_return_items', 'incomes', 'expenses', 'journal_entries', 
    'journal_entry_lines', 'chart_of_accounts', 'purchase_orders', 
    'purchase_order_items', 'purchase_returns', 'purchase_return_items', 
    'suppliers', 'warehouses', 'warehouse_stocks', 'stock_transfers', 
    'roles', 'notifications', 'subscription_payments', 'subscription_transactions',
    'payment_gateways', 'tenant_user_impersonation_tokens', 'store_configs', 'domains'
];

echo "Cleaning up orphaned records...\n";

$tenantIds = DB::table('tenants')->pluck('id')->toArray();

foreach ($tables as $table) {
    if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
        $count = DB::table($table)->whereNotIn('tenant_id', $tenantIds)->count();
        if ($count > 0) {
            DB::table($table)->whereNotIn('tenant_id', $tenantIds)->delete();
            echo "Deleted $count orphans from $table.\n";
        }
    }
}

echo "Cleanup complete.\n";
