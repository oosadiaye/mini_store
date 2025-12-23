<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductEnquiry extends Model
{
    protected $fillable = [
        'product_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'message',
        'status',
        'admin_reply',
        'replied_at',
        'replied_by',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Get the product this enquiry is about
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the admin who replied
     */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Scope for pending enquiries
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for replied enquiries
     */
    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    /**
     * Check if enquiry is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if enquiry has been replied
     */
    public function isReplied(): bool
    {
        return $this->status === 'replied';
    }
}
