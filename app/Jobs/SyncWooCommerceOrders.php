<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\Order;
use App\Services\WooCommerceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncWooCommerceOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenant;

    /**
     * Create a new job instance.
     */
    public function __construct(Tenant $tenant = null)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(WooCommerceService $service): void
    {
        if ($this->tenant) {
            $this->syncForTenant($this->tenant, $service);
        } else {
            // If no specific tenant, loop through all tenants with WC enabled
            // This part might be better handled by a different scheduled command that dispatches this job per tenant.
            // For now, we assume this job is dispatched per tenant.
        }
    }
    
    protected function syncForTenant(Tenant $tenant, WooCommerceService $service)
    {
        $service->setCredentialsFromTenant($tenant);

        if (!$service->isConfigured()) {
            return;
        }
        
        // Fetch recent orders (e.g. last 24 hours or since last sync)
        // Ideally store last_sync_timestamp in settings.
        $lastSync = $tenant->settings['woocommerce_last_sync'] ?? null;
        $params = ['per_page' => 20];
        
        if ($lastSync) {
            $params['after'] = \Carbon\Carbon::parse($lastSync)->toISOString();
        }

        try {
            $wcOrders = $service->fetchOrders($params);
            
            foreach ($wcOrders as $wcOrder) {
                // Delegate to Service to handle processing (shared with Webhook)
                $service->processOrder($tenant, $wcOrder);
            }
            
            // Update last sync time
            $settings = $tenant->settings;
            $settings['woocommerce_last_sync'] = now()->toIso8601String();
            $tenant->settings = $settings;
            $tenant->save();
            
        } catch (\Exception $e) {
            Log::error("WooCommerce Sync Failed for Tenant {$tenant->id}: " . $e->getMessage());
        }
    }
}
