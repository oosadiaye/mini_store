<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'address_type',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
