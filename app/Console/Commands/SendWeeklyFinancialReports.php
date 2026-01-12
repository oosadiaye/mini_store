<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\Expense;
use App\Mail\WeeklyFinancialReport;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendWeeklyFinancialReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:weekly-financial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly financial analysis summary email to all active tenants.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Weekly Financial Reports...');

        // Get all active tenants
        $tenants = Tenant::where('is_active', true)->where('is_suspended', false)->get();

        $start = Carbon::now()->subWeek()->startOfWeek();
        $end = Carbon::now()->subWeek()->endOfWeek();
        
        $this->info("Period: " . $start->format('Y-m-d') . " to " . $end->format('Y-m-d'));

        foreach ($tenants as $tenant) {
            $this->info("Processing: " . $tenant->name);

            // Important: We must set the tenant context to access tenant-scoped data 
            // BUT our models use BelongsToTenant. 
            // In a console command, we don't have the HTTP middleware to set the scope automatically.
            // We need to manually scope our queries or temporarily set the tenant context if we had a service for that.
            // Since we are iterating, we can just query by tenant_id directly on the models IF we disable global scope or explicitly check.
            
            // However, typical multi-tenant packages often need initialization. 
            // Assuming simplified approach: Query tables by tenant_id.
            
            // NOTE: Order and Expense likely use BelongsToTenant trait which adds a global scope.
            // We need to be careful. The global scope usually looks for `session('tenant_id')` or similar.
            // In Console, that session is empty.
            // Best approach: Use `withoutGlobalScope` and manually filter, OR use `tenancy()->initialize($tenant)` if using stancl/tenancy (not using here).
            
            // Let's assume manual filtering for safety in this custom implementation.
            
            // Calculate Revenue
            $revenue = Order::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereBetween('created_at', [$start, $end])
                //->where('payment_status', 'paid')
                ->sum('total');

            // Calculate Expenses
            $expenses = Expense::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->whereBetween('expense_date', [$start, $end])
                ->sum('amount');

            $netProfit = $revenue - $expenses;
            
            // Top Product
            // Use DB query for performance and avoiding model scope complexity in loop
            $topProductRaw = \Illuminate\Support\Facades\DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.tenant_id', $tenant->id)
                ->whereBetween('orders.created_at', [$start, $end])
                ->select('order_items.product_name', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as sold_count'), \Illuminate\Support\Facades\DB::raw('SUM(order_items.total) as revenue'))
                ->groupBy('order_items.product_name')
                ->orderByDesc('sold_count')
                ->first();

            $topProduct = null;
            if ($topProductRaw) {
                $topProduct = [
                    'name' => $topProductRaw->product_name,
                    'sold_count' => $topProductRaw->sold_count,
                    'revenue' => '₦' . number_format($topProductRaw->revenue, 2)
                ];
            }

            // Format Currency (naive)
            $fmtRevenue = '₦' . number_format($revenue, 2);
            $fmtExpenses = '₦' . number_format($expenses, 2);
            $fmtProfit = '₦' . number_format($netProfit, 2);
            
            // Send Email
            if ($tenant->email) {
                try {
                    Mail::to($tenant->email)->send(new WeeklyFinancialReport(
                        $tenant->name,
                        $tenant->slug,
                        $start->format('M d'),
                        $end->format('M d'),
                        $fmtRevenue,
                        $fmtExpenses,
                        $fmtProfit,
                        $topProduct
                    ));
                    $this->info("Sent to: " . $tenant->email);
                } catch (\Exception $e) {
                    $this->error("Failed to send to {$tenant->email}: " . $e->getMessage());
                }
            }
        }

        $this->info('Weekly Financial Reports sent successfully.');
    }
}
