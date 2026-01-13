<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportExportController extends Controller
{
    /**
     * Export Products to CSV
     */
    /**
     * Export Products to CSV
     */
    public function export()
    {
        $fileName = 'products_export_' . date('Y-m-d_H-i') . '.csv';
        $products = Product::with(['category', 'brand'])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
            'Name', 'SKU', 'Category', 'Brand', 'Price', 
            'Cost Price', 'Compare At Price', 'Stock Quantity', 'Low Stock Threshold', 
            'Description', 'Short Description', 'Barcode', 
            'Track Inventory (Yes/No)', 'Active (Yes/No)', 'Featured (Yes/No)', 'Expiry Date (YYYY-MM-DD)'
        );

        $callback = function() use($products, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                $row = array();
                $row[] = $product->name;
                $row[] = $product->sku;
                $row[] = $product->category ? $product->category->name : '';
                $row[] = $product->brand ? $product->brand->name : '';
                $row[] = $product->price;
                $row[] = $product->cost_price;
                $row[] = $product->compare_at_price;
                $row[] = $product->stock_quantity;
                $row[] = $product->low_stock_threshold;
                $row[] = $product->description;
                $row[] = $product->short_description;
                $row[] = $product->barcode;
                $row[] = $product->track_inventory ? 'Yes' : 'No';
                $row[] = $product->is_active ? 'Yes' : 'No';
                $row[] = $product->is_featured ? 'Yes' : 'No';
                $row[] = $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV Template
     */
    /**
     * Download CSV Template
     */
    public function template()
    {
        $fileName = 'products_import_template.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array(
            'Name', 'SKU', 'Category', 'Brand', 'Price', 
            'Cost Price', 'Compare At Price', 'Stock Quantity', 'Low Stock Threshold', 
            'Description', 'Short Description', 'Barcode', 
            'Track Inventory (Yes/No)', 'Active (Yes/No)', 'Featured (Yes/No)', 'Expiry Date (YYYY-MM-DD)'
        );

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array(
                'Example Product', 'SKU-12345', 'T-Shirts', 'Nike', '29.99', 
                '15.00', '39.99', '100', '10', 
                'Full Description Here', 'Short Description', '1234567890123', 
                'Yes', 'Yes', 'No', '2025-12-31'
            ));
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Products from CSV or ZIP (with images)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,zip',
        ]);

        $file = $request->file('file');
        $isZip = $file->extension() === 'zip';
        
        if ($isZip) {
            return $this->importFromZip($file);
        }
        
        return $this->importFromCsv($file);
    }
    
    /**
     * Import from ZIP file containing CSV + images
     */
    protected function importFromZip($zipFile)
    {
        $zip = new \ZipArchive();
        $tempDir = storage_path('app/temp/' . uniqid());
        mkdir($tempDir, 0755, true);
        
        if ($zip->open($zipFile->getRealPath()) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();
            
            // Find CSV file
            $csvFile = null;
            $imagesDir = null;
            
            foreach (scandir($tempDir) as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $path = $tempDir . '/' . $item;
                if (is_file($path) && in_array(pathinfo($path, PATHINFO_EXTENSION), ['csv', 'txt'])) {
                    $csvFile = $path;
                }
                if (is_dir($path) && strtolower($item) === 'images') {
                    $imagesDir = $path;
                }
            }
            
            if (!$csvFile) {
                \File::deleteDirectory($tempDir);
                return back()->with('error', 'No CSV file found in ZIP');
            }
            
            // Import CSV
            $result = $this->processCsvImport($csvFile, $imagesDir);
            
            // Cleanup
            \File::deleteDirectory($tempDir);
            
            return back()->with('success', $result);
        }
        
        return back()->with('error', 'Failed to extract ZIP file');
    }
    
    /**
     * Import from CSV file only
     */
    protected function importFromCsv($file)
    {
        $result = $this->processCsvImport($file->getRealPath());
        return back()->with('success', $result);
    }
    
    /**
     * Process CSV import with optional images directory
     */
    /**
     * Process CSV import with optional images directory
     */
    protected function processCsvImport($csvPath, $imagesDir = null)
    {
        $csvData = array_map('str_getcsv', file($csvPath));
        
        // Remove header row
        $header = array_map('trim', $csvData[0]);
        unset($csvData[0]);

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($csvData as $row) {
                // Map row to keys based on index
                // 0: Name, 1: SKU, 2: Category, 3: Brand, 4: Price
                // 5: Cost Price, 6: Compare At Price, 7: Stock, 8: Low Stock
                // 9: Description, 10: Short Desc, 11: Barcode
                // 12: Track Inv, 13: Active, 14: Featured, 15: Expiry
                
                if (count($row) < 5) continue; // Skip malformed rows

                $name = $row[0] ?? 'Unknown Product';
                $sku = $row[1] ?? 'SKU-' . Str::random(8);
                if(empty($sku)) $sku = 'SKU-' . Str::random(8);

                $categoryName = $row[2] ?? null;
                $brandName = $row[3] ?? null;
                $price = floatval($row[4] ?? 0);
                
                $costPrice = floatval($row[5] ?? 0);
                $compareAtPrice = floatval($row[6] ?? 0);
                $stock = intval($row[7] ?? 0);
                $lowStock = intval($row[8] ?? 10);
                
                $description = $row[9] ?? '';
                $shortDescription = $row[10] ?? '';
                $barcode = $row[11] ?? '';
                
                $trackInvStr = strtolower($row[12] ?? 'yes');
                $trackInv = ($trackInvStr === 'yes' || $trackInvStr === '1' || $trackInvStr === 'true');

                $isActiveStr = strtolower($row[13] ?? 'yes');
                $isActive = ($isActiveStr === 'yes' || $isActiveStr === '1' || $isActiveStr === 'true');
                
                $isFeaturedStr = strtolower($row[14] ?? 'no');
                $isFeatured = ($isFeaturedStr === 'yes' || $isFeaturedStr === '1' || $isFeaturedStr === 'true');
                
                $expiryDate = $row[15] ?? null;
                if ($expiryDate && !strtotime($expiryDate)) $expiryDate = null;

                // Find or Create Category
                $categoryId = null;
                if (!empty($categoryName)) {
                    $category = Category::firstOrCreate(
                        ['name' => $categoryName],
                        ['slug' => Str::slug($categoryName), 'is_active' => true]
                    );
                    $categoryId = $category->id;
                }

                // Find or Create Brand
                $brandId = null;
                if (!empty($brandName)) {
                    $brand = Brand::firstOrCreate(
                        ['name' => $brandName],
                        ['slug' => Str::slug($brandName), 'is_active' => true]
                    );
                    $brandId = $brand->id;
                }

                // Update or Create Product
                $product = Product::withTrashed()->where('sku', $sku)->first();

                $data = [
                    'name' => $name,
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'compare_at_price' => $compareAtPrice,
                    'stock_quantity' => $stock,
                    'low_stock_threshold' => $lowStock,
                    'description' => $description,
                    'short_description' => $shortDescription,
                    'barcode' => $barcode,
                    'track_inventory' => $trackInv,
                    'is_active' => $isActive,
                    'is_featured' => $isFeatured,
                    'expiry_date' => $expiryDate,
                ];

                if ($product) {
                    $product->update($data);
                    $updatedCount++;
                } else {
                    $data['sku'] = $sku;
                    Product::create($data);
                    $importedCount++;
                    $product = Product::where('sku', $sku)->first();
                }
                
                // Match and upload images if images directory exists
                if ($imagesDir && $product) {
                    $this->matchAndUploadImages($product, $imagesDir);
                }
            }
            
            DB::commit();
            return back()->with('success', "Import successful! Created: $importedCount, Updated: $updatedCount.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Import failed: " . $e->getMessage());
        }
    }
    
    /**
     * Match and upload images for a product from images directory
     */
    protected function matchAndUploadImages($product, $imagesDir)
    {
        $imageService = app(\App\Services\ImageMatchingService::class);
        $sku = $product->sku;
        
        // Look for images matching the SKU
        $files = glob($imagesDir . '/*');
        
        foreach ($files as $filePath) {
            if (!is_file($filePath)) continue;
            
            $filename = basename($filePath);
            $extractedSku = $imageService->extractSkuFromFilename($filename);
            
            if (strtoupper($extractedSku) === strtoupper($sku)) {
                // Create UploadedFile instance from path
                $file = new \Illuminate\Http\UploadedFile(
                    $filePath,
                    $filename,
                    mime_content_type($filePath),
                    null,
                    true
                );
                
                try {
                    $imageService->uploadAndAttach($product, $file);
                } catch (\Exception $e) {
                    // Log error but continue with other images
                    \Log::error("Failed to upload image {$filename} for SKU {$sku}: " . $e->getMessage());
                }
            }
        }
    }
}
