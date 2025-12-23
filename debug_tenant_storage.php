<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Get the first tenant
$tenant = Tenant::first();

if (!$tenant) {
    echo "No tenant found.\n";
    exit;
}

echo "Tenant ID: " . $tenant->id . "\n";

$tenant->run(function () {
    echo "--- Inside Tenant Context ---\n";
    
    $category = \App\Models\Category::whereNotNull('image')->latest()->first();
    
    if ($category) {
        $path = $category->image;
        echo "Testing Path: $path\n";
        
        // Simulate TenantAssetsController Logic
        try {
            $allowedRoot = realpath(storage_path('app/public'));
            echo "Allowed Root (realpath): " . $allowedRoot . "\n";
            
            if ($allowedRoot === false) {
                throw new Exception("Storage root doesn't exist");
            }

            $attemptedPath = realpath("{$allowedRoot}/{$path}");
            echo "Attempted Path (realpath): " . $attemptedPath . "\n";

            if ($attemptedPath === false) {
                 throw new Exception('Accessing a nonexistent file');
            }

            if (! Str::startsWith($attemptedPath, $allowedRoot)) {
                 throw new Exception('Accessing a file outside the storage root');
            }
            
            echo "SUCCESS: Controller would serve this file.\n";
            
        } catch (Exception $e) {
            echo "FAILURE: Controller would abort. Reason: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "No image to test.\n";
    }
});
