<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist (idempotent)
        $this->call(TenantRoleSeeder::class);

        $adminRole = Role::where('name', 'Store Admin')->first();

        if (!$adminRole) {
            $this->command->error("Store Admin role not found. Migration aborted.");
            return;
        }

        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            // Logic to map legacy 'role' string to new Role
            // Assuming 'admin' was the primary legacy role
            // Also protect ID 1 (Owner)
            
            if ($user->hasRole('Store Admin')) {
                continue;
            }

            if ($user->role === 'admin' || $user->id === 1) {
                $user->assignRole($adminRole);
                $this->command->info("Assigned 'Store Admin' to user: {$user->email}");
                $count++;
            }
            
            // Handle other mappings if known... 
            // For now, default others to strictly NO role unless we know what they are. 
            // Or maybe 'Sales'? Better safe than sorry.
        }

        $this->command->info("Migrated {$count} users to Spatie Roles.");
    }
}
