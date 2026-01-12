<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'product_variant_id',
        'warehouse_id',
        'type', // purchase, sale, adjustment, transfer, return
        'quantity',
        'balance_after',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
