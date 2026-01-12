<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'display_name',
        'is_active',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function getConfigValue($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
