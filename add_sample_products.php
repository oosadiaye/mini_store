<?php
use App\Models\Product;
use App\Models\Category;
use App\Models\Tenant;

// Ensure we are in the GIG tenant context if possible, otherwise find it
$tenant = Tenant::where('slug', 'GIG')->first();
if (!$tenant) {
    echo "Tenant GIG not found\n";
    exit;
}

$tenant->run(function() {
    $categories = [
        'Essentials' => 'Electronics',
        'Lifestyle' => 'General',
        'Apparel' => 'T-Shirts'
    ];

    foreach ($categories as $catName => $niche) {
        $category = Category::firstOrCreate(['name' => $catName]);
        
        // Add 2 products per category
        for ($i = 1; $i <= 2; $i++) {
            Product::updateOrCreate(
                ['name' => "Premium {$catName} Item $i"],
                [
                    'category_id' => $category->id,
                    'price' => rand(5000, 50000),
                    'description' => "A high-end, premium quality {$catName} product designed for excellence.",
                    'is_featured' => true,
                    'is_active' => true,
                    'available_stock' => rand(2, 20),
                    'track_inventory' => true
                ]
            );
        }
    }
});
echo "Sample products added successfully.\n";
