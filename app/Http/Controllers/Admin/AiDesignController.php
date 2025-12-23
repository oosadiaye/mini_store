<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AiDesignController extends Controller
{
    public function index()
    {
        return view('admin.ai.index');
    }

    public function generateDesign(Request $request)
    {
        $prompt = strtolower($request->input('prompt', ''));
        
        // Default to Modern Minimal
        $templateSlug = 'modern-minimal';
        $colors = ['primary' => '#000000', 'secondary' => '#ffffff', 'accent' => '#e5e7eb'];
        $fonts = ['heading' => 'Inter', 'body' => 'Inter'];
        $layout = [
            'header_style' => 'sticky',
            'visuals' => ['radius' => 8, 'shadow' => 'sm'],
            'sections' => ['hero' => true, 'categories' => true, 'new_arrivals' => true, 'promo' => true, 'newsletter' => true]
        ];

        // Keyword Analysis
        if (str_contains($prompt, 'nature') || str_contains($prompt, 'green') || str_contains($prompt, 'organic') || str_contains($prompt, 'eco')) {
            $templateSlug = 'organic-fresh';
            $colors = ['primary' => '#059669', 'secondary' => '#ecfdf5', 'accent' => '#fcd34d'];
            $fonts = ['heading' => 'Poppins', 'body' => 'Open Sans'];
            $layout['visuals'] = ['radius' => 24, 'shadow' => 'none'];

        } elseif (str_contains($prompt, 'tech') || str_contains($prompt, 'dark') || str_contains($prompt, 'cyber') || str_contains($prompt, 'gaming')) {
            $templateSlug = 'tech-geeks';
            $colors = ['primary' => '#00ff41', 'secondary' => '#000000', 'accent' => '#ff00ff'];
            $fonts = ['heading' => 'Oswald', 'body' => 'Cousine']; // Cousine fallback to mono
            $layout['visuals'] = ['radius' => 0, 'shadow' => 'xl'];
            $layout['header_style'] = 'transparent';

        } elseif (str_contains($prompt, 'luxury') || str_contains($prompt, 'gold') || str_contains($prompt, 'premium') || str_contains($prompt, 'elegant')) {
            $templateSlug = 'midnight-luxury';
            $colors = ['primary' => '#d4af37', 'secondary' => '#000000', 'accent' => '#111111'];
            $fonts = ['heading' => 'Playfair Display', 'body' => 'Lato'];
            $layout['visuals'] = ['radius' => 0, 'shadow' => 'none'];
            $layout['header_style'] = 'boxed';

        } elseif (str_contains($prompt, 'pop') || str_contains($prompt, 'fun') || str_contains($prompt, 'vibrant') || str_contains($prompt, 'bold')) {
            $templateSlug = 'vibrant-pop';
            $colors = ['primary' => '#f0abfc', 'secondary' => '#facc15', 'accent' => '#000000'];
            $fonts = ['heading' => 'Poppins', 'body' => 'Roboto'];
            $layout['visuals'] = ['radius' => 12, 'shadow' => 'lg'];

        } elseif (str_contains($prompt, 'classic') || str_contains($prompt, 'business') || str_contains($prompt, 'trade') || str_contains($prompt, 'blue')) {
            $templateSlug = 'classic-trade';
            $colors = ['primary' => '#1e3a8a', 'secondary' => '#f3f4f6', 'accent' => '#f59e0b'];
            $fonts = ['heading' => 'Roboto', 'body' => 'Open Sans'];
            $layout['visuals'] = ['radius' => 4, 'shadow' => 'md'];

        } elseif (str_contains($prompt, 'pink') || str_contains($prompt, 'boutique') || str_contains($prompt, 'soft')) {
            $templateSlug = 'elegant-boutique';
            $colors = ['primary' => '#be185d', 'secondary' => '#fdf2f8', 'accent' => '#fbcfe8'];
            $fonts = ['heading' => 'Merriweather', 'body' => 'Lato'];
            $layout['visuals'] = ['radius' => 16, 'shadow' => 'md'];
            $layout['header_style'] = 'transparent';
        }

        // Apply changes
        $template = \App\Models\StorefrontTemplate::where('slug', $templateSlug)->first();
        if (!$template) {
            // Fallback if template seeded data is missing
            $template = \App\Models\StorefrontTemplate::first();
        }

        $settings = \App\Models\ThemeSetting::firstOrNew(['is_active' => true]);
        $settings->template_id = $template->id;
        $settings->colors = $colors;
        $settings->fonts = $fonts;
        $settings->layout_settings = $layout;
        $settings->save();

        return response()->json([
            'success' => true,
            'message' => "Theme successfully generated based on your prompt: '$prompt'. Applied theme: " . $template->name,
            'redirect' => route('storefront.home')
        ]);
    }

    public function generateCopy(Request $request)
    {
        $type = $request->input('type', 'hero');
        
        // Mocking AI copy generation
        $copy = [
            'hero' => [
                "Elevate Your Lifestyle with Our Premium Collection",
                "Discover Quality That Speaks for Itself",
                "Your One-Stop Shop for Everything Extraordinary"
            ],
            'about' => [
                "We are dedicated to providing the best quality products with a focus on dependability, customer service, and uniqueness.",
                "Founded in 2024, our store has come a long way from its beginnings. We hope you enjoy our products as much as we enjoy offering them to you."
            ]
        ];

        $text = $copy[$type][array_rand($copy[$type])] ?? "Welcome to our store!";

        return response()->json([
            'success' => true,
            'text' => $text
        ]);
    }
}
