<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        // Only apply scope if we're in a tenant context
        if ($tenantId = $this->getTenantId()) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }

    /**
     * Get the current tenant ID from the application context
     */
    protected function getTenantId()
    {
        // Check if tenant is set in the application container
        if (app()->bound('tenant')) {
            $tenant = app('tenant');
            return $tenant ? $tenant->id : null;
        }

        // Fallback to config
        return config('app.tenant_id');
    }
}
