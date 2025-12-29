<?php

use Illuminate\Support\Facades\Route;

// Installation Routes (Must be defined BEFORE tenant wildcard routes)
Route::prefix('install')->name('install.')->group(function () {
    Route::get('/', [\App\Http\Controllers\InstallerController::class, 'welcome'])->name('welcome');
    Route::get('/requirements', [\App\Http\Controllers\InstallerController::class, 'requirements'])->name('requirements');
    Route::get('/environment', [\App\Http\Controllers\InstallerController::class, 'environment'])->name('environment');
    Route::post('/environment', [\App\Http\Controllers\InstallerController::class, 'saveEnvironment'])->name('environment.save');
    Route::get('/database', [\App\Http\Controllers\InstallerController::class, 'database'])->name('database');
    Route::post('/database', [\App\Http\Controllers\InstallerController::class, 'runMigration'])->name('database.migrate');
    Route::get('/admin', [\App\Http\Controllers\InstallerController::class, 'admin'])->name('admin');
    Route::post('/admin', [\App\Http\Controllers\InstallerController::class, 'storeAdmin'])->name('admin.store');
    Route::get('/finish', [\App\Http\Controllers\InstallerController::class, 'finish'])->name('finish');
});

// Load Central Routes (landing, registration, superadmin)
require __DIR__ . '/central.php';

// Load Auth Routes (login, logout, password reset) -> Must be loaded BEFORE tenant wildcard
require __DIR__ . '/auth.php';

// Load Tenant Routes (storefront, admin) -> Has wildcard {tenant}, so load LAST
require __DIR__ . '/tenant.php';
