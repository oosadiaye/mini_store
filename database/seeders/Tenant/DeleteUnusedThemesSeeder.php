<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeleteUnusedThemesSeeder extends Seeder
{
    public function run(): void
    {
        // Delete the 3 unused themes
        $themesToDelete = ['classic-trade', 'midnight-luxury', 'vibrant-pop'];
        
        DB::table('storefront_templates')
            ->whereIn('slug', $themesToDelete)
            ->delete();
            
        echo "Deleted themes: " . implode(', ', $themesToDelete) . "\n";
    }
}
