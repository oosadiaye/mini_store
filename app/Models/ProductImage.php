<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_path',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get full image URL
     */
    public function getUrlAttribute(): string
    {
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        // Route handles query param automatically if not in definition? 
        // Let's rely on Laravel's behavior: route('name', ['path' => 'val']) -> /media?path=val
        return route('tenant.media', ['path' => $this->image_path]);
    }
}
