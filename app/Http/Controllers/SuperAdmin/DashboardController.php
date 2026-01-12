<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        
        // Monthly Revenue (₦) - Sum of completed subscription payments this month
        $monthlyRevenue = \App\Models\SubscriptionPayment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // New Signups this month
        $newSignups = Tenant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Recent Activity
        $recentActivity = \App\Models\AuditLog::with('user')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('superadmin.dashboard', compact(
            'totalTenants', 
            'monthlyRevenue', 
            'newSignups', 
            'recentActivity'
        ));
    }
}
