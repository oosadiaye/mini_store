<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SecureFileUploader;

class SubscriptionController extends Controller
{
    /**
     * @var SecureFileUploader
     */
    protected $uploader;

    public function __construct(SecureFileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * Display a listing of available plans.
     */
    public function index()
    {
        $tenant = app('tenant');
        $subscriptionService = new \App\Services\SubscriptionService();
        
        // Fetch all active plans sorted by price
        $plans = Plan::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        // Calculate proration for each plan
        $prorations = [];
        foreach ($plans as $plan) {
            $prorations[$plan->id] = $subscriptionService->getProrationDetails($tenant, $plan);
        }

        // Check for pending transaction
        $hasPending = $subscriptionService->hasPendingTransaction($tenant);

        return view('tenant.subscription.index', compact('plans', 'prorations', 'hasPending'));
    }

    /**
     * Store subscription selection.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $tenant = app('tenant');
        $subscriptionService = new \App\Services\SubscriptionService();

        // Check if upgrade is allowed
        $upgradeCheck = $subscriptionService->canUpgrade($tenant, $plan);
        if (!$upgradeCheck['can_upgrade']) {
            return back()->with('error', $upgradeCheck['reason']);
        }

        // Check if plan is free or on trial, skip payment
        if ($plan->price > 0) {
            try {
                // Calculate proration if upgrading
                $proration = $subscriptionService->getProrationDetails($tenant, $plan);
                
                $service = new \App\Services\SubscriptionPaymentService();
                $init = $service->initialize($tenant, $plan);
                
                if (isset($init['is_manual']) && $init['is_manual']) {
                    return view('tenant.subscription.bank_transfer', [
                        'plan' => $plan,
                        'paymentConfig' => $init['bank_details'],
                        'proration' => $proration
                    ]);
                }
                
                return redirect($init['checkout_url']);
            } catch (\Exception $e) {
                return back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
            }
        }

        // Free plan logic
        $tenant->update([
            'plan_id' => $plan->id,
            'is_active' => true,
            'subscription_ends_at' => now()->addDays($plan->duration_days),
        ]);

        return redirect()->route('admin.dashboard', ['tenant' => $tenant->slug])
            ->with('success', "You have successfully subscribed to the {$plan->name} plan.");
    }

    public function submitPayment(Request $request)
    {
        $tenant = app('tenant');
        $subscriptionService = new \App\Services\SubscriptionService();
        
        // Check for duplicate pending payment
        if ($subscriptionService->hasPendingTransaction($tenant)) {
            return back()->with('error', 'You already have a pending payment. Please wait for approval.');
        }
        
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        
        // Calculate proration
        $proration = $subscriptionService->calculateProration($tenant, $plan);

        if ($request->hasFile('proof')) {
            $path = $this->uploader->upload($request->file('proof'), 'subscription-proofs', 'local', ['application/pdf', 'image/jpeg', 'image/png']);
            
            // Generate Reference
            $reference = 'MAN-' . $tenant->id . '-' . time();

            \DB::table('subscription_transactions')->insert([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'old_plan_id' => $tenant->plan_id,
                'amount' => $proration['amount_due'],
                'prorated_credit' => $proration['credit'],
                'unused_days' => $proration['unused_days'],
                'reference' => $reference,
                'status' => 'pending',
                'proof_path' => $path,
                'notes' => $validated['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug])
                ->with('warning', 'Payment submitted successfully! Your subscription is pending approval.');
        }

        return back()->with('error', 'Please upload a proof of payment.');
    }

    public function callback(Request $request)
    {
        $tenant = app('tenant');
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->query('transaction_id');

        if (!$reference) {
            return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug])
                ->with('error', 'No transaction reference found.');
        }

        try {
            $service = new \App\Services\SubscriptionPaymentService();
            $verification = $service->verify($reference);

            if ($verification['success']) {
                $planId = $verification['metadata']['plan_id'] ?? null;
                
                if (!$planId) {
                     return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug])
                        ->with('error', 'Could not verify plan details from payment.');
                }

                $plan = Plan::find($planId);
                
                if ($plan) {
                    $tenant->update([
                        'plan_id' => $plan->id,
                        'is_active' => true,
                        'subscription_ends_at' => now()->addDays($plan->duration_days),
                    ]);

                     return redirect()->route('admin.dashboard', ['tenant' => $tenant->slug])
                        ->with('success', "Payment successful! You are now subscribed to {$plan->name}.");
                }
            }
            
            return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug])
                ->with('error', 'Payment verification failed: ' . ($verification['message'] ?? 'Unknown error'));

        } catch (\Exception $e) {
            return redirect()->route('tenant.subscription.index', ['tenant' => $tenant->slug])
                ->with('error', 'Error verifying payment: ' . $e->getMessage());
        }
    }
}
