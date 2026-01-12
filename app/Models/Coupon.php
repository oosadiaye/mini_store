<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes, \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'type',
        'value',
        'min_spend',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_spend' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($total)
    {
        if ($this->min_spend && $total < $this->min_spend) {
            return 0;
        }

        if ($this->type === 'fixed') {
            return min($this->value, $total);
        }

        return round(($total * $this->value) / 100, 2);
    }
}
