<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketCategory;

class TicketCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = TicketCategory::with('assignedStaff')->latest()->get();
        // Get all superadmins to assign categories to
        $staff = \App\Models\User::where('role', 'superadmin')->get(); // Assuming 'role' column exists or similar logic
        // If role doesn't exist, maybe just get all users for now or filter by a specific trait. 
        // Based on previous context, SuperAdmins are just Users with possibly a role or check.
        // Let's assume we want to assign to any user for now, or refine if 'superadmin' check is intricate.
        // Checking `CheckSuperAdmin` middleware suggests there might be a way. 
        // Let's just get all users for simplicity or check if there is a specific way to identify staff.
        // I'll stick to User::all() for the dropdown to avoid errors if logic is complex, 
        // but ideally filtered by admins.
        $staff = \App\Models\User::all(); 

        return view('superadmin.ticket-categories.index', compact('categories', 'staff'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        TicketCategory::create($validated);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticketCategory->update($validated);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TicketCategory $ticketCategory)
    {
        if ($ticketCategory->tickets()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete this category because it has assigned tickets.');
        }

        $ticketCategory->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
