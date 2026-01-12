<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class StorefrontProduct extends Product
{
    protected $table = 'products';

    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'price', 
        'compare_at_price', 'category_id', 'is_featured', 'is_active',
        'published_status', 'meta_title', 'meta_description', 'meta_keywords'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('published', function (Builder $builder) {
            $builder->where('published_status', 'published')
                    ->where('is_active', true);
        });
    }

    /**
     * Get warehouse stocks.
     */
    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class, 'product_id');
    }

    /**
     * Get the real-time available stock across all warehouses.
     * 
     * @return int
     */
    public function getAvailableStockAttribute(): int
    {
        if (!$this->track_inventory) {
            return 9999; // Unlimited if not tracked
        }

        // Sum quantity from all warehouse stocks for this product
        return (int) $this->warehouseStocks()->sum('quantity');
    }
}
