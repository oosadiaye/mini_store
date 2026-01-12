<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantSettingsController extends Controller
{
    public function toggleStorefront(Request $request)
    {
        $tenant = app('tenant');

        // Critical Validation: Check if the tenant's plan includes the online_store feature
        if (!$tenant->hasFeature('online_store')) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Your current plan does not include the Online Store feature.'], 403);
            }
            abort(403, 'Your current plan does not include the Online Store feature.');
        }

        $tenant->update([
            'is_storefront_enabled' => !$tenant->is_storefront_enabled
        ]);

        $status = $tenant->is_storefront_enabled ? 'enabled' : 'disabled';
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'enabled' => (bool)$tenant->is_storefront_enabled,
                'message' => "Public storefront has been {$status}."
            ]);
        }

        return back()->with('success', "Public storefront has been {$status}.");
    }
}
