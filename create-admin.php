<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Initialize tenant
$tenant = \App\Models\Tenant::find('dplux');
tenancy()->initialize($tenant);

// Add missing columns if they don't exist
try {
    \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT "staff" AFTER password');
    echo "Added role column\n";
} catch (\Exception $e) {
    echo "Role column already exists or error: " . $e->getMessage() . "\n";
}

try {
    \Illuminate\Support\Facades\DB::statement('ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER role');
    echo "Added is_active column\n";
} catch (\Exception $e) {
    echo "is_active column already exists or error: " . $e->getMessage() . "\n";
}

// Create admin user
\Illuminate\Support\Facades\DB::table('users')->insert([
    'name' => 'Jacob Osadiaye',
    'email' => 'osadiaye4real@gmail.com',
    'password' => bcrypt('12345678'),
    'role' => 'admin',
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "\n✅ Admin user created successfully!\n";
echo "Login at: http://dplux.localhost:8000/login\n";
echo "Email: osadiaye4real@gmail.com\n";
echo "Password: 12345678\n";
