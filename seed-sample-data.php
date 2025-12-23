<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Initialize tenant
$tenant = \App\Models\Tenant::find('dplux');
tenancy()->initialize($tenant);

echo "Creating sample data for dplux tenant...\n\n";

// Create categories
$categories = [
    ['name' => 'Electronics', 'slug' => 'electronics', 'is_active' => true, 'show_on_storefront' => true, 'sort_order' => 1],
    ['name' => 'Clothing', 'slug' => 'clothing', 'is_active' => true, 'show_on_storefront' => true, 'sort_order' => 2],
    ['name' => 'Books', 'slug' => 'books', 'is_active' => true, 'show_on_storefront' => true, 'sort_order' => 3],
];

foreach ($categories as $category) {
    $category['created_at'] = now();
    $category['updated_at'] = now();
    \Illuminate\Support\Facades\DB::table('categories')->insert($category);
}
echo "✅ Created 3 categories\n";

// Get category IDs
$electronicsId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'electronics')->value('id');
$clothingId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'clothing')->value('id');
$booksId = \Illuminate\Support\Facades\DB::table('categories')->where('slug', 'books')->value('id');

// Create products
$products = [
    [
        'name' => 'Wireless Headphones',
        'slug' => 'wireless-headphones',
        'sku' => 'SKU-' . strtoupper(substr(md5(rand()), 0, 8)),
        'category_id' => $electronicsId,
        'price' => 99.99,
        'compare_at_price' => 129.99,
        'cost_price' => 50.00,
        'short_description' => 'Premium wireless headphones with noise cancellation',
        'description' => 'Experience superior sound quality with our premium wireless headphones featuring active noise cancellation, 30-hour battery life, and comfortable over-ear design.',
        'stock_quantity' => 50,
        'low_stock_threshold' => 10,
        'track_inventory' => true,
        'is_active' => true,
        'is_featured' => true,
    ],
    [
        'name' => 'Smart Watch',
        'slug' => 'smart-watch',
        'sku' => 'SKU-' . strtoupper(substr(md5(rand()), 0, 8)),
        'category_id' => $electronicsId,
        'price' => 199.99,
        'compare_at_price' => 249.99,
        'cost_price' => 100.00,
        'short_description' => 'Feature-rich smartwatch with health tracking',
        'description' => 'Stay connected and healthy with our advanced smartwatch featuring heart rate monitoring, GPS, and 7-day battery life.',
        'stock_quantity' => 30,
        'low_stock_threshold' => 5,
        'track_inventory' => true,
        'is_active' => true,
        'is_featured' => true,
    ],
    [
        'name' => 'Cotton T-Shirt',
        'slug' => 'cotton-t-shirt',
        'sku' => 'SKU-' . strtoupper(substr(md5(rand()), 0, 8)),
        'category_id' => $clothingId,
        'price' => 24.99,
        'compare_at_price' => null,
        'cost_price' => 10.00,
        'short_description' => 'Comfortable 100% cotton t-shirt',
        'description' => 'Classic cotton t-shirt perfect for everyday wear. Available in multiple colors and sizes.',
        'stock_quantity' => 100,
        'low_stock_threshold' => 20,
        'track_inventory' => true,
        'is_active' => true,
        'is_featured' => false,
    ],
    [
        'name' => 'Denim Jeans',
        'slug' => 'denim-jeans',
        'sku' => 'SKU-' . strtoupper(substr(md5(rand()), 0, 8)),
        'category_id' => $clothingId,
        'price' => 59.99,
        'compare_at_price' => 79.99,
        'cost_price' => 30.00,
        'short_description' => 'Premium denim jeans with perfect fit',
        'description' => 'High-quality denim jeans with modern fit and durable construction.',
        'stock_quantity' => 8,
        'low_stock_threshold' => 10,
        'track_inventory' => true,
        'is_active' => true,
        'is_featured' => true,
    ],
    [
        'name' => 'Programming Guide',
        'slug' => 'programming-guide',
        'sku' => 'SKU-' . strtoupper(substr(md5(rand()), 0, 8)),
        'category_id' => $booksId,
        'price' => 39.99,
        'compare_at_price' => null,
        'cost_price' => 15.00,
        'short_description' => 'Complete guide to modern programming',
        'description' => 'Learn programming from scratch with this comprehensive guide covering all major languages and frameworks.',
        'stock_quantity' => 25,
        'low_stock_threshold' => 5,
        'track_inventory' => true,
        'is_active' => true,
        'is_featured' => false,
    ],
];

foreach ($products as $product) {
    $product['created_at'] = now();
    $product['updated_at'] = now();
    \Illuminate\Support\Facades\DB::table('products')->insert($product);
}
echo "✅ Created 5 products\n";

echo "\n🎉 Sample data created successfully!\n";
echo "\nYour dashboard should now show:\n";
echo "- Total Products: 5\n";
echo "- Active Products: 5\n";
echo "- Categories: 3\n";
echo "- Low Stock Products: 1 (Denim Jeans)\n";
echo "\nRefresh your dashboard to see the data!\n";
