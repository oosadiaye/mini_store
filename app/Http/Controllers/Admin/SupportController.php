<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Notifications\NewTicketCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('tenant_id', app('tenant')->id)
            ->with('category')
            ->latest()
            ->paginate(10);
            
        $categories = TicketCategory::all();

        return view('tenant.admin.support.index', compact('tickets', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $category = TicketCategory::find($validated['category_id']);

        $ticket = SupportTicket::create([
            'tenant_id' => app('tenant')->id,
            'category_id' => $validated['category_id'],
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
            'assigned_to' => $category->assigned_to, // Auto-assign based on category
        ]);

        // Create initial message
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        // Send notification to assigned staff
        if ($ticket->assignedStaff) {
            Notification::send($ticket->assignedStaff, new NewTicketCreated($ticket));
        }

        return redirect()->route('admin.support.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(SupportTicket $support)
    {
        // Ensure tenant owns the ticket
        if ($support->tenant_id != app('tenant')->id) {
            abort(403);
        }

        $support->load(['messages.user', 'category', 'assignedStaff']);

        return view('tenant.admin.support.show', compact('support'));
    }

    public function reply(Request $request, SupportTicket $support)
    {
        if ($support->tenant_id != app('tenant')->id) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Handle attachment upload if implemented later
        
        TicketMessage::create([
            'ticket_id' => $support->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $support->update(['updated_at' => now()]); // Touch timestamp

        return redirect()->back()->with('success', 'Message sent.');
    }
}
