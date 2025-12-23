<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'tax_number',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get purchase orders
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Scope for active suppliers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function transactions()
    {
        return $this->morphMany(JournalEntryLine::class, 'entity');
    }
}
