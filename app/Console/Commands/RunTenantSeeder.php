<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;

class RunTenantSeeder extends Command
{
    protected $signature = 'tenant:seed {slug} {class}';
    protected $description = 'Run a seeder for a specific tenant';

    public function handle()
    {
        $slug = $this->argument('slug');
        $class = $this->argument('class');

        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            $this->error("Tenant '{$slug}' not found.");
            return 1;
        }

        $this->info("Running {$class} for tenant: {$slug} (ID: {$tenant->id})");

        // Set tenant context
        app()->instance('tenant', $tenant);
        config(['app.tenant_id' => $tenant->id]);
        
        // Also required for BelongsToTenant trait to pick it up if it uses a global scope
        // that looks at the config or app instance.
        // Assuming BelongsToTenant trait uses tenant() helper or config.

        // Force the seeder to run
        Artisan::call('db:seed', [
            '--class' => $class,
            '--force' => true,
        ], $this->output);

        $this->info("Done.");
        return 0;
    }
}
