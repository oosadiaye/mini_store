<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenant = app('tenant');
        $messages = ContactMessage::where('store_id', $tenant->id)
            ->latest()
            ->paginate(15);
            
        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Mark the specified message as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $tenant = app('tenant');
        $message = ContactMessage::where('store_id', $tenant->id)->where('id', $id)->firstOrFail();
        
        $message->update(['is_read' => true]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Marked as read',
            'data' => $message 
        ]);
    }
}
