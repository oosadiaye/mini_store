<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Stancl\Tenancy\Features\UserImpersonation;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')->latest()->get();
        return view('superadmin.tenants.index', compact('tenants'));
    }

    public function show(Tenant $tenant)
    {
        return view('superadmin.tenants.show', compact('tenant'));
    }

    public function impersonate(Tenant $tenant)
    {
        // 1. Initialize tenancy to search for users in that tenant's DB
        tenancy()->initialize($tenant);

        // 2. Find the admin user (assuming first user or role based)
        // In a real app, you might choose WHICH user to impersonate or look for 'admin' role
        $user = \App\Models\User::first();

        if (!$user) {
            return redirect()->back()->with('error', 'No users found in this tenant.');
        }

        // 3. Generate impersonation token and redirect
        // The UserImpersonation feature handles the token generation and redirect URL construction
        $token = tenancy()->impersonate($tenant, $user->id, '/admin/dashboard');

        \App\Helpers\AuditHelper::log('impersonate_tenant', "Impersonated tenant: {$tenant->id}", ['tenant_id' => $tenant->id]);

        // Construct the impersonation URL manually since we need to cross domains
        $domain = $tenant->domains->first()?->domain ?? $tenant->id . '.' . config('app.domain');
        $protocol = request()->secure() ? 'https://' : 'http://';
        $port = request()->getPort();
        $portSuffix = (($protocol === 'http://' && $port !== 80) || ($protocol === 'https://' && $port !== 443)) ? ":{$port}" : '';
        
        $impersonateUrl = "{$protocol}{$domain}{$portSuffix}/impersonate/{$token->token}";

        return redirect($impersonateUrl);
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        \App\Helpers\AuditHelper::log('delete_tenant', "Deleted tenant: {$tenant->id}", ['tenant_id' => $tenant->id]);
        return redirect()->route('superadmin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
