<?php
// test_storage.php
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

try {
    echo "Testing Tenant Disk Storage...\n";
    $disk = Storage::disk('tenant');
    echo "Root: " . $disk->path('') . "\n";
    
    // Create dummy file
    $content = 'test image content';
    $filename = 'test_product.txt';
    $path = 'products/' . $filename;
    
    $result = $disk->put($path, $content);
    
    if ($result) {
        echo "SUCCESS: File written to $path\n";
        echo "Exists? " . ($disk->exists($path) ? 'Yes' : 'No') . "\n";
        echo "Full Path: " . $disk->path($path) . "\n";
    } else {
        echo "FAILURE: Could not write file.\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
