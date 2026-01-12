<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    use HasFactory;

    protected $connection = 'mysql'; // Ensure it always uses the central database

    protected $fillable = [
        'title',
        'content',
        'type',
        'attachment_type',
        'attachment_path',
        'action_url',
        'target_type',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'announcement_tenant');
    }

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }
    
    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment_path) {
            return null;
        }

        if (filter_var($this->attachment_path, FILTER_VALIDATE_URL)) {
            return $this->attachment_path;
        }

        return route('global.media', ['path' => $this->attachment_path]);
    }

    /**
     * Scope a query to only include active announcements.
     */
    public function scopeActive(Builder $query)
    {
        $now = now();
        return $query->where('start_at', '<=', $now)
                     ->where('end_at', '>=', $now);
    }
}
