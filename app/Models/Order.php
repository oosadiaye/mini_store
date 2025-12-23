<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
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
}
