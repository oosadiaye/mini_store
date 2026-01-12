<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'customer_notes',
        'admin_notes',
        'admin_notes',
        'order_source', // storefront, admin, pos
        'amount_paid',
        'warehouse_id',
        'woocommerce_id',
        'woocommerce_data',
        'woocommerce_status',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(OrderShipping::class);
    }

    public function scopeStorefront($query)
    {
        return $query->where('order_source', 'storefront');
    }

    public function scopeAdmin($query)
    {
        return $query->where('order_source', 'admin');
    }

    public function scopePos($query)
    {
        return $query->where('order_source', 'pos');
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
