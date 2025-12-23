<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';
    protected $guarded = ['id']; // sub_ledger_type is handled here because it's not in guarded

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function transactions()
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    // Helpers
    public function getBalanceAttribute()
    {
        // Simple balance calc: Debits - Credits (Normal Debit) or Credits - Debits (Normal Credit)
        $debits = $this->transactions()->sum('debit');
        $credits = $this->transactions()->sum('credit');

        if (in_array($this->account_type, ['asset', 'expense'])) {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }
}
