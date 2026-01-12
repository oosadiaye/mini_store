<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Mail\NewContactInquiry;
use App\Models\ContactMessage;
use App\Models\StoreConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Handle the incoming contact form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $tenant = app('tenant');
        $storeId = $tenant->id;

        // 1. Save to Database
        $contactMessage = ContactMessage::create([
            'store_id' => $storeId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        // 2. Send Email Notification
        $config = StoreConfig::first();
        $supportEmail = $config->store_email ?? $tenant->email; // Fallback to tenant email

        if ($supportEmail) {
            try {
                Mail::to($supportEmail)->send(new NewContactInquiry($contactMessage));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Illuminate\Support\Facades\Log::error('Failed to send contact email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your message has been sent.',
        ]);
    }
}
