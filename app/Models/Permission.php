<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Traits\BelongsToTenant;

class Permission extends SpatiePermission
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'guard_name',
    ];
}
