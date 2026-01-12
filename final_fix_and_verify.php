<?php

use App\Models\Category;
use App\Models\StoreConfig;
use App\Models\Tenant;
use App\Services\StorefrontService;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = Tenant::first();
echo "Tenant: " . ($tenant ? $tenant->id : 'None') . "\n";

// 1. Force Visibility on Selected Categories
$config = StoreConfig::first();
$ids = $config->selected_categories;
if (is_string($ids)) $ids = json_decode($ids, true);

if ($ids) {
    Category::whereIn('id', $ids)->update(['is_visible_online' => true, 'is_active' => true]);
    echo "Forced visibility for categories: " . implode(', ', $ids) . "\n";
}

// 2. Test Service Logic
$service = new StorefrontService();
$data = $service->getHomeData($tenant);

echo "Category Sections Count: " . count($data['category_sections']) . "\n";
foreach ($data['category_sections'] as $section) {
    echo " - " . $section['category_name'] . " (Products: " . $section['products']->count() . ")\n";
}
