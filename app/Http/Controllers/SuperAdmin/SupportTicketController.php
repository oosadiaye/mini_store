<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\TicketMessage;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with(['tenant', 'category', 'assignedStaff'])
            ->latest()
            ->paginate(15);

        return view('superadmin.tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $supportTicket)
    {
        $supportTicket->load(['messages.user', 'category', 'tenant']);
        return view('superadmin.tickets.show', compact('supportTicket'));
    }

    public function update(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high',
        ]);

        $supportTicket->update($validated);

        return redirect()->back()->with('success', 'Ticket updated successfully.');
    }

    public function reply(Request $request, SupportTicket $supportTicket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Create message
        TicketMessage::create([
            'ticket_id' => $supportTicket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Auto-update status to In Progress if it was Open
        if ($supportTicket->status === 'open') {
            $supportTicket->update(['status' => 'in_progress']);
        }
        
        $supportTicket->touch();

        // Notification to tenant could go here (e.g. email)

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }
}
