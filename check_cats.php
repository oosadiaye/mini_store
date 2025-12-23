<?php
$products = App\Models\Product::take(5)->get();
foreach ($products as $p) {
    echo "Product: {$p->name} (ID: {$p->id}) | CategoryID: " . ($p->category_id ?? 'NULL') . " | Category: " . ($p->category ? $p->category->name : 'MISSING') . PHP_EOL;
}
