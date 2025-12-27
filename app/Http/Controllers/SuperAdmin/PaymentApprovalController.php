<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentApprovalController extends Controller
{
    public function index()
    {
        $payments = SubscriptionPayment::with(['tenant', 'plan'])
            ->where('status', 'pending')
            ->where('payment_method', 'manual')
            ->latest()
            ->paginate(15);
            
        return view('superadmin.payment-approvals.index', compact('payments'));
    }

    public function approve(SubscriptionPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Payment already processed.');
        }

        DB::transaction(function () use ($payment) {
            // 1. Update Payment Status
            $payment->update([
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // 2. Update Tenant Subscription
            $tenant = $payment->tenant;
            $plan = $payment->plan;
            
            // Calculate new end date
            // If current sub is active and same plan, extend. Else start fresh.
            $currentEndsAt = $tenant->subscription_ends_at;
            $now = now();
            
            if ($currentEndsAt && $currentEndsAt->isFuture() && $tenant->plan_id == $plan->id) {
                $newEndsAt = $currentEndsAt->addDays($plan->duration_days);
            } else {
                $newEndsAt = $now->addDays($plan->duration_days);
            }

            $updateData = [
                'plan_id' => $plan->id,
                'subscription_ends_at' => $newEndsAt,
            ];
            
            // If plan has limits/caps, they are usually static on plan, 
            // but if we stored custom limits on tenant we would update them here.
            // For now, limits are pulled from relation.

            $tenant->update($updateData);

            // 3. Send Notification (TODO: Implement Mail/Notification)
            // Notification::send($tenant->users->first(), new SubscriptionActivated($payment));
        });

        return back()->with('success', 'Payment approved and subscription activated.');
    }

    public function reject(SubscriptionPayment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Payment already processed.');
        }

        $payment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(), // Rejected by
            'approved_at' => now(),
        ]);

        // Notification::send($payment->tenant->owner, new PaymentRejected($payment));

        return back()->with('success', 'Payment rejected.');
    }
}
