<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('manager')->latest()->paginate(20);
        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
        return view('admin.warehouses.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Warehouse::create($validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse created successfully!');
    }

    public function edit(Warehouse $warehouse)
    {
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
        return view('admin.warehouses.edit', compact('warehouse', 'managers'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $warehouse->update($validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse updated successfully!');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse deleted successfully!');
    }
}
