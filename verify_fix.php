<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

try {
    echo "1. Creating test tenant...\n";
    $tenant = Tenant::create([
        'id' => 'final-verification',
        'name' => 'Final Verification Tenant',
        'slug' => 'final-verification',
        'email' => 'final@example.com'
    ]);
    
    echo "2. Creating related data (Product)...\n";
    Product::create([
        'tenant_id' => $tenant->id,
        'name' => 'Verification Product',
        'slug' => 'verification-product',
        'sku' => 'VER-PROD-' . time(),
        'price' => 100.00
    ]);
    
    $productCountBefore = Product::where('tenant_id', $tenant->id)->count();
    echo "Product count before deletion: $productCountBefore\n";
    
    echo "3. Deleting tenant...\n";
    $tenant->delete();
    
    echo "4. Checking for orphans...\n";
    $productCountAfter = Product::where('tenant_id', 'final-verification')->count();
    $tenantCount = Tenant::where('id', 'final-verification')->count();
    
    if ($productCountAfter === 0 && $tenantCount === 0) {
        echo "SUCCESS: Tenant and related products were successfully deleted!\n";
    } else {
        echo "FAILURE: Orphaned data remains or tenant was not deleted.\n";
        echo "Tenant count: $tenantCount, Product count: $productCountAfter\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
