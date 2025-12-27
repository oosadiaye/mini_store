<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::select('products.*')->with(['category', 'images']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'low_stock') {
                $query->lowStock();
            }
        }

        // Filter by warehouse stock
        if ($request->filled('warehouse_id')) {
            // We want to show products and their stock AT this warehouse.
            // If we just want to filter products available at this warehouse, we use whereHas.
            // But usually inventory report means "Show me stock for Warehouse X".
            
            // Overwrite stock_quantity with warehouse specific quantity
            $query->addSelect([
                'warehouse_stock' => \App\Models\WarehouseStock::select('quantity')
                    ->whereColumn('product_id', 'products.id')
                    ->where('warehouse_id', $request->warehouse_id)
                    ->limit(1)
            ])->withCasts(['warehouse_stock' => 'integer']);
        }

        $products = $query->latest()->paginate(20);
        
        // If warehouse filtered, we map over to swap stock_quantity for display
        if ($request->filled('warehouse_id')) {
            $products->getCollection()->transform(function($product) {
                // If warehouse_stock is null (no record), it means 0
                $product->stock_quantity = $product->warehouse_stock ?? 0;
                return $product;
            });
        }

        $categories = Category::active()->orderBy('name')->get();
        $warehouses = \App\Models\Warehouse::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories', 'warehouses'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands = \App\Models\Brand::active()->sorted()->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        // Enforce Plan Limits
        $limit = app('tenant')->getLimit('products_limit');
        if ($limit !== -1 && $limit !== null) {
            if (Product::count() >= $limit) {
                return redirect()->back()
                    ->with('error', "You have reached your plan's limit of {$limit} products. Please upgrade to add more.")
                    ->withInput();
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $validated['track_inventory'] = $request->has('track_inventory');
        $validated['is_active'] = $request->has('is_active');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        $validated['low_stock_threshold'] = $validated['low_stock_threshold'] ?? 10;

        $product = Product::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
{
    $product->load('images', 'variants', 'warehouses');
    $categories = Category::active()->orderBy('name')->get();
    $brands = \App\Models\Brand::active()->sorted()->get();
    $warehouses = \App\Models\Warehouse::where('is_active', true)->get();
    return view('admin.products.edit', compact('product', 'categories', 'brands', 'warehouses'));
}
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'flash_sale_price' => 'nullable|numeric|min:0',
            'flash_sale_end_date' => 'nullable|date',
        ]);

        $validated['track_inventory'] = $request->has('track_inventory');
    $validated['is_active'] = $request->has('is_active');
    $validated['is_featured'] = $request->has('is_featured');

    $product->update($validated);

    // Sync warehouse stock
    if ($request->has('warehouse_stock')) {
        $warehouseStock = [];
        foreach ($request->warehouse_stock as $warehouseId => $quantity) {
            if ($quantity !== null && $quantity >= 0) {
                $warehouseStock[$warehouseId] = ['quantity' => (int)$quantity];
            }
        }
        $product->warehouses()->sync($warehouseStock);
        
        // Update total stock_quantity as sum of all warehouse stocks
        $product->stock_quantity = array_sum(array_column($warehouseStock, 'quantity'));
        $product->save();
    }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $currentMaxOrder = $product->images()->max('sort_order') ?? -1;
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $currentMaxOrder + $index + 1,
                    'is_primary' => $product->images()->count() === 0 && $index === 0,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function deleteImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return back()->with('success', 'Image deleted successfully!');
    }
    
    /**
     * Quick image upload for inline drag-and-drop
     */
    public function quickImageUpload(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);
        
        $imageService = app(\App\Services\ImageMatchingService::class);
        $result = $imageService->uploadAndAttach($product, $request->file('image'));
        
        return response()->json([
            'success' => true,
            'url' => $result['url'],
            'image_id' => $result['image_id'],
            'message' => 'Image uploaded successfully',
        ]);
    }
    
    /**
     * Bulk actions for products
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        
        $ids = $request->product_ids;
        $action = $request->action;
        
        switch ($action) {
            case 'mark_featured':
                Product::whereIn('id', $ids)->update(['is_featured' => true]);
                $message = count($ids) . ' products marked as featured.';
                break;
                
            case 'unmark_featured':
                Product::whereIn('id', $ids)->update(['is_featured' => false]);
                $message = count($ids) . ' products unmarked as featured.';
                break;
                
            case 'enable_flash_sale':
                // Validate flash sale data
                $request->validate([
                    'flash_sale_price' => 'required|numeric|min:0',
                    'flash_sale_start' => 'required|date',
                    'flash_sale_end' => 'required|date|after:flash_sale_start',
                ]);
                
                Product::whereIn('id', $ids)->update([
                    'is_flash_sale' => true,
                    'flash_sale_price' => $request->flash_sale_price,
                    'flash_sale_start' => $request->flash_sale_start,
                    'flash_sale_end' => $request->flash_sale_end,
                ]);
                $message = count($ids) . ' products added to flash sale.';
                break;
                
            case 'disable_flash_sale':
                Product::whereIn('id', $ids)->update([
                    'is_flash_sale' => false,
                    'flash_sale_price' => null,
                    'flash_sale_start' => null,
                    'flash_sale_end' => null,
                ]);
                $message = count($ids) . ' products removed from flash sale.';
                break;
                
            case 'activate':
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                $message = count($ids) . ' products activated.';
                break;
                
            case 'deactivate':
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                $message = count($ids) . ' products deactivated.';
                break;
                
            case 'delete':
                Product::whereIn('id', $ids)->delete();
                $message = count($ids) . ' products deleted.';
                break;
                
            default:
                return back()->with('error', 'Invalid action.');
        }
        
        return back()->with('success', $message);
    }
}
