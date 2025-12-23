<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Tenant\ChartOfAccountsSeeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TenantPageSeeder::class,
            ChartOfAccountsSeeder::class,
            Tenant\CleanRetailThemeSeeder::class,
        ]);
    }
}
