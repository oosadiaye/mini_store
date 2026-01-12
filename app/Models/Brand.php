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
        if (!$this->logo) {
            return null;
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        return route('tenant.media', ['path' => $this->logo]);
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
