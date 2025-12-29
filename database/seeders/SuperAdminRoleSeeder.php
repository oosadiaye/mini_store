<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminRoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Core Permissions for SuperAdmin
        $permissions = [
            'view tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',
            'impersonate tenants',
            'manage plans',
            'manage subscriptions',
            'manage staff', // Manage other SuperAdmin users
            'manage roles', // Manage roles and permissions
            'view system reports',
            'configure settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Create Roles and Assign Permissions

        // Super Admin (All permissions)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Support Agent (Read-only access to tenants and limited actions)
        $supportRole = Role::firstOrCreate(['name' => 'Support Agent', 'guard_name' => 'web']);
        $supportRole->givePermissionTo([
            'view tenants',
            'impersonate tenants',
            'view system reports',
        ]);

        // Manager (Can do everything except manage other staff/roles)
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo([
            'view tenants', 'create tenants', 'edit tenants', 'impersonate tenants',
            'manage plans', 'manage subscriptions', 'view system reports', 'configure settings'
        ]);
        
        // Output for feedback
        $this->command->info('SuperAdmin Roles and Permissions seeded successfully.');
    }
}
