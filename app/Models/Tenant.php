<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'slug',
        'plan_id',
        'trial_ends_at',
        'subscription_ends_at',
        'is_active',
        'is_suspended',
        'data',
        'settings',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'data' => 'array',
        'settings' => 'array',
    ];

    public function currentPlan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    public function hasFeature($feature)
    {
        // Core features that are always enabled for everyone
        $alwaysEnabled = [];

        if (in_array($feature, $alwaysEnabled)) {
            return true;
        }

        if (!$this->currentPlan || !$this->currentPlan->features) {
            return false;
        }

        return in_array($feature, $this->currentPlan->features);
    }

    public function getLimit($key)
    {
        if (!$this->currentPlan || !$this->currentPlan->caps) {
            return null; // Unlimited by default if no plan/caps
        }

        return $this->currentPlan->caps[$key] ?? null; // Returns integer or null (unlimited)
    }

    /**
     * Get the custom domain requests for the tenant.
     */
    public function customDomainRequests()
    {
        return $this->hasMany(\App\Models\CustomDomainRequest::class, 'tenant_id');
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
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    public function domains()
    {
        return $this->hasMany(\App\Models\Domain::class);
    }
    public function getCurrencySymbolAttribute()
    {
        return $this->settings['currency_symbol'] ?? $this->data['currency_symbol'] ?? '₦';
    }

    public function getCurrencyCodeAttribute()
    {
        return $this->settings['currency_code'] ?? $this->data['currency_code'] ?? 'NGN';
    }
}
