<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StorefrontTemplate;

class ThemeSetting extends Model
{
    /**
     * Scope a query to a specific theme slug.
     */
    public function scopeForTheme($query, $slug)
    {
        return $query->where('theme_slug', $slug);
    }

    /**
     * Scope a query to only active theme settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the active theme slug for the current tenant.
     * Currently returns the default reference theme.
     */
    public static function getActiveThemeSlug()
    {
        $setting = self::where('is_active', true)->with('template')->first();
        return $setting && $setting->template ? $setting->template->slug : 'modern-minimal';
    }

    /**
     * Get settings for the active theme.
     */
    public static function getSettings()
    {
        $slug = self::getActiveThemeSlug();
        $record = self::forTheme($slug)->first();
        return $record ? $record->settings : [];
    }

    protected $table = 'theme_settings';

    protected $fillable = [
        'tenant_id',
        'theme_slug',
        'settings',
        'colors',
        'fonts',
        'layout_settings',
        'custom_css',
        'is_active',
        'template_id',
    ];

    protected $casts = [
        'settings' => 'array',
        'colors' => 'array',
        'fonts' => 'array',
        'layout_settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relationship to the storefront template.
     */
    public function template()
    {
        return $this->belongsTo(StorefrontTemplate::class, 'template_id');
    }

    public function getColorsAttribute()
    {
        return $this->settings['colors'] ?? [];
    }

    public function getFontsAttribute()
    {
        return $this->settings['fonts'] ?? [];
    }

    public function getLayoutSettingsAttribute()
    {
        return $this->settings['layout_settings'] ?? [];
    }

    public function getCustomCssAttribute()
    {
        return $this->settings['custom_css'] ?? '';
    }
}
