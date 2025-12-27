<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Renter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'id_number',
        'contract_start_date',
        'contract_end_date',
        'rental_amount',
        'payment_frequency',
        'security_deposit',
        'status',
        'notes',
        'tenant_id',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'rental_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->tenant_id)) {
                $model->tenant_id = app('tenant')->id ?? null;
            }
        });
    }

    /**
     * Get outstanding balance from AR ledger
     */
    public function getOutstandingBalanceAttribute()
    {
        // Sum all AR journal entries for this renter
        $balance = \App\Models\JournalEntryLine::where('renter_id', $this->id)
            ->whereHas('account', function($q) {
                $q->where('account_code', '1300'); // AR - Renters
            })
            ->sum(\DB::raw('debit - credit'));
            
        return $balance;
    }

    /**
     * Get all journal entry lines (AR ledger)
     */
    public function transactions()
    {
        return $this->hasMany(\App\Models\JournalEntryLine::class, 'renter_id');
    }

    /**
     * Scope for active renters
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for tenant context
     */
    public function scopeForTenant($query, $tenantId = null)
    {
        $tenantId = $tenantId ?? (app('tenant')->id ?? null);
        return $query->where('tenant_id', $tenantId);
    }
}
