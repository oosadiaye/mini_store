<?php
// Run via: php artisan tinker verify_login.php

$tenantId = 'test_tenant';
$email = 'admin@test.com';
$password = 'password123';

echo "Simulating Login for: $email on Tenant: $tenantId\n";

// 1. Bind Tenant
$tenant = \App\Models\Tenant::find($tenantId);
if (!$tenant) {
    echo "Tenant not found\n";
    exit;
}
app()->instance('tenant', $tenant);
echo "Tenant Bound: {$tenant->name}\n";

// 2. Attempt Login
if (\Illuminate\Support\Facades\Auth::attempt(['email' => $email, 'password' => $password])) {
    echo "SUCCESS: Login Successful!\n";
    echo "User: " . \Illuminate\Support\Facades\Auth::user()->name . "\n";
} else {
    echo "FAILURE: Login Failed!\n";
    
    // Debug: Check if user exists with simple query under this scope
    $user = \App\Models\User::where('email', $email)->first();
    if ($user) {
        echo "User found in scope. Hash check: " . (\Illuminate\Support\Facades\Hash::check($password, $user->password) ? 'PASS' : 'FAIL') . "\n";
    } else {
        echo "User NOT found in current scope (TenantScope active?)\n";
    }
}
