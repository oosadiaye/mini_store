<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementRead extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql'; // Ensure it always uses the central database
    
    public $timestamps = false;

    protected $fillable = [
        'announcement_id',
        'user_id',
        'tenant_id',
        'created_at'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
