<?php
// test_upload_store.php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

echo "Testing UploadedFile::store('products', 'tenant')...\n";

try {
    // Create a dummy file
    $tmpFile = sys_get_temp_dir() . '/test_upload.jpg';
    file_put_contents($tmpFile, 'dummy image content');
    
    $file = new UploadedFile(
        $tmpFile,
        'test_upload.jpg',
        'image/jpeg',
        null,
        true // test mode
    );
    
    // Simulate store
    $path = $file->store('products', 'tenant');
    
    echo "Stored Path: $path\n";
    
    // Check where it landed
    $tenantExists = Storage::disk('tenant')->exists($path);
    $publicExists = Storage::disk('public')->exists($path);
    
    echo "Exists on Tenant Disk? " . ($tenantExists ? 'YES' : 'NO') . "\n";
    echo "Exists on Public Disk? " . ($publicExists ? 'YES' : 'NO') . "\n";
    
    if ($tenantExists) {
        echo "Tenant Full Path: " . Storage::disk('tenant')->path($path) . "\n";
    }
    if ($publicExists) {
        echo "Public Full Path: " . Storage::disk('public')->path($path) . "\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
