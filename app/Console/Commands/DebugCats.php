<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class DebugCats extends Command
{
    protected $signature = 'debug:cats';
    protected $description = 'Debug product categories';

    public function handle()
    {
        $products = Product::with('category')->get();
        
        $this->info("Found " . $products->count() . " products.");

        foreach ($products as $p) {
            $catId = $p->category_id ?? 'NULL';
            $catName = $p->category ? $p->category->name : 'RELATION_NULL';
            $this->line("Product: {$p->name} [ID: {$p->id}] | CategoryID: {$catId} | Category Relation: {$catName}");
        }
    }
}
