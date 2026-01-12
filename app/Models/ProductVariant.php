<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'name',
        'sku',
        'price',
        'stock_quantity',
        'barcode',
        'is_active',
        'attributes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'attributes' => 'array',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = 'VAR-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if variant is in stock
     */
    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Scope for active variants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
