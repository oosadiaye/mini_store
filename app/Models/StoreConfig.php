<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreConfig extends Model
{
    use HasFactory;
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'store_name',
        'logo_path',
        'brand_color',
        'industry',
        'selected_categories',
        'layout_preference',
        'is_completed',
        'store_email',
        'store_email',
        'social_links',
        'theme_layout',
    ];

    protected $casts = [
        'selected_categories' => 'array',
        'social_links' => 'array',
        'is_completed' => 'boolean',
        'theme_layout' => 'array',
    ];
}
