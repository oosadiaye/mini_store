<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CustomDomainRequest;
use Illuminate\Http\Request;

class CustomDomainController extends Controller
{
    /**
     * Display a listing of custom domain requests.
     */
    public function index()
    {
        $requests = CustomDomainRequest::with('tenant')
            ->latest()
            ->paginate(20);
            
        return view('superadmin.custom-domains.index', compact('requests'));
    }

    /**
     * Approve a custom domain request.
     */
    public function approve(Request $request, $id)
    {
        $domainRequest = CustomDomainRequest::findOrFail($id);
        
        $domainRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Logic to verify DNS and provision SSL would go here
        
        return back()->with('success', 'Domain approved successfully.');
    }

    /**
     * Reject a custom domain request.
     */
    public function reject(Request $request, $id)
    {
        $domainRequest = CustomDomainRequest::findOrFail($id);
        
        $domainRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason', 'Domain configuration invalid'),
        ]);

        return back()->with('success', 'Domain rejected.');
    }
}
