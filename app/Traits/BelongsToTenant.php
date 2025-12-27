<?php

namespace App\Traits;

use App\Scopes\TenantScope;

trait BelongsToTenant
{
    /**
     * Boot the BelongsToTenant trait for a model.
     */
    protected static function bootBelongsToTenant()
    {
        // Add global scope to automatically filter by tenant
        static::addGlobalScope(new TenantScope);
        
        // Automatically set tenant_id when creating a new model
        static::creating(function ($model) {
            if (!$model->tenant_id && app()->bound('tenant')) {
                $tenant = app('tenant');
                if ($tenant) {
                    $model->tenant_id = $tenant->id;
                }
            }
        });
    }

    /**
     * Get the tenant that owns the model.
     */
    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id');
    }
}
