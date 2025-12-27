<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['domains', 'currentPlan'])->latest()->get();
        $plans = Plan::where('is_active', true)->get();
        return view('superadmin.tenants.index', compact('tenants', 'plans'));
    }

    public function show(Tenant $tenant)
    {
        // Load users manually or via relationship to avoid global scope issues
        $users = \App\Models\User::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('tenant_id', $tenant->id)
            ->with('roles') // Assuming roles relationship exists on User, or standard Spatie roles
            ->get();
            
        return view('superadmin.tenants.show', compact('tenant', 'users'));
    }

    public function impersonate(Tenant $tenant)
    {
        // Find the admin user for this tenant
        // Since we are now single-DB, we can search the User model directly with tenant_id
        $user = \App\Models\User::withoutGlobalScope(\App\Scopes\TenantScope::class)
            ->where('tenant_id', $tenant->id)
            ->where('role', 'admin') // Assuming role column exists
            ->first();

        if (!$user) {
            // Fallback to first user
            $user = \App\Models\User::withoutGlobalScope(\App\Scopes\TenantScope::class)
                ->where('tenant_id', $tenant->id)
                ->first();
        }

        if (!$user) {
            return redirect()->back()->with('error', 'No users found in this tenant.');
        }

        // Login as the user
        // Store the current superadmin id in session before switching
        $superAdminId = \Illuminate\Support\Facades\Auth::id();
        session()->put('superadmin_impersonator_id', $superAdminId);

        \Illuminate\Support\Facades\Auth::login($user);

        // Redirect to tenant dashboard
        // URL: /slug/admin
        $tenantUrl = url('/' . $tenant->slug . '/admin');

        \App\Helpers\AuditHelper::log('impersonate_tenant', "Impersonated tenant: {$tenant->id}", ['tenant_id' => $tenant->id]);

        return redirect($tenantUrl)->with('success', "Impersonating {$user->name}");
    }

    public function impersonateUser($userId)
    {
        $user = \App\Models\User::withoutGlobalScope(\App\Scopes\TenantScope::class)->findOrFail($userId);
        
        // Login as the user
        $superAdminId = \Illuminate\Support\Facades\Auth::id();
        session()->put('superadmin_impersonator_id', $superAdminId);

        \Illuminate\Support\Facades\Auth::login($user);

        // Redirect to tenant dashboard
        $tenant = \App\Models\Tenant::find($user->tenant_id);
        $tenantUrl = url('/' . $tenant->slug . '/admin');

        \App\Helpers\AuditHelper::log('impersonate_user', "Impersonated user: {$user->id}", ['tenant_id' => $tenant->id]);

        return redirect($tenantUrl)->with('success', "Impersonating {$user->name}");
    }

    public function stopImpersonation()
    {
        $superAdminId = session('superadmin_impersonator_id');

        if (!$superAdminId) {
            abort(403, 'Unauthorized action.');
        }

        // Login back as SuperAdmin
        \Illuminate\Support\Facades\Auth::loginUsingId($superAdminId);
        session()->forget('superadmin_impersonator_id');

        return redirect()->route('superadmin.dashboard')->with('success', 'Welcome back, SuperAdmin!');
    }

    public function destroy(Tenant $tenant)
    {
        // Delete tenant database and domain would be ideal in real scenario
        $tenant->domains()->delete();
        $tenant->delete();

        \App\Helpers\AuditHelper::log('delete_tenant', "Deleted tenant: {$tenant->id}", ['tenant_id' => $tenant->id]);
        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }

    public function suspend(Tenant $tenant)
    {
        $tenant->update(['is_suspended' => true]);
        \App\Helpers\AuditHelper::log('suspend_tenant', "Suspended tenant: {$tenant->id}", ['tenant_id' => $tenant->id]);
        return back()->with('success', 'Tenant suspended successfully.');
    }

    public function unsuspend(Tenant $tenant)
    {
        $tenant->update(['is_suspended' => false]);
        \App\Helpers\AuditHelper::log('unsuspend_tenant', "Unsuspended tenant: {$tenant->id}", ['tenant_id' => $tenant->id]);
        return back()->with('success', 'Tenant reactivated successfully.');
    }
    
    public function updatePlan(Request $request, Tenant $tenant)
    {
        $validate = $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);
        
        $plan = Plan::find($validate['plan_id']);
        
        $tenant->update([
            'plan_id' => $plan->id,
            // Also update the 'plan' string column for backward compatibility if needed, or deprecate it
            'plan' => $plan->name 
        ]);
        
        // You might want to update subscription dates here or trigger events
        
        \App\Helpers\AuditHelper::log('update_tenant_plan', "Updated tenant plan to {$plan->name}: {$tenant->id}", ['tenant_id' => $tenant->id]);
        
        return back()->with('success', 'Tenant plan updated successfully.');
    }
}
