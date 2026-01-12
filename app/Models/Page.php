<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function sections()
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
