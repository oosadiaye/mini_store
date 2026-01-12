<?php

use App\Models\StoreConfig;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\StoreSetupWizardController;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = Tenant::first();
echo "Tenant: " . ($tenant ? $tenant->id : 'None') . "\n";

// Access the controller logic via valid means or just replicate it to SAVE the file
$config = StoreConfig::first();
$selectedCatIds = $config->selected_categories ?? [];

$fullCategories = [];
if (!empty($selectedCatIds)) {
        $selectedIds = is_array($selectedCatIds) ? $selectedCatIds : json_decode($selectedCatIds, true);
        $fullCategories = \App\Models\Category::whereIn('id', $selectedIds)
        ->get()
        ->map(fn($c) => [
            'id' => $c->id, 
            'name' => $c->name, 
            'slug' => $c->slug,
            'public_display_name' => $c->public_display_name ?? $c->name,
            'image' => $c->image_path
        ])
        ->values()
        ->toArray();
}

$schema = [
    'catalog' => [
        'visible_categories' => $fullCategories
    ],
    'generated_at' => now()->toIso8601String()
];

// Save to Tenant Storage
Storage::disk('tenant')->put('generated_theme_schema.json', json_encode($schema, JSON_PRETTY_PRINT));

echo "Saved generated_theme_schema.json to tenant storage.\n";
