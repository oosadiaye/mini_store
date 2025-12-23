<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
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
        
        if ($image) {
            if (filter_var($image->image_path, FILTER_VALIDATE_URL)) {
                return $image->image_path;
            }
            return route('tenant.media', ['path' => $image->image_path]);
        }

        return 'https://via.placeholder.com/300?text=' . str_replace(' ', '+', $this->name);
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
}
