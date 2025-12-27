<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of subscribed tenants.
     */
    public function subscriptions(Request $request)
    {
        $query = Tenant::with(['currentPlan', 'users' => function($q) {
            // Get the first admin user or main contact
            $q->where('role', 'admin'); 
        }]); // Assuming 'users' relationship exists and 'admin' is the role for the main user

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by Date Duration
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $dateType = $request->input('date_type', 'created_at'); // Default to Joined Date
            $dateField = match($dateType) {
                'subscription_end' => 'subscription_ends_at',
                'trial_end' => 'trial_ends_at',
                default => 'created_at'
            };

            if ($request->filled('date_from')) {
                $query->whereDate($dateField, '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate($dateField, '<=', $request->input('date_to'));
            }
        }

        $tenants = $query->latest()->paginate(20);

        return view('superadmin.reports.subscriptions', compact('tenants'));
    }
}
