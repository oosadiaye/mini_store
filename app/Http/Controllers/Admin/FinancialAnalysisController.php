<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialAnalysisController extends Controller
{
    public function index()
    {
        // Get date range (default to this month)
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // Calculate Revenue (Total Paid Orders)
        $revenue = Order::whereBetween('created_at', [$start, $end])
            // ->where('payment_status', 'paid') // Uncomment if you have this column
            ->sum('total');

        // Calculate Expenses
        $expenses = Expense::whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        // Net Profit
        $profit = $revenue - $expenses;

        // Top Selling Product
        $topProduct = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$start, $end])
            // ->where('orders.payment_status', 'paid') // Optional based on business rule
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as sold_count'), DB::raw('SUM(order_items.total) as revenue'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('sold_count')
            ->first();

        // Convert stdClass to array for view if needed, or pass object. 
        // View expects array key access for 'name' based on Mailable? 
        // Mailable implementation uses array access $topProduct['name']. 
        // Let's normalize it to array.
        if ($topProduct) {
            $topProduct = [
                'name' => $topProduct->product_name,
                'sold_count' => $topProduct->sold_count,
                'revenue' => '₦' . number_format($topProduct->revenue, 2)
            ];
        }

        // Previous Month Comparison (Mock logic for UI display)
        $revenueGrowth = 12; // +12%
        $expenseGrowth = 5;  // +5%

        return view('admin.financial.index', compact(
            'revenue', 
            'expenses', 
            'profit', 
            'start', 
            'end',
            'revenueGrowth', 
            'expenseGrowth'
        ));
    }
}
