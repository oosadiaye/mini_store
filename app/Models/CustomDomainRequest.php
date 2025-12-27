<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomDomainRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'domain',
        'status',
        'ssl_status',
        'dns_instructions',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the custom domain request.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the user who approved the request.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if SSL is active.
     */
    public function hasSsl(): bool
    {
        return $this->ssl_status === 'active';
    }
}
