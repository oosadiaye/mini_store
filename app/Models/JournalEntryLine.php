<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntryLine extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $guarded = ['id'];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public function renter()
    {
        return $this->belongsTo(Renter::class);
    }
}
