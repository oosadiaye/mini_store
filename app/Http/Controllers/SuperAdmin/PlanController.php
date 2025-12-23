<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order')->orderBy('price')->get();
        return view('superadmin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('superadmin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'duration_months' => 'required|integer|min:1',
            'features' => 'nullable|string', // We'll accept newline-separated string and convert to array
        ]);

        $features = $request->features 
            ? array_filter(array_map('trim', explode("\n", $request->features)))
            : [];

        $plan = Plan::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'price' => $request->price,
            'currency' => strtoupper($request->currency),
            'duration_months' => $request->duration_months,
            'features' => $features,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        \App\Helpers\AuditHelper::log('create_plan', "Created plan: {$plan->name}", ['plan_id' => $plan->id]);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('superadmin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
         $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'duration_months' => 'required|integer|min:1',
            'features' => 'nullable|string',
        ]);

        $features = $request->features 
            ? array_filter(array_map('trim', explode("\n", $request->features)))
            : [];

        $plan->update([
            'name' => $request->name,
            // Don't auto-update slug to preserve URLs, or optional
            'price' => $request->price,
            'currency' => strtoupper($request->currency),
            'duration_months' => $request->duration_months,
            'features' => $features,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        \App\Helpers\AuditHelper::log('update_plan', "Updated plan: {$plan->name}", ['plan_id' => $plan->id]);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        \App\Helpers\AuditHelper::log('delete_plan', "Deleted plan: {$plan->name}", ['plan_id' => $plan->id]);
        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
