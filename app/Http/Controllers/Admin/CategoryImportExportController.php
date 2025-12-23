<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryImportExportController extends Controller
{
    /**
     * Export Categories to CSV
     */
    public function export()
    {
        $fileName = 'categories_export_' . date('Y-m-d_H-i') . '.csv';
        $categories = Category::with('parent')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('ID', 'Name', 'Slug', 'Parent Category', 'Description', 'Sort Order', 'Active', 'Show on Storefront');

        $callback = function() use($categories, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($categories as $category) {
                $row['ID']  = $category->id;
                $row['Name']    = $category->name;
                $row['Slug']    = $category->slug;
                $row['Parent']  = $category->parent ? $category->parent->name : '';
                $row['Description']  = $category->description;
                $row['Sort Order']  = $category->sort_order;
                $row['Active']  = $category->is_active ? 'Yes' : 'No';
                $row['Storefront']  = $category->show_on_storefront ? 'Yes' : 'No';

                fputcsv($file, array($row['ID'], $row['Name'], $row['Slug'], $row['Parent'], $row['Description'], $row['Sort Order'], $row['Active'], $row['Storefront']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV Template
     */
    public function template()
    {
        $fileName = 'categories_import_template.csv';
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Name', 'Slug (Optional)', 'Parent Category Name (Optional)', 'Description', 'Sort Order', 'Active (Yes/No)', 'Show on Storefront (Yes/No)');

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Example row
            fputcsv($file, array('Men\'s Clothing', 'mens-clothing', '', 'Clothing for Men', '1', 'Yes', 'Yes'));
            fputcsv($file, array('Shirts', 'shirts', 'Men\'s Clothing', 'T-Shirts and Polos', '2', 'Yes', 'Yes'));
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import Categories from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        
        // Remove header row
        $header = array_map('trim', $csvData[0]);
        unset($csvData[0]);

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($csvData as $row) {
                // Name (0), Slug (1), Parent (2), Desc (3), Sort (4), Active (5), Storefront (6)
                
                if (count($row) < 1) continue; 

                $name = $row[0] ?? null;
                if (!$name) continue;

                $slug = !empty($row[1]) ? Str::slug($row[1]) : Str::slug($name);
                $parentName = $row[2] ?? null;
                $description = $row[3] ?? '';
                $sortOrder = intval($row[4] ?? 0);
                
                $isActiveStr = strtolower($row[5] ?? 'yes');
                $isActive = ($isActiveStr === 'yes' || $isActiveStr === '1' || $isActiveStr === 'true');
                
                $showStorefrontStr = strtolower($row[6] ?? 'yes');
                $showStorefront = ($showStorefrontStr === 'yes' || $showStorefrontStr === '1' || $showStorefrontStr === 'true');

                // Resolve Parent ID
                $parentId = null;
                if (!empty($parentName)) {
                    // Try to find parent by name, create if strictly necessary or just leave null if not found to avoid circular dependency issues in simple import
                    // Ideally we should do a second pass or basic lookup
                    $parent = Category::where('name', $parentName)->first();
                    if ($parent) {
                        $parentId = $parent->id;
                    } else {
                        // Optional: Create parent on the fly? Let's just create it to be safe/useful
                        $parent = Category::create([
                            'name' => $parentName, 
                            'slug' => Str::slug($parentName),
                            'is_active' => true
                        ]);
                        $parentId = $parent->id;
                    }
                }

                $category = Category::where('slug', $slug)->orWhere('name', $name)->first();

                if ($category) {
                    $category->update([
                        'name' => $name,
                        'description' => $description,
                        'parent_id' => $parentId,
                        'sort_order' => $sortOrder,
                        'is_active' => $isActive,
                        'show_on_storefront' => $showStorefront
                    ]);
                    $updatedCount++;
                } else {
                    Category::create([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'parent_id' => $parentId,
                        'sort_order' => $sortOrder,
                        'is_active' => $isActive,
                        'show_on_storefront' => $showStorefront
                    ]);
                    $importedCount++;
                }
            }
            
            DB::commit();
            return back()->with('success', "Import successful! Created: $importedCount, Updated: $updatedCount.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Import failed: " . $e->getMessage());
        }
    }
}
