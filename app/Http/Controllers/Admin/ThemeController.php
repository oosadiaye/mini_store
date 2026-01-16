<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreConfig;

class ThemeController extends Controller
{
    public function editor()
    {
        $tenant = app('tenant');
        $config = StoreConfig::where('tenant_id', $tenant->id)->firstOrFail();
        
        // Initialize default layout if empty
        $layout = $config->theme_layout ?? [
            [
                'id' => 'hero-1',
                'type' => 'hero_banner',
                'props' => [
                    'title' => 'Welcome to ' . $tenant->name,
                    'subtitle' => 'Discover our amazing products',
                    'bg_color' => '#1a1a1a',
                    'text_color' => '#ffffff'
                ]
            ],
            [
                'id' => 'products-1',
                'type' => 'product_grid',
                'props' => [
                    'title' => 'Featured Products',
                    'limit' => 4
                ]
            ]
        ];

        return view('admin.theme.editor', compact('layout'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme_layout' => 'required|array',
        ]);

        $tenant = app('tenant');
        $config = StoreConfig::where('tenant_id', $tenant->id)->firstOrFail();

        $config->update([
            'theme_layout' => $validated['theme_layout']
        ]);

        return response()->json(['message' => 'Theme layout saved successfully!']);
    }
}
