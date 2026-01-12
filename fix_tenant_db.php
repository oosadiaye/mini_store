<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Tenant Database for 'dplux'...\n\n";

echo "🔧 Fixing Tenant Data in Central Database for 'dplux'...\n\n";

// 1. Run Migrations
echo "1. Running migrations...\n";
\Illuminate\Support\Facades\Artisan::call('migrate', [
    '--force'   => true,
]);
echo \Illuminate\Support\Facades\Artisan::output() . "\n";
echo "✅ Migrations completed.\n\n";

// 2. Initialize Tenant Context
echo "2. Initializing Tenant 'dplux'...\n";
$tenant = \App\Models\Tenant::where('slug', 'dplux')->first();
if (!$tenant) {
    // Re-create tenant if missing
    $tenant = \App\Models\Tenant::create([
        'id' => 'dplux',
        'name' => 'DPlux Store',
        'slug' => 'dplux',
        'email' => 'admin@dplux.com',
        'is_active' => true,
        'data' => [
            'currency_symbol' => '₦',
            'currency_code' => 'NGN'
        ]
    ]);
}
app()->instance('tenant', $tenant);
config(['app.tenant_id' => $tenant->id]);
echo "✅ Tenant initialized.\n\n";

echo "3. Seeding Admin User & Sample Data...\n";

// Clear existing data for a fresh seed
\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');

// 1. Tables with tenant_id
$tablesWithTenantId = [
    'journal_entries', 'stock_movements', 'purchase_returns', 'purchase_return_items', 
    'purchase_order_items', 'purchase_orders', 'products', 'categories', 
    'suppliers', 'warehouses', 'users', 'chart_of_accounts',
    'order_returns', 'order_return_items', 'orders', 'order_items'
];

foreach ($tablesWithTenantId as $table) {
    if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'tenant_id')) {
        \Illuminate\Support\Facades\DB::table($table)->where('tenant_id', $tenant->id)->delete();
    }
}

// 2. Tables without tenant_id (delete based on parent)
\Illuminate\Support\Facades\DB::table('journal_entry_lines')
    ->whereIn('journal_entry_id', function($query) use ($tenant) {
        $query->select('id')->from('journal_entries')->where('tenant_id', $tenant->id);
    })->delete();

\Illuminate\Support\Facades\DB::table('warehouse_stock')
    ->whereIn('warehouse_id', function($query) use ($tenant) {
        $query->select('id')->from('warehouses')->where('tenant_id', $tenant->id);
    })->delete();

\Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "✅ Existing tenant data cleared.\n";

// Create Admin User
\Illuminate\Support\Facades\DB::table('users')->updateOrInsert(
    ['email' => 'osadiaye4real@gmail.com'],
    [
        'tenant_id' => $tenant->id,
        'name' => 'Jacob Osadiaye',
        'password' => bcrypt('12345678'),
        'role' => 'admin',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]
);
echo "✅ Admin user created (osadiaye4real@gmail.com)\n";

// Create Categories
$categories = [
    ['name' => 'Electronics', 'slug' => 'electronics', 'is_active' => 1, 'show_on_storefront' => 1, 'sort_order' => 1],
    ['name' => 'Clothing', 'slug' => 'clothing', 'is_active' => 1, 'show_on_storefront' => 1, 'sort_order' => 2],
    ['name' => 'Books', 'slug' => 'books', 'is_active' => 1, 'show_on_storefront' => 1, 'sort_order' => 3],
];

foreach ($categories as $cat) {
    \Illuminate\Support\Facades\DB::table('categories')->updateOrInsert(
        ['slug' => $cat['slug'], 'tenant_id' => $tenant->id],
        array_merge($cat, ['created_at' => now(), 'updated_at' => now()])
    );
}
echo "✅ Categories seeded\n";

// Get Category IDs
$electronicsId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'electronics')->where('tenant_id', $tenant->id)->value('id');
$clothingId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'clothing')->where('tenant_id', $tenant->id)->value('id');

