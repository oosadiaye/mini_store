<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\PageLayout;

class SeedHeroSection extends Command
{
    protected $signature = 'seed:hero';
    protected $description = 'Seed the Hero section with default content';

    public function handle()
    {
        $tenant = Tenant::first();
        
        if (!$tenant) {
            $this->error('No tenant found!');
            return 1;
        }

        \Stancl\Tenancy\Facades\Tenancy::initialize($tenant);
        
        $this->info("Initializing tenant: {$tenant->name}");

        $heroData = [
            'id' => 'hero-1',
            'type' => 'hero',
            'enabled' => true,
            'order' => 1,
            'title' => 'Refined Retail Reimagined.',
            'content' => 'Discover a curated collection of premium essentials designed to elevate your everyday lifestyle.',
            'settings' => [
                'button_text' => 'Start Shopping',
                'button_link' => '/products',
                'background_color' => '#1a1a2e',
                'overlay_color' => '#000000',
                'overlay_opacity' => 40,
                'min_height_desktop' => 600,
                'min_height_mobile' => 400,
                'title_color' => '#ffffff',
                'title_font_size_desktop' => 64,
                'title_font_size_mobile' => 36,
            ]
        ];

        $layout = PageLayout::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'page_name' => 'home'
            ],
            [
                'tenant_id' => $tenant->id,
                'page_name' => 'home',
                'is_active' => true,
                'sections' => [$heroData]
            ]
        );

        $this->info('✅ Hero section created successfully!');
        $this->info('Section count: ' . count($layout->sections));
        
        return 0;
    }
}
