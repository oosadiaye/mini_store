<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'quantity',
        'price',
        'tax_amount',
        'tax_code_id',
        'total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function taxCode()
    {
        return $this->belongsTo(TaxCode::class);
    }
}
