<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use SoftDeletes, HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'cost_price',
        'compare_at_price',
        'category_id',
        'brand_id',
        'barcode',
        'track_inventory',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_flash_sale',
        'flash_sale_price',
        'flash_sale_start',
        'flash_sale_end',
        'expiry_date',
        'rich_description',
        'meta_tags',
        'published_status',
        'woocommerce_id',
        'woocommerce_data',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'image_url',
        'discount_percentage',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'flash_sale_price' => 'decimal:2',
        'flash_sale_start' => 'datetime',
        'flash_sale_end' => 'datetime',
        'expiry_date' => 'date',
        'track_inventory' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_flash_sale' => 'boolean',
        'meta_tags' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                // Generate Serial Number Format: SKU-YYYYMMDDHHMMSS
                $product->sku = 'SKU-' . date('YmdHis') . rand(10, 99);
            }
        });
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the product combos (child products)
     */
    public function combos(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_combos', 'parent_product_id', 'child_product_id')
            ->withPivot('quantity', 'discount_amount')
            ->withTimestamps();
    }

    /**
     * Get product images
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get product variants
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get purchase order items
     */
    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Get warehouses with stock quantities
     */
    public function warehouses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get product reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->orderByDesc('created_at');
    }

    /**
     * Get primary image
     */
    public function primaryImage()
    {
        return $this->images()->where('is_primary', true)->first();
    }

    /**
     * Get product image URL attribute
     */
    public function getImageUrlAttribute(): string
    {
        $image = $this->primaryImage() ?? $this->images()->first();
        
        if ($image && !empty(trim($image->image_path))) {
            $path = trim($image->image_path);
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            return route('tenant.media', ['path' => $path]);
        }

        // Return a local SVG placeholder data URI to avoid external network dependencies (ERR_FAILED issues)
        // Simple gray rect with text
        $svg = '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#eeeeee"/><text x="50%" y="50%" font-family="Arial" font-size="20" fill="#999999" dominant-baseline="middle" text-anchor="middle">No Image</text></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get primary image attribute (alias for image_url)
     * This supports legacy/theme usages of $product->primary_image as a URL string
     */
    public function getPrimaryImageAttribute(): string
    {
        return $this->image_url;
    }

    /**
     * Check if product is in stock
     */
    public function inStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    /**
     * Check if product is low stock
     */
    public function isLowStock(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }
        return $this->stock_quantity <= $this->low_stock_threshold;
    }



    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): ?float
    {
        if ($this->compare_at_price && $this->compare_at_price > $this->price) {
            return round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100, 2);
        }
        return null;
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    /**
     * Scope for in-stock products
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('track_inventory', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    /**
     * Scope for low stock products
     */
    public function scopeLowStock($query)
    {
        return $query->where('track_inventory', true)
                     ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }
    
    /**
     * Scope for flash sale products
     */
    public function scopeFlashSale($query)
    {
        return $query->where('is_flash_sale', true)
                    ->where('flash_sale_start', '<=', now())
                    ->where('flash_sale_end', '>=', now())
                    ->where('is_active', true);
    }
    
    /**
     * Check if flash sale is currently active
     */
    public function isFlashSaleActive(): bool
    {
        if (!$this->is_flash_sale) {
            return false;
        }
        
        $now = now();
        return $now->between($this->flash_sale_start, $this->flash_sale_end);
    }
    
    /**
     * Get the active price (flash sale or regular)
     */
    public function getActivePrice(): float
    {
        return $this->isFlashSaleActive() ? $this->flash_sale_price : $this->price;
    }
    
    /**
     * Get discount percentage
     */
    public function getDiscountPercentage(): int
    {
        if (!$this->isFlashSaleActive() || !$this->flash_sale_price) {
            return 0;
        }
        
        return round((($this->price - $this->flash_sale_price) / $this->price) * 100);
    }
    
    /**
     * Scope for best selling products
     */
    public function scopeBestSelling($query)
    {
        return $query->withCount('orderItems')
                    ->where('is_active', true)
                    ->orderBy('order_items_count', 'desc');
    }

    /**
     * Record stock movement
     */
    public function recordMovement($warehouseId, $quantity, $type, $referenceType = null, $referenceId = null, $notes = null, $updateGlobal = true)
    {
        // 1. Update Global Stock if tracked
        if ($this->track_inventory && $updateGlobal) {
            $this->increment('stock_quantity', $quantity); // quantity can be negative
        }

        // 2. Update Warehouse Stock
        // 2. Update Warehouse Stock
        $warehouseStock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $this->id)
            ->first();

        if (!$warehouseStock) {
            try {
                $warehouseStock = \App\Models\WarehouseStock::create([
                    'warehouse_id' => $warehouseId,
                    'product_id' => $this->id,
                    'quantity' => 0
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle concurrent creation (Race Condition) - Integrity constraint violation
                if ($e->getCode() == 23000) { 
                    $warehouseStock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
                        ->where('product_id', $this->id)
                        ->first();
                } else {
                    throw $e;
                }
            }
        }

        if ($warehouseStock) {
            $warehouseStock->increment('quantity', $quantity);
        }
        
        // 3. Log History
        return \App\Models\StockMovement::create([
            'tenant_id' => $this->tenant_id,
            'product_id' => $this->id,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'quantity' => $quantity,
            'balance_after' => $warehouseStock->quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }
}
