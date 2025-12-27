<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    use \App\Traits\BelongsToTenant;
    
    protected $fillable = [
        'page',
        'block_id',
        'block_type',
        'content',
        'settings',
        'order',
        'is_active',
    ];
    
    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get blocks for a specific page
     */
    public static function getPageBlocks(string $page)
    {
        return static::where('page', $page)
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->keyBy('block_id');
    }
    
    /**
     * Update a specific field in the content JSON
     */
    public function updateField(string $field, $value)
    {
        $content = $this->content ?? [];
        data_set($content, $field, $value);
        $this->content = $content;
        $this->save();
        
        return $this;
    }
}
