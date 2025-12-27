<?php

use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

// Simple script to insert Hero section data
$tenant = Tenant::first();

if (!$tenant) {
    echo "No tenant found!\n";
    exit(1);
}

// Switch to tenant database
DB::purge('tenant');
config(['database.connections.tenant.database' => 'tenant' . $tenant->id]);
DB::reconnect('tenant');

$heroSections = json_encode([[
    'id' => 'hero-1',
    'type' => 'hero',
    'enabled' => true,
    'order' => 1,
    'title' => 'Refined Retail Reimagined.',
    'content' => 'Discover a curated collection of premium essentials designed to elevate your everyday lifestyle.',
    'settings' => [
        'button_text' => 'Start Shopping',
        'button_link' => '/products',
        'background_color' => '#1a1a2e',
        'overlay_color' => '#000000',
        'overlay_opacity' => 40,
    ]
]]);

// Check if record exists
$exists = DB::connection('tenant')->table('page_layouts')
    ->where('page_name', 'home')
    ->exists();

if ($exists) {
    DB::connection('tenant')->table('page_layouts')
        ->where('page_name', 'home')
        ->update([
            'sections' => $heroSections,
            'is_active' => true,
            'updated_at' => now()
        ]);
    echo "✅ Hero section UPDATED successfully!\n";
} else {
    DB::connection('tenant')->table('page_layouts')
        ->insert([
            'page_name' => 'home',
            'sections' => $heroSections,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    echo "✅ Hero section CREATED successfully!\n";
}

echo "Done!\n";
