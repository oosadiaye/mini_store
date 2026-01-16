<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\SuperAdmin;


/*
|--------------------------------------------------------------------------
| Central Routes
|--------------------------------------------------------------------------
|
| These routes are for the central domain (mini.tryquot.com)
| Includes landing page, tenant registration, and superadmin panel
|
*/

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Tenant Registration
Route::get('/register', [TenantRegistrationController::class, 'create'])->name('tenant.register');
Route::post('/register', [TenantRegistrationController::class, 'store'])->name('tenant.store');

// SuperAdmin Authentication (No middleware or guest)
// Removed: consolidated into standard /login

// Stop Impersonation Route (Must be accessible by impersonated user who is NOT superadmin)
Route::post('superadmin/stop-impersonation', [SuperAdmin\TenantController::class, 'stopImpersonation'])
    ->middleware('auth')
    ->name('superadmin.stop-impersonation');

// SuperAdmin Panel
Route::get('superadmin/dashboard', function() {
    return redirect()->route('superadmin.dashboard');
});

Route::prefix('superadmin')->middleware(['auth', 'superadmin'])->name('superadmin.')->group(function () {
    Route::post('/logout', [SuperAdmin\AuthController::class, 'destroy'])->name('logout');
    Route::get('/', [SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

    // Role Management
    Route::resource('roles', SuperAdmin\RoleController::class);

    // Staff Management
    Route::resource('staff', SuperAdmin\StaffController::class);
    
    // Tenant Management
    Route::get('tenants/{tenant}/impersonate', [SuperAdmin\TenantController::class, 'impersonate'])->name('tenants.impersonate');
    Route::post('tenants/{tenant}/suspend', [SuperAdmin\TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/unsuspend', [SuperAdmin\TenantController::class, 'unsuspend'])->name('tenants.unsuspend');
    Route::put('tenants/{tenant}/plan', [SuperAdmin\TenantController::class, 'updatePlan'])->name('tenants.update-plan');
    Route::get('users/{userId}/impersonate', [SuperAdmin\TenantController::class, 'impersonateUser'])->name('users.impersonate');
    Route::put('tenants/{tenant}/password', [SuperAdmin\TenantController::class, 'resetPassword'])->name('tenants.password');
    Route::resource('tenants', SuperAdmin\TenantController::class);
    
    // Custom Domain Management
    Route::get('/custom-domains', [SuperAdmin\CustomDomainController::class, 'index'])->name('custom-domains.index');
    Route::post('/custom-domains/{id}/approve', [SuperAdmin\CustomDomainController::class, 'approve'])->name('custom-domains.approve');
    Route::post('/custom-domains/{id}/reject', [SuperAdmin\CustomDomainController::class, 'reject'])->name('custom-domains.reject');

    // Subscription Plans Management
    Route::resource('plans', SuperAdmin\PlanController::class);

    // Payment Gateways
    Route::get('payment-gateways', [SuperAdmin\PaymentGatewayController::class, 'index'])->name('payment-gateways.index');
    Route::put('payment-gateways/{gateway}', [SuperAdmin\PaymentGatewayController::class, 'update'])->name('payment-gateways.update');

    // Subscription Requests (Bank Transfer Approvals)
    Route::get('subscription-requests', [SuperAdmin\SubscriptionRequestController::class, 'index'])->name('subscription-requests.index');
    Route::post('subscription-requests/{id}/approve', [SuperAdmin\SubscriptionRequestController::class, 'approve'])->name('subscription-requests.approve');
    Route::post('subscription-requests/{id}/reject', [SuperAdmin\SubscriptionRequestController::class, 'reject'])->name('subscription-requests.reject');

    // Ticket Categories
    Route::resource('ticket-categories', SuperAdmin\TicketCategoryController::class);

    // Support Tickets
    Route::get('tickets', [SuperAdmin\SupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/{supportTicket}', [SuperAdmin\SupportTicketController::class, 'show'])->name('tickets.show');
    Route::put('tickets/{supportTicket}', [SuperAdmin\SupportTicketController::class, 'update'])->name('tickets.update');
    Route::post('tickets/{supportTicket}/reply', [SuperAdmin\SupportTicketController::class, 'reply'])->name('tickets.reply');

    // Global Settings
    Route::get('settings', [SuperAdmin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SuperAdmin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test-email', [SuperAdmin\SettingsController::class, 'sendTestEmail'])->name('settings.test-email');

    // Central PWA Branding
    Route::get('pwa/branding', [SuperAdmin\GlobalPwaController::class, 'edit'])->name('pwa.edit');
    Route::post('pwa/branding', [SuperAdmin\GlobalPwaController::class, 'update'])->name('pwa.update');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('subscriptions', [SuperAdmin\ReportController::class, 'subscriptions'])->name('subscriptions');
    });

    // Audit Logs
    Route::get('audit-logs', [SuperAdmin\AuditLogController::class, 'index'])->name('audit-logs.index');
    
    // Announcements
    Route::resource('announcements', SuperAdmin\AnnouncementController::class);
});

// Global Media Route for serving assets from private storage (SuperAdmin)
Route::get('/global/media', function () {
    $path = request()->query('path');
    if (!$path) abort(404);
    
    // Fix for query strings
    if (str_contains($path, '?')) {
        $path = explode('?', $path)[0];
    }

    $disk = \Illuminate\Support\Facades\Storage::disk('local');
    
    if ($disk->exists($path)) {
        return $disk->response($path);
    }
    
    abort(404);
})->name('global.media');
