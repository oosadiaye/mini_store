<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// ... existing routes ...

Route::get('/debug-db', function () {
    try {
        $dbName = DB::connection()->getDatabaseName();
        $columns = DB::select('SHOW COLUMNS FROM brands');
        return response()->json([
            'database' => $dbName,
            'columns' => $columns,
            'connection' => config('database.default'),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
