<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Payment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'entity_type',
        'entity_id',
        'amount',
        'unallocated_amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
        'journal_entry_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'unallocated_amount' => 'decimal:2',
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }
}
