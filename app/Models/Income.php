<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $guarded = ['id'];
    protected $casts = ['transaction_date' => 'date'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
