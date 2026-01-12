<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product; // Admin Product Model (Tenant Scoped by Global Scope if consistent, but here likely needs explicit or base scoping)
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('is_active', true);

        // 1. Search (Keyword)
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // 2. Category Filter
        if ($request->filled('category_slug')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->query('category_slug'));
            });
        }

        // 3. Price Filter (Min/Max)
        // Note: This filters by base price. For flash sales, logic can get complex in SQL. 
        // We'll stick to base price for efficiency unless requested otherwise.
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->query('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->query('max_price'));
        }

        // 4. Featured Filter
        if ($request->query('is_featured') === 'true' || $request->query('is_featured') === '1') {
            $query->where('is_featured', true);
        }

        // 5. Sorting
        // 'newest', 'price_low', 'price_high', 'name_asc'
        $sort = $request->query('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            case 'new_arrivals': // Map Fresh Drops
                $query->latest();
                break;
            case 'best_sellers': // Map Crowd Favorites (Mock with Random or Featured if available)
                $query->inRandomOrder(); 
                break;
            default:
                $query->latest();
                break;
        }

        // 5. Pagination
        $perPage = $request->query('per_page', 12);
        $products = $query->paginate($perPage);

        // 6. Aggregations (Filters) - Calculate from a formatted clone of the base query
        // We want filters availability based on current search context, OR all available?
        // Usually, users want to see all categories *unless* we are drilling down.
        // Let's return ALL active categories for now to populate the sidebar.
        $categories = \App\Models\Category::where('is_active', true)
                        ->where('show_on_storefront', true)
                        ->withCount('products')
                        ->get()
                        ->map(function ($cat) {
                            return [
                                'name' => $cat->name,
                                'slug' => $cat->slug,
                                'count' => $cat->products_count
                            ];
                        });
        
        // Price Range (Global Min/Max for sliders)
        $minPrice = Product::where('is_active', true)->min('price') ?? 0;
        $maxPrice = Product::where('is_active', true)->max('price') ?? 1000;

        // 7. Response Transformation
        $formattedData = $products->getCollection()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->price,
                'compare_at_price' => (float) $product->compare_at_price,
                'is_flash_sale' => $product->isFlashSaleActive(),
                'active_price' => (float) $product->getActivePrice(),
                'discount_percentage' => $product->getDiscountPercentage(),
                'image_url' => $product->image_url,
                'category' => $product->category ? $product->category->name : 'Uncategorized',
                'stock_status' => $product->inStock() ? 'in_stock' : 'out_of_stock',
                'url' => route('storefront.product.detail', ['tenant' => app('tenant')->slug, 'slug' => $product->slug]),
            ];
        });

        return response()->json([
            'data' => $formattedData,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total_items' => $products->total(),
                'per_page' => $products->perPage(),
            ],
            'filters' => [
                'categories' => $categories,
                'price_range' => [
                    'min' => (float) $minPrice,
                    'max' => (float) $maxPrice
                ]
            ]
        ]);
    }
    public function list()
    {
        // Get layout preference from tenant config
        $tenantConfig = \App\Models\StoreConfig::first(); // Assuming single tenant context
        $layoutMode = $tenantConfig->layout_preference ?? 'minimal';

        // Map backend layout preference to frontend mode
        // 'minimal', 'showcase' -> 'grid'
        // 'catalog' -> 'table' (or logic as requested: quick_order -> table)
        // User Request: 'brand_showcase' OR 'high_volume_mart' -> Grid. 'quick_order' -> Table.
        // My migration has: 'minimal', 'showcase', 'catalog'. Let's map 'catalog' to table for now or checking actual values.
        
        $viewMode = 'grid';
        if ($layoutMode === 'catalog') {
            $viewMode = 'table';
        }

        $config = $tenantConfig; // Alias for layout compatibility

        return view('storefront.products.index', compact('viewMode', 'layoutMode', 'config'));
    }

    public function show($slug)
    {
        $product = Product::active()->where('slug', $slug)->with(['images', 'category'])->firstOrFail();

        // Related Products: Same category, active, not current product, limit 4
        $relatedProducts = [];
        if ($product->category) {
            $relatedProducts = Product::active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get()
                ->map(function ($related) {
                    return [
                        'id' => $related->id,
                        'name' => $related->name,
                        'slug' => $related->slug,
                        'price' => (float) $related->price,
                        'active_price' => (float) $related->getActivePrice(),
                        'image_url' => $related->image_url,
                        'url' => route('storefront.product.detail', ['tenant' => app('tenant')->slug, 'slug' => $related->slug]),
                    ];
                });
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'sale_price' => $product->isFlashSaleActive() ? (float) $product->flash_sale_price : ($product->compare_at_price > $product->price ? (float) $product->price : null),
            'compare_at_price' => (float) $product->compare_at_price,
            'stock' => $product->track_inventory ? $product->stock_quantity : 999, // 999 or null for unlimited
            'description' => $product->description,
            'images' => $product->images->map(function ($img) {
                return filter_var($img->image_path, FILTER_VALIDATE_URL) ? $img->image_path : route('tenant.media', ['path' => $img->image_path]);
            }),
            'related_products' => $relatedProducts,
            'url' => route('storefront.product.detail', ['tenant' => app('tenant')->slug, 'slug' => $product->slug]),
        ]);
    }

    public function detail($slug)
    {
        $product = Product::active()->where('slug', $slug)->with(['images', 'category'])->firstOrFail();
        
        // Use the API logic for consistency (or simple Eloquent here)
        // Let's just pass the model and let the view/Alpine handle formatting if needed, 
        // but for the related products card, we might need them prepared.
        
        $relatedProducts = [];
        if ($product->category_id) {
            $relatedProducts = Product::active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->limit(4)
                ->get();
        }

        $tenantConfig = \App\Models\StoreConfig::first();
        $config = $tenantConfig;

        return view('storefront.products.show', compact('product', 'relatedProducts', 'config'));
    }
}
