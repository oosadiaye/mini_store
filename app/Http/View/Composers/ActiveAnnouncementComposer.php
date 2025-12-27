<?php

namespace App\Http\View\Composers;

use App\Models\Announcement;
use Illuminate\View\View;

class ActiveAnnouncementComposer
{
    public function compose(View $view)
    {
        if (!app()->bound('tenant')) {
            return;
        }

        $tenantId = app('tenant')->id;
        $userId = auth()->id();

        if (!$userId) {
            return;
        }

        // Get all active announcements relevant to this tenant
        $announcements = Announcement::active()
            ->where(function ($query) use ($tenantId) {
                $query->where('target_type', 'all')
                      ->orWhereHas('tenants', function ($q) use ($tenantId) {
                          $q->where('tenants.id', $tenantId);
                      });
            })
            ->get();

        // Separate Onboarding (Pending only)
        $onboarding = $announcements->filter(function ($a) use ($tenantId, $userId) {
             return $a->type === 'onboarding' && 
                    !$a->reads()->where('tenant_id', $tenantId)->where('user_id', $userId)->exists();
        })->first(); // Get the first one to show

        // Unread Count for Notification Bell
        $unreadCount = $announcements->filter(function ($a) use ($tenantId, $userId) {
             return !$a->reads()->where('tenant_id', $tenantId)->where('user_id', $userId)->exists();
        })->count();

        $view->with('sharedOnboarding', $onboarding)
             ->with('sharedAnnouncements', $announcements)
             ->with('sharedUnreadAnnouncementsCount', $unreadCount);
    }
}
