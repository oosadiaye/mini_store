<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'address',
        'city',
        'phone',
        'email',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the manager
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get products with stock quantities
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_warehouse')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get warehouse stock
     */
    public function stock(): HasMany
    {
        return $this->hasMany(WarehouseStock::class);
    }

    /**
     * Scope for active warehouses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
