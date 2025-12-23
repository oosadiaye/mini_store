<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Tenant Database for 'dplux'...\n\n";

// 1. Re-create the database
try {
    echo "1. Re-creating database 'tenantdplux'...\n";
    \Illuminate\Support\Facades\DB::connection('mysql')->statement('DROP DATABASE IF EXISTS tenantdplux');
    \Illuminate\Support\Facades\DB::connection('mysql')->statement('CREATE DATABASE tenantdplux');
    echo "✅ Database recreated.\n\n";
} catch (\Exception $e) {
    die("❌ Failed to create database: " . $e->getMessage() . "\n");
}

// 2. Run Migrations
echo "2. Running migrations for tenant 'dplux'...\n";
\Illuminate\Support\Facades\Artisan::call('tenants:migrate', [
    '--tenants' => ['dplux'],
    '--force'   => true,
]);
echo \Illuminate\Support\Facades\Artisan::output() . "\n";
echo "✅ Migrations completed.\n\n";

// 3. Initialize Tenant & Seed Data
echo "3. Seeding Admin User & Sample Data...\n";
$tenant = \App\Models\Tenant::find('dplux');
tenancy()->initialize($tenant);

// Create Admin User
\Illuminate\Support\Facades\DB::table('users')->updateOrInsert(
    ['email' => 'osadiaye4real@gmail.com'],
    [
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
        ['slug' => $cat['slug']],
        array_merge($cat, ['created_at' => now(), 'updated_at' => now()])
    );
}
echo "✅ Categories seeded\n";

// Get Category IDs
$electronicsId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'electronics')->value('id');
$clothingId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'clothing')->value('id');

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
        ['sku' => $prod['sku']],
        array_merge($prod, ['created_at' => now(), 'updated_at' => now()])
    );
}
echo "✅ Products seeded\n\n";

echo "🎉 REPAIR COMPLETE! Attempt to login now.\n";
