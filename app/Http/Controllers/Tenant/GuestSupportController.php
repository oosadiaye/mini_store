<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class GuestSupportController extends Controller
{
    public function create()
    {
        $categories = TicketCategory::all();
        $tenant = app('tenant');
        return view('tenant.support.guest_create', compact('categories', 'tenant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'category_id' => 'required|exists:ticket_categories,id',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $tenant = app('tenant');

        $ticket = SupportTicket::create([
            'tenant_id' => $tenant->id,
            'category_id' => $request->category_id,
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
            'contact_name' => $request->name,
            'contact_email' => $request->email,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => null, // Guest
            'message' => $request->message,
            'is_internal' => false,
        ]);

        return redirect()->route('tenant.support.guest.success', ['tenant' => $tenant->slug])
            ->with('success', 'Your support ticket has been submitted. We will contact you at ' . $request->email);
    }
    
    public function success()
    {
        $tenant = app('tenant');
        return view('tenant.support.guest_success', compact('tenant'));
    }
}
