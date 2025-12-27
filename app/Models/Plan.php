<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'trial_days',
        'features',
        'caps',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'caps' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'trial_days' => 'integer',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}
