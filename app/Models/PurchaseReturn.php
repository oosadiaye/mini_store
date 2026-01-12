<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'purchase_order_id',
        'status',
        'admin_notes',
        'refund_amount',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
