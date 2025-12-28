<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PageLayout extends Model
{
    use BelongsToTenant; // Optional if we want it tenant-scoped, but migration doesn't enforce FK (yet).

    protected $fillable = [
        'tenant_id', // Add this if we want scoping
        'page_name',
        'sections',
        'is_active',
    ];

    protected $casts = [
        'sections' => 'array',
        'is_active' => 'boolean',
    ];
}
