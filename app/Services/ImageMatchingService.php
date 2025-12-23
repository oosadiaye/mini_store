<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ImageMatchingService
{
    /**
     * Extract SKU from filename using various patterns
     */
    public function extractSkuFromFilename(string $filename): ?string
    {
        // Remove extension
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Pattern 1: Direct SKU (e.g., "PROD-001.jpg")
        if (preg_match('/^([A-Z0-9\-_]+)$/i', $name, $matches)) {
            return strtoupper($matches[1]);
        }
        
        // Pattern 2: SKU with suffix (e.g., "PROD-001-front.jpg", "PROD-001_image.jpg")
        if (preg_match('/^([A-Z0-9\-_]+?)[\-_](front|back|side|image|photo|main|\d+)/i', $name, $matches)) {
            return strtoupper($matches[1]);
        }
        
        // Pattern 3: Prefix with SKU (e.g., "product_PROD-001.jpg")
        if (preg_match('/[_\-]([A-Z0-9\-_]+)$/i', $name, $matches)) {
            return strtoupper($matches[1]);
        }
        
        // Fallback: Use entire filename as SKU
        return strtoupper($name);
    }
    
    /**
     * Find product by SKU
     */
    public function findProductBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }
    
    /**
     * Upload image and attach to product
     */
    public function uploadAndAttach(Product $product, $file, bool $setPrimary = false): array
    {
        $path = $file->store('products', 'public');
        $url = \Storage::url($path);
        
        $isPrimary = $setPrimary || $product->images()->count() === 0;
        
        $image = $product->images()->create([
            'url' => $url,
            'is_primary' => $isPrimary,
        ]);
        
        return [
            'success' => true,
            'url' => $url,
            'image_id' => $image->id,
        ];
    }
    
    /**
     * Process multiple images and match to products
     */
    public function processBulkUpload(array $files): array
    {
        $results = [
            'matched' => [],
            'unmatched' => [],
            'errors' => [],
        ];
        
        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();
            $sku = $this->extractSkuFromFilename($filename);
            
            if (!$sku) {
                $results['errors'][] = [
                    'filename' => $filename,
                    'error' => 'Could not extract SKU from filename',
                ];
                continue;
            }
            
            $product = $this->findProductBySku($sku);
            
            if (!$product) {
                $results['unmatched'][] = [
                    'filename' => $filename,
                    'sku' => $sku,
                ];
                continue;
            }
            
            try {
                $uploadResult = $this->uploadAndAttach($product, $file);
                $results['matched'][] = [
                    'filename' => $filename,
                    'sku' => $sku,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'url' => $uploadResult['url'],
                ];
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'filename' => $filename,
                    'sku' => $sku,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    }
}
