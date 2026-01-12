<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use App\Traits\BelongsToTenant;

class Role extends SpatieRole
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'guard_name',
    ];
}
