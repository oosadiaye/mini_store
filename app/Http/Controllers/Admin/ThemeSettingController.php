<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeSettingController extends Controller
{
    public function index()
    {
        $tenantId = tenant('id');
        $themeSlug = ThemeSetting::getActiveThemeSlug();
        $setting = ThemeSetting::firstOrCreate([
            'tenant_id' => $tenantId,
            'theme_slug' => $themeSlug,
        ], [
            'settings' => []
        ]);

        return view('admin.theme-settings', [
            'settings' => $setting->settings,
            'themeSlug' => $themeSlug,
        ]);
    }

    public function update(Request $request)
    {
        $tenantId = tenant('id');
        $themeSlug = ThemeSetting::getActiveThemeSlug();
        // Accept all inputs except token as settings
        // This allows flexible schema updates without changing controller code
        $data = $request->except(['_token']);

        // Filter empty array items if needed or handle in frontend
        // For now, save raw structure

        $setting = ThemeSetting::updateOrCreate([
            'tenant_id' => $tenantId,
            'theme_slug' => $themeSlug,
        ], [
            'settings' => $data,
        ]);

        return redirect()->back()->with('status', 'Theme settings saved.');
    }
}
