<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditHelper
{
    public static function log($action, $description = null, $details = [])
    {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => Request::ip(),
                'details' => $details,
            ]);
        } catch (\Exception $e) {
            // Fail silently to not disrupt user flow
            // Log::error('Audit Log failed: ' . $e->getMessage());
        }
    }
}
