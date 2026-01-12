<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the cart
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get line total
     */
    public function getLineTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
