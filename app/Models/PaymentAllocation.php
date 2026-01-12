<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class PaymentAllocation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'payment_id',
        'allocatable_type',
        'allocatable_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function allocatable()
    {
        return $this->morphTo();
    }
}
