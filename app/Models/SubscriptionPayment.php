<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'amount',
        'payment_method',
        'status',
        'payment_proof',
        'transaction_reference',
        'metadata',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'approved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
