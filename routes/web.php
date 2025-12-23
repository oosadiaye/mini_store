<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        // Landing page
        Route::get('/', function () {
            return view('welcome');
        });

        // Tenant Registration (Central Domain)
        Route::get('/register-store', [TenantRegistrationController::class, 'create'])->name('tenant.register');
        Route::post('/register-store', [TenantRegistrationController::class, 'store'])->name('tenant.store');
        Route::get('/store-created', [TenantRegistrationController::class, 'success'])->name('tenant.success');

        // Central admin dashboard (redirects to superadmin)
        Route::get('/dashboard', function () {
            return redirect()->route('superadmin.dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        // SuperAdmin Routes
        Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'verified', \App\Http\Middleware\CheckSuperAdmin::class])->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
            
            // Tenants
            Route::get('/tenants', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'index'])->name('tenants.index');
            Route::get('/tenants/{tenant}/impersonate', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'impersonate'])->name('tenants.impersonate');
            Route::delete('/tenants/{tenant}', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'destroy'])->name('tenants.destroy');
            // Plans
            Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class);
            
            // Settings
            Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');

            // Users
            Route::resource('users', \App\Http\Controllers\SuperAdmin\UserController::class);
            
            // Audit Logs
            Route::get('/audit-logs', [\App\Http\Controllers\SuperAdmin\AuditLogController::class, 'index'])->name('audit_logs.index');
        });

        require __DIR__.'/auth.php';

        // Admin Theme Settings (single page UI)
        Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('theme-settings', [\App\Http\Controllers\Admin\ThemeSettingController::class, 'index'])->name('theme-settings.index');
            Route::post('theme-settings', [\App\Http\Controllers\Admin\ThemeSettingController::class, 'update'])->name('theme-settings.update');
        });
    });
}


