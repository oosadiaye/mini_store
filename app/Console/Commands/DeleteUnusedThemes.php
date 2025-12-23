<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteUnusedThemes extends Command
{
    protected $signature = 'themes:delete-unused';
    protected $description = 'Delete unused themes (classic-trade, midnight-luxury, vibrant-pop)';

    public function handle()
    {
        $themesToDelete = ['classic-trade', 'midnight-luxury', 'vibrant-pop'];
        
        $deleted = DB::table('storefront_templates')
            ->whereIn('slug', $themesToDelete)
            ->delete();
            
        $this->info("Deleted {$deleted} themes: " . implode(', ', $themesToDelete));
        
        return 0;
    }
}
