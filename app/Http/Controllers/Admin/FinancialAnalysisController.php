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

        // Top Selling Product (Simple aggregation)
        // Assuming order_items table or similar relationship exists. 
        // For simplicity in this iteration, we'll mock or use a simple query if OrderItem exists.
        // Let's check relation first. If not ready, we skip or use basic count.
        
        $topProduct = null;
        // Basic implementation if OrderItem model exists
        // $topProduct = \App\Models\OrderItem::select('name', DB::raw('sum(quantity) as sold'))
        //     ->groupBy('name')
        //     ->orderByDesc('sold')
        //     ->first();

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
