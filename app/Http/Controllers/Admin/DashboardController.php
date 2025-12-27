<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'daily');
        
        // Date Range Calculation
        $query = \App\Models\Order::query();
        $chartQuery = \App\Models\Order::where('payment_status', 'paid');
        
        switch ($filter) {
            case 'weekly':
                $startDate = now()->subDays(7);
                $groupFormat = 'Y-m-d'; // Group by Day
                $labelFormat = 'M d';
                break;
            case 'monthly':
                $startDate = now()->subDays(30);
                $groupFormat = 'Y-m-d'; // Group by Day
                    $labelFormat = 'M d';
                break;
            case 'yearly':
                $startDate = now()->startOfYear();
                $groupFormat = 'Y-m'; // Group by Month
                    $labelFormat = 'M Y';
                break;
            case 'daily':
            default:
                $startDate = now()->startOfDay();
                $groupFormat = 'Y-m-d H'; // Group by Hour
                    $labelFormat = 'g A';
                break;
        }

        // Apply Date Filter to Stats (except products)
        $query->where('created_at', '>=', $startDate);
        
        // Stats
        $stats = [
            'total_sales' => (clone $query)->where('payment_status', 'paid')->sum('total'),
            'total_orders' => (clone $query)->count(),
            'pending_orders' => \App\Models\Order::where('status', 'pending')->count(), // Pending is always "Current" status, no date filter needed really, or maybe yes? Usually "Current Pending" is what matters. Keeping it all time pending.
            'total_products' => Product::count(),
            'total_customers' => \App\Models\Customer::count(),
            'low_stock_products' => Product::where('track_inventory', true)
                                        ->where('stock_quantity', '>', 0)
                                        ->where('stock_quantity', '<=', \DB::raw('low_stock_threshold'))
                                        ->count(),
        ];

        // Recent Orders (Unfiltered, just latest)
        $recent_orders = \App\Models\Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $low_stock_products = Product::where('track_inventory', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', \DB::raw('low_stock_threshold'))
            ->with('category')
            ->take(5)
            ->get();

        // Sales Chart Data
        $rawSales = $chartQuery->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy(function($date) use ($groupFormat) {
                return \Carbon\Carbon::parse($date->created_at)->format($groupFormat);
            });

        // Fill Gaps
        $sales_chart = [];
        // Logic to fill gaps based on period
        if ($filter === 'daily') {
                for ($i = 0; $i < 24; $i++) {
                    // If start date is today, we iterate hours 0-23
                    $key = $startDate->copy()->startOfDay()->addHours($i)->format('Y-m-d H');
                    $label = $startDate->copy()->startOfDay()->addHours($i)->format('g A');
                    $total = isset($rawSales[$key]) ? $rawSales[$key]->sum('total') : 0;
                    $sales_chart[] = ['label' => $label, 'total' => $total];
                }
        } elseif ($filter === 'yearly') {
            for ($i = 1; $i <= 12; $i++) {
                $date = now()->startOfYear()->month($i);
                $key = $date->format('Y-m');
                $label = $date->format('M');
                $total = isset($rawSales[$key]) ? $rawSales[$key]->sum('total') : 0;
                $sales_chart[] = ['label' => $label, 'total' => $total];
            }
        } else {
            // Weekly/Monthly (Days)
            $days = $filter === 'weekly' ? 7 : 30;
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $key = $date->format('Y-m-d');
                $label = $date->format('M d');
                $total = isset($rawSales[$key]) ? $rawSales[$key]->sum('total') : 0;
                $sales_chart[] = ['label' => $label, 'total' => $total];
            }
        }


        // Top Products (by quantity)
        $top_products = \App\Models\OrderItem::select('product_id', \DB::raw('SUM(quantity) as total_quantity'))
            ->where('created_at', '>=', $startDate) // Apply filter to top products too?
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Start of expiring products logic
        // Expiring Soon (Next 3 months)
        $expiring_products = Product::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(90))
            ->orderBy('expiry_date')
            ->take(5)
            ->get();

        $stats['expiring_soon'] = Product::where('is_active', true)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays(90))
            ->count();
        // End of expiring products logic

        return view('admin.dashboard', compact('stats', 'recent_orders', 'low_stock_products', 'sales_chart', 'top_products', 'expiring_products', 'filter'));
    }
}
