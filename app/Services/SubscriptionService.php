<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Calculate proration for plan upgrade.
     *
     * @param Tenant $tenant
     * @param Plan $newPlan
     * @return array ['credit' => float, 'amount_due' => float, 'unused_days' => int]
     */
    public function calculateProration(Tenant $tenant, Plan $newPlan): array
    {
        // If no current plan, no proration
        if (!$tenant->plan_id || !$tenant->subscription_ends_at) {
            return [
                'credit' => 0,
                'amount_due' => $newPlan->price,
                'unused_days' => 0,
            ];
        }

        $currentPlan = Plan::find($tenant->plan_id);
        
        if (!$currentPlan) {
            return [
                'credit' => 0,
                'amount_due' => $newPlan->price,
                'unused_days' => 0,
            ];
        }

        // Calculate unused days
        $subscriptionEndsAt = Carbon::parse($tenant->subscription_ends_at);
        $unusedDays = max(0, now()->diffInDays($subscriptionEndsAt, false));

        // If subscription expired, no credit
        if ($unusedDays <= 0) {
            return [
                'credit' => 0,
                'amount_due' => $newPlan->price,
                'unused_days' => 0,
            ];
        }

        // Calculate daily rate and credit
        $dailyRate = $currentPlan->price / max(1, $currentPlan->duration_days);
        $credit = round($dailyRate * $unusedDays, 2);
        $amountDue = max(0, $newPlan->price - $credit);

        return [
            'credit' => $credit,
            'amount_due' => $amountDue,
            'unused_days' => $unusedDays,
        ];
    }

    /**
     * Check if tenant has a pending transaction.
     *
     * @param Tenant $tenant
     * @return bool
     */
    public function hasPendingTransaction(Tenant $tenant): bool
    {
        return DB::table('subscription_transactions')
            ->where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Check if tenant can upgrade to the new plan.
     * Prevents downgrades where credit exceeds new plan price.
     *
     * @param Tenant $tenant
     * @param Plan $newPlan
     * @return array ['can_upgrade' => bool, 'reason' => string|null]
     */
    public function canUpgrade(Tenant $tenant, Plan $newPlan): array
    {
        // Same plan check
        if ($tenant->plan_id == $newPlan->id) {
            return [
                'can_upgrade' => false,
                'reason' => 'You are already on this plan.',
            ];
        }

        // Pending transaction check
        if ($this->hasPendingTransaction($tenant)) {
            return [
                'can_upgrade' => false,
                'reason' => 'You have a pending payment. Please wait for approval or contact support.',
            ];
        }

        // Calculate proration
        $proration = $this->calculateProration($tenant, $newPlan);

        // Reject if credit exceeds new plan price (downgrade with excess credit)
        if ($proration['credit'] > $newPlan->price) {
            return [
                'can_upgrade' => false,
                'reason' => 'Your current plan credit exceeds the new plan price. Please wait until your current subscription expires or contact support.',
            ];
        }

        return [
            'can_upgrade' => true,
            'reason' => null,
        ];
    }

    /**
     * Get proration details for display.
     *
     * @param Tenant $tenant
     * @param Plan $newPlan
     * @return array|null
     */
    public function getProrationDetails(Tenant $tenant, Plan $newPlan): ?array
    {
        if (!$tenant->plan_id || $tenant->plan_id == $newPlan->id) {
            return null;
        }

        $proration = $this->calculateProration($tenant, $newPlan);

        if ($proration['credit'] <= 0) {
            return null;
        }

        return $proration;
    }
}
