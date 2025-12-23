<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['entry_date' => 'date'];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    // Auto calculate totals
    public function getTotalDebitAttribute()
    {
        return $this->lines()->sum('debit');
    }

    public function getTotalCreditAttribute()
    {
        return $this->lines()->sum('credit');
    }
}
