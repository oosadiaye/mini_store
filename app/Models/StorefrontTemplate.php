<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorefrontTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'default_settings',
        'layout_data',
        'is_premium',
        'is_active',
    ];

    protected $casts = [
        'default_settings' => 'array',
        'layout_data' => 'array',
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function settings()
    {
        return $this->hasMany(ThemeSetting::class, 'template_id');
    }
}
