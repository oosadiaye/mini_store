<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'session_id',
        'customer_id',
        'coupon_id',
        'customer_email',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get cart items
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get cart subtotal
     */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute(): float
    {
        if (!$this->coupon) {
            return 0;
        }

        return $this->coupon->calculateDiscount($this->subtotal);
    }

    /**
     * Get shipping cost based on tenant settings
     */
    public function getShippingCostAttribute(): float
    {
        $tenant = app('tenant');
        $settings = $tenant->data ?? [];
        $cost = (float) ($settings['shipping_cost'] ?? 0);
        $threshold = (float) ($settings['free_shipping_threshold'] ?? 0);

        // If threshold is set and subtotal meets it, free shipping
        if ($threshold > 0 && $this->subtotal >= $threshold) {
            return 0;
        }

        return $cost;
    }

    /**
     * Get final total
     */
    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - $this->discount_amount + $this->shipping_cost);
    }

    /**
     * Get total items count
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
