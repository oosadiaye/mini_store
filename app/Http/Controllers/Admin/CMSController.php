<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CMSController extends Controller
{
    /**
     * Save the theme settings JSON from the visual editor.
     */
    public function save(Request $request)
    {
        $request->validate([
            'theme_settings' => 'required|array'
        ]);

        $settings = $request->input('theme_settings');
        
        // Update timestamp
        $settings['last_edited_at'] = now()->toIso8601String();

        Storage::disk('tenant')->put('theme_settings.json', json_encode($settings, JSON_PRETTY_PRINT));

        return response()->json(['success' => true]);
    }
}
