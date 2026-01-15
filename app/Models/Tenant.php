<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


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
        'is_storefront_enabled',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
        'is_suspended' => 'boolean',
        'data' => 'array',
        'settings' => 'array',
        'is_storefront_enabled' => 'boolean',
    ];

    protected static function booted()
    {
        static::deleting(function ($tenant) {
            $tables = [
                'categories', 'products', 'product_variants', 'product_images', 
                'product_combos', 'product_warehouse', 'brands', 'coupons', 
                'carts', 'cart_items', 'reviews', 'payment_types', 
                'product_enquiries', 'posts', 'pages', 'page_sections', 
                'page_layouts', 'storefront_settings', 'storefront_templates', 
                'banners', 'customers', 'customer_addresses', 'orders', 
                'order_items', 'order_shipping', 'order_returns', 
                'order_return_items', 'incomes', 'expenses', 'journal_entries', 
                'journal_entry_lines', 'chart_of_accounts', 'purchase_orders', 
                'purchase_order_items', 'purchase_returns', 'purchase_return_items', 
                'suppliers', 'warehouses', 'warehouse_stocks', 'stock_transfers', 
                'roles', 'notifications', 'subscription_payments', 'subscription_transactions',
                'payment_gateways', 'tenant_user_impersonation_tokens', 'store_configs'
            ];

            foreach ($tables as $table) {
                if (\Illuminate\Support\Facades\Schema::hasTable($table) && \Illuminate\Support\Facades\Schema::hasColumn($table, 'tenant_id')) {
                    DB::table($table)->where('tenant_id', $tenant->id)->delete();
                }
            }
            
            // Special handling for tables that might not have tenant_id but are related
            // For example, domains (which we already handle in controller, but let's be safe here too)
            if (\Illuminate\Support\Facades\Schema::hasTable('domains')) {
                DB::table('domains')->where('tenant_id', $tenant->id)->delete();
            }
        });
    }

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

    /**
     * Get the primary domain for the tenant.
     */
    public function getPrimaryDomain(): string
    {
        // 1. Check for approved custom domain
        $customDomain = $this->customDomainRequests()
            ->where('status', 'approved')
            ->first();

        if ($customDomain) {
            return $customDomain->domain;
        }

        // 2. Fallback to subdomain
        $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);
        return $this->slug . '.' . $centralDomain;
    }
}
