<?php
// fix_product_images.php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

echo "Migrating Product Images from Public to Tenant Disk...\n";

try {
    $publicDisk = Storage::disk('public');
    $tenantDisk = Storage::disk('tenant');
    
    // Ensure tenant products directory exists
    if (!$tenantDisk->exists('products')) {
        $tenantDisk->makeDirectory('products');
    }
    
    $files = $publicDisk->files('products');
    $count = 0;
    
    foreach ($files as $file) {
        // Full paths
        $sourcePath = $publicDisk->path($file);
        $destPath = 'products/' . basename($file);
        
        // Skip if already exists in tenant (unless we want to overwrite? Let's safeguard)
        if ($tenantDisk->exists($destPath)) {
            echo "Skipping $file (already exists in tenant)\n";
            continue;
        }
        
        // Move file
        // We use File::copy instead of Storage::move to avoid disk adapter issues if they are different roots
        // Actually, let's use stream copy to be safe
        $stream = fopen($sourcePath, 'r');
        $tenantDisk->put($destPath, $stream);
        fclose($stream);
        
        // Optional: Delete from public?
        // Let's keep them for safety for now, or rename?
        // $publicDisk->delete($file); 
        
        echo "Moved: $file\n";
        $count++;
    }
    
    echo "Migration Complete. Moved $count files.\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
