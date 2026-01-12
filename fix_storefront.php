<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreConfig;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = \App\Models\Tenant::first();
echo "Tenant: " . ($tenant ? $tenant->id : 'None') . "\n";

// Try to access StoreConfig directly
try {
    $check = StoreConfig::first();
    echo "StoreConfig Access: " . ($check ? 'Success' : 'Null') . "\n";
} catch (\Exception $e) {
    echo "StoreConfig Schema Error: " . $e->getMessage() . "\n";
    // If table doesn't exist, we might be in the wrong DB. 
    // But let's proceed and see if we can just find categories.
}

// 1. Find categories with products
$categories = Category::whereHas('products')->get();
echo "Found " . $categories->count() . " categories with products.\n";

$validIds = [];
foreach ($categories as $category) {
    // Make sure they are active and visible
    $category->update([
        'is_active' => true,
        'is_visible_online' => true
    ]);
    $validIds[] = $category->id;
    echo " - Added Category: {$category->name} (ID: {$category->id})\n";
}

if (empty($validIds)) {
    echo "No categories with products found. Creating one...\n";
    $cat = Category::create([
        'name' => 'Featured Collection',
        'slug' => 'featured-collection',
        'is_active' => true,
        'is_visible_online' => true,
        'sort_order' => 1
    ]);
    
    // Assign random products
    $products = Product::limit(5)->get();
    foreach($products as $p) {
        $p->update(['category_id' => $cat->id]);
    }
    $validIds[] = $cat->id;
    echo " - Created and populated Category: {$cat->name} (ID: {$cat->id})\n";
}

// 2. Update Store Config
$config = StoreConfig::first();
if (!$config) {
    $config = StoreConfig::create([
        'store_name' => $tenant->name ?? 'My Store',
        'brand_color' => '#0A2540',
        'selected_categories' => $validIds
    ]);
} else {
    $config->selected_categories = $validIds;
    $config->save();
}

echo "Updated Store Config with selected categories: " . json_encode($validIds) . "\n";
echo "Done.\n";
