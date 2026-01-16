<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SecureFileUploader;

class GlobalPwaController extends Controller
{
    protected $uploader;

    public function __construct(SecureFileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function edit()
    {
        // Fetch current settings
        $settings = DB::table('global_settings')
            ->where('group', 'pwa')
            ->pluck('value', 'key');

        return view('superadmin.pwa.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'pwa_admin_name' => 'required|string|max:50',
            'pwa_admin_short_name' => 'required|string|max:15',
            'pwa_admin_theme_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'pwa_admin_bg_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'pwa_admin_icon' => 'nullable|image|max:2048|mimes:png', // PNG preferred for icons
        ]);

        $data = $request->except(['_token', 'pwa_admin_icon']);

        // Handle Icon Upload
        if ($request->hasFile('pwa_admin_icon')) {
            $path = $this->uploader->upload($request->file('pwa_admin_icon'), 'pwa', 'public');
            $data['pwa_admin_icon'] = $path;
        }

        // Save settings
        foreach ($data as $key => $value) {
            DB::table('global_settings')->updateOrInsert(
                ['group' => 'pwa', 'key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Central PWA Settings updated successfully.');
    }
}
