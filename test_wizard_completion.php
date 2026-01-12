<?php
// test_wizard_completion.php

use App\Models\StoreConfig;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\StoreSetupWizardController;

echo "--- Wizard Completion Test ---\n";

$tenant = app('tenant');
echo "Tenant: " . $tenant->name . "\n";

// 1. Ensure Config exists or create mock
$config = StoreConfig::firstOrNew(['id' => 1]);
$config->store_name = 'Test Store Generated';
$config->industry = 'electronics';
$config->layout_preference = 'brand_showcase'; // Testing the NEW preset
$config->brand_color = '#ef4444'; // Red-ish
$config->selected_categories = [1, 2]; // Mock IDs
$config->save();

echo "Config Mocked: " . $config->layout_preference . "\n";

// 2. Instantiate Controller and call generateThemeSettings (public via helper or reflection? It's private.)
// Actually, let's just use the Finish route logic manually or expose generation.
// Since it's private, we'll use Reflection for this verification script.

$controller = new StoreSetupWizardController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('generateThemeSettings');
$method->setAccessible(true);

$settings = $method->invoke($controller, $config);

echo "Settings Generated:\n";
// print_r($settings);
echo "Layout Mode: " . $settings['design']['layout_mode'] . "\n";
echo "Components: " . implode(', ', $settings['design']['components']) . "\n";

// 3. Verify Components match Preset
$expected = ['storefront.hero-banner', 'storefront.text-block', 'storefront.featured-product-carousel', 'storefront.newsletter-signup'];
if ($settings['design']['components'] === $expected) {
    echo "PASS: Components match Brand Showcase preset.\n";
} else {
    echo "FAIL: Components do not match.\n";
    print_r($settings['design']['components']);
}

// 4. Simulate Save to Storage
Storage::disk('tenant')->put('theme_settings.json', json_encode($settings, JSON_PRETTY_PRINT));
echo "Saved theme_settings.json to tenant disk.\n";

// 5. Verify File Exists
if (Storage::disk('tenant')->exists('theme_settings.json')) {
    echo "PASS: theme_settings.json exists.\n";
    echo "Content Preview:\n";
    echo substr(Storage::disk('tenant')->get('theme_settings.json'), 0, 200) . "...\n";
} else {
    echo "FAIL: File not found.\n";
}
