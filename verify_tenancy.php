<?php

use App\Models\Tenant;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "--- STARTING VERIFICATION ---\n";

DB::beginTransaction();

try {
    // 1. Create Tenant 1
    echo "Creating Tenant 1 (demo)...\n";
    $t1 = Tenant::create([
        'id' => 'demo',
        'slug' => 'demo',
        'name' => 'Demo Store',
        'email' => 'demo@example.com',
        'is_active' => true,
        'data' => []
    ]);

    // 2. Create User for Tenant 1
    echo "Creating Admin for Tenant 1...\n";
    $u1 = User::create([
        'name' => 'Demo Admin',
        'email' => 'admin@demo.com',
        'password' => bcrypt('password'),
        'role' => 'admin',
        'tenant_id' => $t1->id
    ]);

    // 3. Set Context to Tenant 1
    echo "Setting Context to Tenant 1...\n";
    app()->instance('tenant', $t1);

    // 4. Create Product for Tenant 1
    echo "Creating Product for Tenant 1...\n";
    $p1 = Product::create([
        'tenant_id' => $t1->id, // Should be auto-set by trait, but being explicit for now
        'name' => 'T1 Product',
        'slug' => 't1-product',
        'price' => 100,
        // Add minimal required fields
    ]);

    // 5. Create Tenant 2
    echo "Creating Tenant 2 (demo2)...\n";
    $t2 = Tenant::create([
        'id' => 'demo2',
        'slug' => 'demo2',
        'name' => 'Demo Store 2',
        'email' => 'demo2@example.com',
        'is_active' => true,
        'data' => []
    ]);

    // 6. Set Context to Tenant 2
    echo "Setting Context to Tenant 2...\n";
    app()->instance('tenant', $t2);

    // 7. Create Product for Tenant 2
    echo "Creating Product for Tenant 2...\n";
    $p2 = Product::create([
        'name' => 'T2 Product', // Traits should set tenant_id
        'slug' => 't2-product',
        'price' => 200,
    ]);

    // 8. Verify Scoping
    echo "Verifying Scope (Context: Tenant 2)...\n";
    $products = Product::all();
    echo "Found " . $products->count() . " products.\n";
    
    if ($products->count() === 1 && $products->first()->name === 'T2 Product') {
        echo "PASS: Only T2 products visible.\n";
    } else {
        echo "FAIL: Visible products: " . $products->pluck('name')->implode(', ') . "\n";
    }

    // 9. Verify Scope Change
    echo "Switching Context to Tenant 1...\n";
    app()->instance('tenant', $t1);
    // Clear cache/memory if needed, usually new query is fine
    // Global scopes query builder, so fresh query works
    $products = Product::all();
    echo "Found " . $products->count() . " products.\n";

    if ($products->count() === 1 && $products->first()->name === 'T1 Product') {
        echo "PASS: Only T1 products visible.\n";
    } else {
         echo "FAIL: Visible products: " . $products->pluck('name')->implode(', ') . "\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

DB::rollBack(); // Rollback so we don't junk the DB
echo "\n--- VERIFICATION FINISHED ---\n";
