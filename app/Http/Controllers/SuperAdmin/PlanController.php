<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = Plan::latest()->get();
        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'array',
            'features.*' => 'string',
            'caps' => 'array',
            'caps.max_products' => 'nullable|integer|min:0',
            'caps.max_transactions' => 'nullable|integer|min:0',
            'caps.max_customers' => 'nullable|integer|min:0',
            'caps.max_suppliers' => 'nullable|integer|min:0',
            'caps.max_sales' => 'nullable|integer|min:0',
            'caps.max_purchases' => 'nullable|integer|min:0',
            'caps.max_warehouses' => 'nullable|integer|min:0',
            'caps.max_users' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
 
        Plan::create($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'array',
            'features.*' => 'string',
            'caps' => 'array',
            'caps.max_products' => 'nullable|integer|min:0',
            'caps.max_transactions' => 'nullable|integer|min:0',
            'caps.max_customers' => 'nullable|integer|min:0',
            'caps.max_suppliers' => 'nullable|integer|min:0',
            'caps.max_sales' => 'nullable|integer|min:0',
            'caps.max_purchases' => 'nullable|integer|min:0',
            'caps.max_warehouses' => 'nullable|integer|min:0',
            'caps.max_users' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $plan->update($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        // Check if plan has tenants
        if (\App\Models\Tenant::where('plan_id', $plan->id)->exists()) {
            return back()->with('error', 'Cannot delete plan as it has assigned tenants. Please reassign them first.');
        }

        $plan->delete();

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }
}
