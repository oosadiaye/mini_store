<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $tenantId = app('tenant')->id;
        $userId = auth()->id();

        // Fetch all active announcements applicable to this tenant
        $announcements = Announcement::active()
            ->where(function ($query) use ($tenantId) {
                $query->where('target_type', 'all')
                      ->orWhereHas('tenants', function ($q) use ($tenantId) {
                          $q->where('tenants.id', $tenantId);
                      });
            })
            ->latest()
            ->get()
            ->map(function ($announcement) use ($tenantId, $userId) {
                $announcement->is_read = $announcement->reads()
                    ->where('tenant_id', $tenantId)
                    ->where('user_id', $userId)
                    ->exists();
                return $announcement;
            });

        return view('admin.announcements.index', compact('announcements'));
    }

    public function markAsRead(Request $request, $id)
    {
        $announcement = Announcement::active()->findOrFail($id);
        
        AnnouncementRead::firstOrCreate([
            'announcement_id' => $id,
            'user_id' => auth()->id(),
            'tenant_id' => app('tenant')->id,
        ]);

        return response()->json(['success' => true]);
    }
}
