<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Product;
use App\Mail\ProductExpiryNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckProductExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for products expiring in 6 months and notify admins.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expiring products...');

        // Filter: Exactly 180 days from now
        $targetDate = Carbon::now()->addDays(180)->format('Y-m-d');
        
        // Get all active tenants
        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $this->info("Processing tenant: {$tenant->name}");

            // Find products expiring on the target date for this tenant
            $expiringProducts = Product::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereDate('expiry_date', $targetDate)
                ->where('is_active', true)
                ->get();

            if ($expiringProducts->isNotEmpty()) {
                $this->info("Found " . $expiringProducts->count() . " products expiring in 6 months.");
                
                // Get admin email (tenant email)
                $email = $tenant->email ?? $tenant->data['company_email'] ?? null;

                if ($email) {
                    try {
                        Mail::to($email)->send(new ProductExpiryNotification($expiringProducts));
                        $this->info("Notification sent to {$email}");
                    } catch (\Exception $e) {
                        $this->error("Failed to send email to {$email}: " . $e->getMessage());
                    }
                } else {
                    $this->warn("No email found for tenant {$tenant->name}");
                }
            }
        }

        $this->info('Expiry check completed.');
    }
}
