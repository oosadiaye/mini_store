<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketCategory extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'assigned_to'];

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'category_id');
    }
}
