<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToTenant;

class Supplier extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
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

    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
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

    public function payments()
    {
        return $this->morphMany(Payment::class, 'entity');
    }
}
