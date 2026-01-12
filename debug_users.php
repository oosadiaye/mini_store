<?php
// Run this via: php artisan tinker debug_users.php

$tenants = \App\Models\Tenant::all();
echo "\n--- TENANTS ---\n";
foreach ($tenants as $tenant) {
    echo "ID: {$tenant->id} | Name: {$tenant->name} | Plan: {$tenant->plan_id}\n";
}

$users = \App\Models\User::withoutGlobalScope(\App\Scopes\TenantScope::class)->get();
echo "\n--- USERS ---\n";
foreach ($users as $user) {
    echo "ID: {$user->id} | Name: {$user->name} | Email: {$user->email} | Role: {$user->role} | Tenant: {$user->tenant_id}\n";
}
