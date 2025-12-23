<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EnquiryReplied;
use App\Models\ProductEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ProductEnquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductEnquiry::with(['product', 'repliedBy'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $enquiries = $query->paginate(20);
        $pendingCount = ProductEnquiry::pending()->count();

        return view('admin.enquiries.index', compact('enquiries', 'pendingCount'));
    }

    public function show(ProductEnquiry $enquiry)
    {
        $enquiry->load(['product', 'repliedBy']);
        return view('admin.enquiries.show', compact('enquiry'));
    }

    public function reply(Request $request, ProductEnquiry $enquiry)
    {
        $request->validate([
            'admin_reply' => 'required|string|min:10',
        ]);

        $enquiry->update([
            'admin_reply' => $request->admin_reply,
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => auth()->id(),
        ]);

        // Send email to customer
        try {
            Mail::to($enquiry->customer_email)->send(new EnquiryReplied($enquiry));
        } catch (\Exception $e) {
            \Log::error('Failed to send enquiry reply email: ' . $e->getMessage());
        }

        return back()->with('success', 'Reply sent successfully!');
    }

    public function updateStatus(Request $request, ProductEnquiry $enquiry)
    {
        $request->validate([
            'status' => 'required|in:pending,replied,closed',
        ]);

        $enquiry->update(['status' => $request->status]);

        return back()->with('success', 'Status updated successfully!');
    }
}
