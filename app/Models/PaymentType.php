<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bank_details' => 'array',
        'is_active' => 'boolean',
        'require_gateway' => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'gl_account_id');
    }
}
