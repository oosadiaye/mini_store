<?php
// Run via: php artisan tinker verify_password_reset.php

$tenantId = 'test_tenant';
$newPassword = 'password123';

echo "Testing Password Reset for Tenant: $tenantId\n";

$user = \App\Models\User::withoutGlobalScope(\App\Scopes\TenantScope::class)
    ->where('tenant_id', $tenantId)
    ->where('role', 'admin')
    ->first();

if (!$user) {
    echo "User not found!\n";
    exit;
}

echo "Found User: {$user->email} (ID: {$user->id})\n";
echo "Old Hash: " . substr($user->password, 0, 10) . "...\n";

// Reset Password
$user->update([
    'password' => \Illuminate\Support\Facades\Hash::make($newPassword)
]);

$user->refresh();
echo "New Hash: " . substr($user->password, 0, 10) . "...\n";

if (\Illuminate\Support\Facades\Hash::check($newPassword, $user->password)) {
    echo "SUCCESS: Hash matches '$newPassword'\n";
} else {
    echo "FAILURE: Hash does not match!\n";
}
