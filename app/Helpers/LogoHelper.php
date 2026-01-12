<?php

namespace App\Helpers;

class LogoHelper
{
    /**
     * Extract initials from store name
     */
    public static function getInitials(string $name): string
    {
        $name = trim($name);
        
        if (empty($name)) {
            return 'S'; // Default fallback
        }
        
        $words = preg_split('/\s+/', $name);
        
        // If multiple words, take first letter of first two words
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        
        // Single word: take first two letters or just first if too short
        return strtoupper(substr($name, 0, min(2, strlen($name))));
    }
    
    /**
     * Get color scheme based on store name (consistent per store)
     */
    public static function getColorScheme(string $name): array
    {
        $colors = [
            ['bg' => '#4F46E5', 'text' => '#FFFFFF'], // Indigo
            ['bg' => '#10B981', 'text' => '#FFFFFF'], // Green
            ['bg' => '#F59E0B', 'text' => '#FFFFFF'], // Amber
            ['bg' => '#EF4444', 'text' => '#FFFFFF'], // Red
            ['bg' => '#8B5CF6', 'text' => '#FFFFFF'], // Purple
            ['bg' => '#06B6D4', 'text' => '#FFFFFF'], // Cyan
            ['bg' => '#EC4899', 'text' => '#FFFFFF'], // Pink
            ['bg' => '#000000', 'text' => '#FFFFFF'], // Black
        ];
        
        // Use CRC32 hash to consistently select same color for same name
        $index = abs(crc32($name)) % count($colors);
        return $colors[$index];
    }
    
    /**
     * Generate SVG logo from initials
     */
    public static function generateSvg(string $initials, int $size = 64, ?array $colors = null): string
    {
        if (!$colors) {
            $name = app()->bound('tenant') ? app('tenant')->name : 'Store';
            $colors = self::getColorScheme($name);
        }
        
        $fontSize = round($size * 0.4);
        $borderRadius = round($size * 0.125);
        
        $svg = <<<SVG
<svg width="{$size}" height="{$size}" xmlns="http://www.w3.org/2000/svg">
  <rect width="100%" height="100%" fill="{$colors['bg']}" rx="{$borderRadius}"/>
  <text 
    x="50%" 
    y="50%" 
    dominant-baseline="middle" 
    text-anchor="middle" 
    font-family="Inter, -apple-system, BlinkMacSystemFont, sans-serif" 
    font-weight="600" 
    font-size="{$fontSize}px" 
    fill="{$colors['text']}">
    {$initials}
  </text>
</svg>
SVG;
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    
    /**
     * Get logo (custom or generated)
     */
    public static function getLogo(int $size = 64): string
    {
        // Read fresh data from database instead of cached tenant()->data
        if (app()->bound('tenant')) {
            $tenant = app('tenant');
            $freshData = json_decode(
                \Illuminate\Support\Facades\DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->value('data') ?? '{}',
                true
            );
        } else {
            $freshData = [];
        }
        
        \Log::info("LogoHelper::getLogo called", [
            'has_logo' => !empty($freshData['logo']),
            'logo_path' => $freshData['logo'] ?? 'none'
        ]);
        
        // If custom logo exists, return its URL
        if (!empty($freshData['logo'])) {
             // Retrieve via tenant media route
            return route('tenant.media', ['path' => $freshData['logo']]);
        }
        
        // Generate from initials
        $name = app()->bound('tenant') ? app('tenant')->name : 'Store';
        $initials = self::getInitials($name);
        return self::generateSvg($initials, $size);
    }
    
    /**
     * Get favicon (custom or generated)
     */
    public static function getFavicon(): string
    {
        // Read fresh data from database instead of cached tenant()->data
        if (app()->bound('tenant')) {
            $tenant = app('tenant');
            $freshData = json_decode(
                \Illuminate\Support\Facades\DB::table('tenants')
                    ->where('id', $tenant->id)
                    ->value('data') ?? '{}',
                true
            );
        } else {
            $freshData = [];
        }
        
        // If custom favicon exists, return its URL with cache busting
        if (!empty($freshData['favicon'])) {
             // Retrieve via tenant media route with cache busting param
            return route('tenant.media', ['path' => $freshData['favicon'], 'v' => time()]);
        }
        
        // Generate 32x32 favicon from initials
        $name = app()->bound('tenant') ? app('tenant')->name : 'Store';
        $initials = self::getInitials($name);
        return self::generateSvg($initials, 32);
    }
}
