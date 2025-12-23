<?php
// Quick script to create page_layouts table in tenant database
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get tenant
$tenant = \App\Models\Tenant::where('id', 'dplux')->first();

if ($tenant) {
    $tenant->run(function () {
        $sql = "CREATE TABLE IF NOT EXISTS page_layouts (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            page_name VARCHAR(255) NOT NULL UNIQUE,
            sections JSON NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        )";
        
        DB::statement($sql);
        echo "✅ Table 'page_layouts' created successfully in tenant database!\n";
    });
} else {
    echo "❌ Tenant 'dplux' not found!\n";
}
