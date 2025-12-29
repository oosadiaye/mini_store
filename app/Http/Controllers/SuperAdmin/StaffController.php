<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        // Get users who are superadmins (and thus not associated with a specific tenant usually, strict check)
        // Adjust the query based on your exact SuperAdmin user logic (is_superadmin flag or tenant_id=null)
        $staff = User::where('is_superadmin', true)->paginate(10);
        return view('superadmin.staff.index', compact('staff'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'web')->get();
        return view('superadmin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        // Use withoutGlobalScope to creating user without tenant context if needed
        // But since we are likely in a context where 'tenant' might not be bound, direct create should work if logic permits.
        // Explicitly force tenant_id to null for superadmin staff to avoid accidental association.
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->is_superadmin = true;
        // $user->tenant_id = null; // Unset tenant scope if model allows
        $user->save();

        $user->assignRole($request->role);

        // Send Invitation Email
        try {
            \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\SuperAdminStaffInvitation($user, $request->password, $request->role));
        } catch (\Exception $e) {
            // Log error but continue
            \Illuminate\Support\Facades\Log::error('Failed to send staff invitation email: ' . $e->getMessage());
        }

        return redirect()->route('superadmin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit(User $staff)
    {
        $roles = Role::where('guard_name', 'web')->get();
        return view('superadmin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $staff->id,
            'role' => 'required|exists:roles,name',
        ]);

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'required|string|min:8|confirmed']);
            $staff->update(['password' => Hash::make($request->password)]);
        }

        $staff->syncRoles([$request->role]);

        return redirect()->route('superadmin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $staff->delete();

        return redirect()->route('superadmin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}
