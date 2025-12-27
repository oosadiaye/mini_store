<?php
/**
 * Quick Fix Script: Promote First User to Super Admin
 * 
 * Usage: Visit http://your-site/promote_admin.php
 * Security: Delete this file after use.
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

try {
    $user = User::first();
    
    if (!$user) {
        die("❌ No users found in database. Please run the installer first.");
    }

    $user->is_superadmin = true;
    $user->role = 'super_admin';
    $user->save();

    echo "<h1 style='color:green'>✅ Success!</h1>";
    echo "<p>User <strong>{$user->email}</strong> has been promoted to Super Admin.</p>";
    echo "<p><a href='/superadmin/login'>Click here to Login</a></p>";

} catch (\Exception $e) {
    die("❌ Error: " . $e->getMessage());
}
