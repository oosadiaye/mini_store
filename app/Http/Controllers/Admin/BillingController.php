<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\SecureFileUploader;

class BillingController extends Controller
{
    /**
     * @var SecureFileUploader
     */
    protected $uploader;

    public function __construct(SecureFileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function index()
    {
        $tenant = app('tenant');
        $currentPlan = $tenant->plan;
        
        // Ensure plans are loaded correctly
        $plans = Plan::orderBy('price')->get();

        $history = SubscriptionPayment::where('tenant_id', $tenant->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('tenant.admin.billing.index', compact('tenant', 'currentPlan', 'plans', 'history'));
    }

    public function checkout(Plan $plan)
    {
        $gateways = PaymentGateway::where('is_active', true)->get();
        return view('tenant.admin.billing.checkout', compact('plan', 'gateways'));
    }

    public function store(Request $request, Plan $plan)
    {
        $request->validate([
            'payment_method' => 'required',
            'proof' => 'required_if:payment_method,manual|file|max:2048', // 2MB max
        ]);

        $paymentMethod = $request->payment_method;
        $tenant = app('tenant');
        $user = auth()->user(); // Assuming tenant admin is logged in
        $reference = strtoupper(substr($paymentMethod, 0, 3) . '-' . Str::random(10));
        
        // 1. Create Pending Payment Record
        $payment = SubscriptionPayment::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
            'transaction_reference' => $reference,
            'metadata' => ['notes' => $request->notes],
        ]);

        // 2. Handle Manual
        if ($paymentMethod === 'manual') {
             $path = null;
            if ($request->hasFile('proof')) {
                $path = $this->uploader->upload($request->file('proof'), 'payment-proofs', 'local', ['application/pdf', 'image/jpeg', 'image/png']);
            }
            $payment->update(['payment_proof' => $path]);

            return redirect()->route('admin.billing.index')->with('success', 'Payment submitted for approval. Subscription will activate once approved.');
        }

        // 3. Handle Gateways
        try {
            $paymentService = new \App\Services\PaymentService();
            $callbackUrl = route('admin.billing.callback', ['gateway' => $paymentMethod]);

            if ($paymentMethod === 'paystack') {
                $redirectUrl = $paymentService->initializePaystack($plan->price, $user->email, $reference, $callbackUrl);
                return redirect($redirectUrl);
            }

            if ($paymentMethod === 'flutterwave') {
                $redirectUrl = $paymentService->initializeFlutterwave($plan->price, $user->email, $reference, $callbackUrl, $tenant->name);
                return redirect($redirectUrl);
            }

        } catch (\Exception $e) {
            $payment->update(['status' => 'failed', 'metadata' => array_merge($payment->metadata ?? [], ['error' => $e->getMessage()])]);
            return back()->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }

        return back()->with('error', 'Payment method not implemented yet.');
    }

    public function callback(Request $request, $gateway)
    {
        $paymentService = new \App\Services\PaymentService();
        $success = false;
        $reference = $request->input('reference') ?? $request->input('tx_ref'); // Paystack vs Flutterwave

        // Find payment by reference
        $payment = SubscriptionPayment::where('transaction_reference', $reference)->first();

        if (!$payment) {
            // Flutterwave sends transaction_id (status=successful&tx_ref=XYZ&transaction_id=123)
            // Paystack sends reference (reference=XYZ&trxref=XYZ)
            return redirect()->route('admin.billing.index')->with('error', 'Payment logic error: Transaction not found.');
        }
        
        try {
            if ($gateway === 'paystack') {
                $success = $paymentService->verifyPaystack($reference);
            } elseif ($gateway === 'flutterwave') {
                $transactionId = $request->input('transaction_id');
                $success = $paymentService->verifyFlutterwave($transactionId);
            }

            if ($success) {
                // Activate Subscription Logic (Same as Manual Approval)
                $payment->update([
                    'status' => 'completed',
                    'approved_by' => null, // Auto-approved
                    'approved_at' => now(),
                ]);

                $tenant = $payment->tenant;
                $plan = $payment->plan;
                
                $currentEndsAt = $tenant->subscription_ends_at;
                $now = now();
                
                if ($currentEndsAt && $currentEndsAt->isFuture() && $tenant->plan_id == $plan->id) {
                    $newEndsAt = $currentEndsAt->addDays($plan->duration_days);
                } else {
                    $newEndsAt = $now->addDays($plan->duration_days);
                }

                $tenant->update([
                    'plan_id' => $plan->id,
                    'subscription_ends_at' => $newEndsAt,
                ]);

                return redirect()->route('admin.billing.index')->with('success', 'Payment successful! Subscription activated.');
            } else {
                $payment->update(['status' => 'failed']);
                return redirect()->route('admin.billing.index')->with('error', 'Payment verification failed.');
            }
        } catch (\Exception $e) {
             return redirect()->route('admin.billing.index')->with('error', 'Payment verification error: ' . $e->getMessage());
        }
    }
}
