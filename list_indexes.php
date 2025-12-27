<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$indexes = DB::select('SHOW INDEX FROM categories');
foreach ($indexes as $index) {
    if ($index->Key_name != 'PRIMARY') {
        echo $index->Key_name . "\n";
    }
}
