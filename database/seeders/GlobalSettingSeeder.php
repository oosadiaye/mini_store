<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // PWA Settings
            ['group' => 'pwa', 'key' => 'pwa_admin_name', 'value' => 'MiniStore Admin'],
            ['group' => 'pwa', 'key' => 'pwa_admin_short_name', 'value' => 'Admin'],
            ['group' => 'pwa', 'key' => 'pwa_admin_theme_color', 'value' => '#4f46e5'],
            ['group' => 'pwa', 'key' => 'pwa_admin_bg_color', 'value' => '#ffffff'],
            ['group' => 'pwa', 'key' => 'pwa_admin_icon', 'value' => null],
        ];

        foreach ($settings as $setting) {
            DB::table('global_settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
