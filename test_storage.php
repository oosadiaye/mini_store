<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

// Simulate file upload
$content = 'test image content';
$path = 'categories/test.txt';

try {
    Storage::disk('public')->put($path, $content);
    echo "File stored successfully at: " . Storage::disk('public')->path($path) . "\n";
    
    if (Storage::disk('public')->exists($path)) {
        echo "File exists check passed.\n";
    } else {
        echo "File exists check FAILED.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
