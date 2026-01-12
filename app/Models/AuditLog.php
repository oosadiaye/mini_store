<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = ['tenant_id', 'user_id', 'action', 'description', 'ip_address', 'details'];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
