<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\Plan;

class SubscriptionRequestController extends Controller
{
    public function index()
    {
        $transactions = DB::table('subscription_transactions')
            ->join('tenants', 'subscription_transactions.tenant_id', '=', 'tenants.id')
            ->join('plans', 'subscription_transactions.plan_id', '=', 'plans.id')
            ->select(
                'subscription_transactions.*', 
                'tenants.name as tenant_name', 
                'tenants.id as tenant_real_id',
                'plans.name as plan_name'
            )
            ->where('subscription_transactions.status', 'pending') // Only pending requests
            ->orderBy('subscription_transactions.created_at', 'desc')
            ->paginate(10);

        return view('admin.subscription_requests.index', compact('transactions'));
    }

    public function approve($id)
    {
        $transaction = DB::table('subscription_transactions')->where('id', $id)->first();

        if (!$transaction) {
            return back()->with('error', 'Transaction not found.');
        }

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Transaction is no longer pending.');
        }

        $tenant = Tenant::find($transaction->tenant_id);
        $plan = Plan::find($transaction->plan_id);

        if (!$tenant || !$plan) {
             return back()->with('error', 'Tenant or Plan not found.');
        }

        // Activate Plan
        $tenant->update([
            'plan_id' => $plan->id,
            'is_active' => true,
            'subscription_ends_at' => now()->addDays($plan->duration_days),
        ]);

        // Update Transaction
        DB::table('subscription_transactions')->where('id', $id)->update([
            'status' => 'approved',
            'updated_at' => now()
        ]);

        return back()->with('success', "Subscription approved for {$tenant->name}.");
    }

    public function reject(Request $request, $id)
    {
         $transaction = DB::table('subscription_transactions')->where('id', $id)->first();

        if (!$transaction) {
            return back()->with('error', 'Transaction not found.');
        }

         DB::table('subscription_transactions')->where('id', $id)->update([
            'status' => 'rejected',
            'notes' => $request->input('reason', 'Rejected by admin'),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Subscription request rejected.');
    }
}
