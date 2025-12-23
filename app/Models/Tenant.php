<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, CentralConnection;

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'plan',
        'trial_ends_at',
        'subscription_ends_at',
        'data',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'data' => 'array',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'phone',
            'plan',
            'trial_ends_at',
            'subscription_ends_at',
            'data',
        ];
    }

    /**
     * Check if tenant is on trial
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if tenant subscription is active
     */
    public function subscriptionActive(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    /**
     * Check if tenant can access the system
     */
    public function canAccess(): bool
    {
        return $this->onTrial() || $this->subscriptionActive();
    }
}
