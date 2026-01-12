<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'public_display_name',
        'slug',
        'description',
        'image',
        'parent_id',
        'sort_order',
        'is_active',
        'show_on_storefront',
        'is_visible_online',
    ];

    /**
     * Get the display name (public or internal).
     */
    public function getDisplayNameAttribute()
    {
        return $this->public_display_name ?: $this->name;
    }

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_storefront' => 'boolean',
        'is_visible_online' => 'boolean',
    ];

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get products in this category
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all descendants (recursive)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for storefront visible categories
     */
    public function scopeStorefront($query)
    {
        return $query->where('show_on_storefront', true)->where('is_active', true);
    }

    /**
     * Scope for root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
    /**
     * Get category image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return route('tenant.media', ['path' => $this->image]);
    }
}
