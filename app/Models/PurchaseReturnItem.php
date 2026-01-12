<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'purchase_return_id',
        'purchase_order_item_id',
        'quantity_returned',
        'refund_amount',
        'return_reason',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }
}
