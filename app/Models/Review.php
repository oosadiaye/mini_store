<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'product_id',
        'customer_id',
        'name',
        'rating',
        'title',
        'body',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'approved' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
