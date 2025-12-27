<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'logo',
        'url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? tenant_asset($this->logo) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
