<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ListThemes extends Command
{
    protected $signature = 'themes:list';
    protected $description = 'List all available themes';

    public function handle()
    {
        $themes = DB::table('storefront_templates')->get(['slug', 'name']);
        
        $this->info("Available Themes:");
        $this->info("================");
        
        foreach ($themes as $theme) {
            $this->line("- {$theme->slug}: {$theme->name}");
        }
        
        $this->info("\nTotal: " . $themes->count() . " themes");
        
        return 0;
    }
}
