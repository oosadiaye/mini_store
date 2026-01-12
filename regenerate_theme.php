<?php

use App\Models\Tenant;
use App\Models\StoreConfig;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\StoreSetupWizardController;

try {
    $tenant = Tenant::first();
    app()->instance('tenant', $tenant);

    $config = StoreConfig::firstOrFail();
    $config->industry = 'fashion'; // Set to Fashion to test presets
    $config->save();

    echo "Config Industry set to: {$config->industry}\n";

    // Simulate Controller Method (Reflection to access private method or just copy logic temporarily)
    // Actually, let's just use the logic directly here to validte it, then save it.
    
    // SMART STYLING ENGINE LOGIC COPY
    $presets = [
        'fashion' => [
            'fonts' => ['heading' => 'Playfair Display', 'body' => 'Lato'],
            'radius' => '0px',
            'vibe' => 'minimalist',
            'placeholder' => 'assets/placeholders/fashion-product.jpg',
        ],
        'electronics' => [
            'fonts' => ['heading' => 'Roboto', 'body' => 'Inter'],
            'radius' => '4px',
            'vibe' => 'dark_mode',
            'placeholder' => 'assets/placeholders/tech-product.jpg',
        ],
        // ... (others skipped for brevity in test script)
    ];

    $style = $presets[$config->industry];

    $settings = [
        'identity' => [
            'name' => $config->store_name,
            'logo' => $config->logo_path,
            'primary_color' => $config->brand_color,
        ],
        'design' => [
            'fonts' => $style['fonts'],
            'radius' => $style['radius'],
            'vibe' => $style['vibe'],
            'layout' => $config->layout_preference,
        ],
        'generated_at' => now()->toIso8601String(),
    ];

    Storage::disk('tenant')->put('theme_settings.json', json_encode($settings, JSON_PRETTY_PRINT));
    echo "theme_settings.json updated.\n";
    echo "Radius: " . $style['radius'] . "\n";
    echo "Heading Font: " . $style['fonts']['heading'] . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