// Create Products
$products = [
    [
        'name' => 'Wireless Headphones',
        'slug' => 'wireless-headphones',
        'sku' => 'SKU-WH001',
        'category_id' => $electronicsId,
        'price' => 99.99,
        'stock_quantity' => 50,
        'track_inventory' => 1,
        'is_active' => 1,
        'is_featured' => 1,
    ],
    [
        'name' => 'Denim Jeans',
        'slug' => 'denim-jeans',
        'sku' => 'SKU-DJ001',
        'category_id' => $clothingId,
        'price' => 59.99,
        'stock_quantity' => 8, // Low stock
        'track_inventory' => 1,
        'is_active' => 1,
        'is_featured' => 1,
    ],
];

foreach ($products as $prod) {
    \Illuminate\Support\Facades\DB::table('products')->updateOrInsert(
        ['sku' => $prod['sku'], 'tenant_id' => $tenant->id],
        array_merge($prod, ['created_at' => now(), 'updated_at' => now()])
    );
}
echo "✅ Products seeded\n\n";

// Create Warehouses
$warehouses = [
    ['name' => 'Main Warehouse', 'code' => 'WH001', 'address' => '123 Logistics Way', 'city' => 'Lagos', 'is_active' => 1],
    ['name' => 'Branch Store', 'code' => 'WH002', 'address' => '456 Retail St', 'city' => 'Abuja', 'is_active' => 1],
];

foreach ($warehouses as $wh) {
    \Illuminate\Support\Facades\DB::table('warehouses')->updateOrInsert(
        ['code' => $wh['code'], 'tenant_id' => $tenant->id],
        array_merge($wh, ['created_at' => now(), 'updated_at' => now()])
    );
}
echo "✅ Warehouses seeded\n";

// Create Suppliers
$suppliers = [
    ['name' => 'Global Tech Solutions', 'company_name' => 'Global Tech Ltd', 'email' => 'sales@globaltech.com', 'is_active' => 1],
    ['name' => 'Fashion Hub', 'company_name' => 'Fashion Hub Inc', 'email' => 'orders@fashionhub.com', 'is_active' => 1],
];

foreach ($suppliers as $sup) {
    \Illuminate\Support\Facades\DB::table('suppliers')->updateOrInsert(
        ['name' => $sup['name'], 'tenant_id' => $tenant->id],
        array_merge($sup, ['created_at' => now(), 'updated_at' => now()])
    );
}
echo "✅ Suppliers seeded\n";

// Create Chart of Accounts
$accounts = [
    ['account_code' => '1200', 'account_name' => 'Inventory Asset', 'account_type' => 'asset', 'sub_ledger_type' => null],
    ['account_code' => '2020', 'account_name' => 'GR/IR Clearing', 'account_type' => 'liability', 'sub_ledger_type' => null],
    ['account_code' => '2000', 'account_name' => 'Accounts Payable', 'account_type' => 'liability', 'sub_ledger_type' => 'supplier'],
    ['account_code' => '1300', 'account_name' => 'Input Tax', 'account_type' => 'asset', 'sub_ledger_type' => null],
    ['account_code' => '5100', 'account_name' => 'Freight In', 'account_type' => 'expense', 'sub_ledger_type' => null],
    ['account_code' => '5200', 'account_name' => 'Purchase Discounts', 'account_type' => 'expense', 'sub_ledger_type' => null],
    ['account_code' => '1010', 'account_name' => 'Cash at Hand', 'account_type' => 'asset', 'sub_ledger_type' => null],
];

foreach ($accounts as $acc) {
    \Illuminate\Support\Facades\DB::table('chart_of_accounts')->updateOrInsert(
        ['account_code' => $acc['account_code'], 'tenant_id' => $tenant->id],
        array_merge($acc, ['created_at' => now(), 'updated_at' => now()])
    );
}
echo "✅ Chart of Accounts seeded\n\n";

echo "🎉 REPAIR COMPLETE! Attempt to login now.\n";
