<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('name')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($data) {
            // Group permissions by module logic (e.g. "create products" -> "Products")
            $parts = explode(' ', $data->name);
            $module = count($parts) > 1 ? ucfirst(end($parts)) : 'General';
            // Custom grouping logic if needed
            if (str_contains($data->name, 'pos')) return 'POS';
            if (str_contains($data->name, 'order')) return 'Orders';
            if (str_contains($data->name, 'product') || str_contains($data->name, 'inventory')) return 'Inventory';
            if (str_contains($data->name, 'customer')) return 'Customers';
            if (str_contains($data->name, 'purchase') || str_contains($data->name, 'supplier')) return 'Purchase';
            if (str_contains($data->name, 'account') || str_contains($data->name, 'financial')) return 'Accounting';
            if (str_contains($data->name, 'user') || str_contains($data->name, 'role')) return 'User Management';
            
            return $module;
        });

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where(fn ($query) => $query->where('guard_name', 'web'))],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        if ($role->name === 'Store Admin') {
           // Prevent editing the super admin role of the store
           // return redirect()->back()->with('error', 'Cannot edit Store Admin role.');
           // Use flash message or just allow viewing
        }

        $permissions = Permission::orderBy('name')->get()->groupBy(function($data) {
             // Group permissions by module logic
             $parts = explode(' ', $data->name);
             $module = count($parts) > 1 ? ucfirst(end($parts)) : 'General';
             
             if (str_contains($data->name, 'pos')) return 'POS';
             if (str_contains($data->name, 'order')) return 'Orders';
             if (str_contains($data->name, 'product') || str_contains($data->name, 'inventory')) return 'Inventory';
             if (str_contains($data->name, 'customer')) return 'Customers';
             if (str_contains($data->name, 'purchase') || str_contains($data->name, 'supplier')) return 'Purchase';
             if (str_contains($data->name, 'account') || str_contains($data->name, 'financial')) return 'Accounting';
             if (str_contains($data->name, 'user') || str_contains($data->name, 'role')) return 'User Management';

             return $module;
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Store Admin') {
             // Store Admin should always have all permissions, or uneditable
             return redirect()->route('admin.roles.index')->with('error', 'Cannot modify Store Admin role.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)->where(fn ($query) => $query->where('guard_name', 'web'))],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
             // If no permissions sent (checkboxes unchecked), remove all
            $role->syncPermissions([]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Store Admin', 'Sales', 'Accountant', 'Inventory', 'Purchase Manager', 'Cashier'])) {
            return back()->with('error', 'Cannot delete default roles.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
