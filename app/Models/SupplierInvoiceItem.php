<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplierInvoice()
    {
        return $this->belongsTo(SupplierInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
