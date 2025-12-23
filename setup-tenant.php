<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Setting up tenant database for 'dplux'...\n\n";

// Get tenant
$tenant = \App\Models\Tenant::find('dplux');
if (!$tenant) {
    die("Tenant 'dplux' not found!\n");
}

// Initialize tenancy
tenancy()->initialize($tenant);

echo "✅ Tenant initialized\n";

// Create categories table if not exists
\Illuminate\Support\Facades\DB::statement('
    CREATE TABLE IF NOT EXISTS categories (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        parent_id BIGINT UNSIGNED NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        description TEXT NULL,
        image VARCHAR(255) NULL,
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        show_on_storefront BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
');
echo "✅ Categories table created\n";

// Create products table if not exists
\Illuminate\Support\Facades\DB::statement('
    CREATE TABLE IF NOT EXISTS products (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        category_id BIGINT UNSIGNED NULL,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        sku VARCHAR(100) NOT NULL UNIQUE,
        barcode VARCHAR(100) NULL,
        description TEXT NULL,
        short_description TEXT NULL,
        price DECIMAL(10,2) NOT NULL,
        compare_at_price DECIMAL(10,2) NULL,
        cost_price DECIMAL(10,2) NULL,
        stock_quantity INT DEFAULT 0,
        low_stock_threshold INT DEFAULT 5,
        track_inventory BOOLEAN DEFAULT TRUE,
        is_active BOOLEAN DEFAULT TRUE,
        is_featured BOOLEAN DEFAULT FALSE,
        meta_title VARCHAR(255) NULL,
        meta_description TEXT NULL,
        meta_keywords TEXT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
');
echo "✅ Products table created\n";

// Create product_images table
\Illuminate\Support\Facades\DB::statement('
    CREATE TABLE IF NOT EXISTS product_images (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        product_id BIGINT UNSIGNED NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
');
echo "✅ Product images table created\n";

// Insert sample categories
$categories = [
    ['name' => 'Electronics', 'slug' => 'electronics', 'is_active' => 1, 'show_on_storefront' => 1, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Clothing', 'slug' => 'clothing', 'is_active' => 1, 'show_on_storefront' => 1, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'Books', 'slug' => 'books', 'is_active' => 1, 'show_on_storefront' => 1, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
];

foreach ($categories as $category) {
    \Illuminate\Support\Facades\DB::table('categories')->insertOrIgnore($category);
}
echo "✅ Sample categories inserted\n";

// Get category IDs
$electronicsId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'electronics')->value('id');
$clothingId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'clothing')->value('id');

// Insert sample products
$products = [
    [
        'name' => 'Wireless Headphones',
        'slug' => 'wireless-headphones',
        'sku' => 'SKU-WH001',
        'category_id' => $electronicsId,
        'price' => 99.99,
        'compare_at_price' => 129.99,
        'cost_price' => 50.00,
        'short_description' => 'Premium wireless headphones with noise cancellation',
        'description' => 'Experience superior sound quality',
        'stock_quantity' => 50,
        'low_stock_threshold' => 10,
        'track_inventory' => 1,
        'is_active' => 1,
        'is_featured' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Smart Watch',
        'slug' => 'smart-watch',
        'sku' => 'SKU-SW001',
        'category_id' => $electronicsId,
        'price' => 199.99,
        'compare_at_price' => 249.99,
        'cost_price' => 100.00,
        'short_description' => 'Feature-rich smartwatch',
        'description' => 'Stay connected and healthy',
        'stock_quantity' => 30,
        'low_stock_threshold' => 5,
        'track_inventory' => 1,
        'is_active' => 1,
        'is_featured' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Cotton T-Shirt',
        'slug' => 'cotton-t-shirt',
        'sku' => 'SKU-TS001',
        'category_id' => $clothingId,
        'price' => 24.99,
        'cost_price' => 10.00,
        'short_description' => 'Comfortable cotton t-shirt',
        'description' => 'Classic cotton t-shirt',
        'stock_quantity' => 100,
        'low_stock_threshold' => 20,
        'track_inventory' => 1,
        'is_active' => 1,
        'is_featured' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Denim Jeans',
        'slug' => 'denim-jeans',
        'sku' => 'SKU-DJ001',
        'category_id' => $clothingId,
        'price' => 59.99,
        'compare_at_price' => 79.99,
        'cost_price' => 30.00,
        'short_description' => 'Premium denim jeans',
        'description' => 'High-quality denim jeans',
        'stock_quantity' => 8,
        'low_stock_threshold' => 10,
        'track_inventory' => 1,
        'is_active' => 1,
        'is_featured' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

foreach ($products as $product) {
    \Illuminate\Support\Facades\DB::table('products')->insertOrIgnore($product);
}
echo "✅ Sample products inserted\n";

tenancy()->end();

echo "\n🎉 Setup complete!\n";
echo "\nYour dashboard should now show:\n";
echo "- Total Products: 4\n";
echo "- Active Products: 4\n";
echo "- Categories: 3\n";
echo "- Low Stock Products: 1 (Denim Jeans - 8 units)\n";
echo "\n✨ Refresh your dashboard at: http://dplux.localhost:8000/admin/dashboard\n";
echo "✨ View storefront at: http://dplux.localhost:8000\n";
