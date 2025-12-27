<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Storefront; (Removed)
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are for tenant-specific pages
| Format: mini.tryquot.com/{tenant}/*
|
*/

// Middleware 'tenant' is alias for IdentifyTenantFromPath
Route::middleware(['web', \App\Http\Middleware\IdentifyTenantFromPath::class])
    ->prefix('{tenant}')
    ->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Storefront Routes (Protected by online_store feature)
    |--------------------------------------------------------------------------
    */
    
        // Tenant Media Route for serving assets
        // Tenant Media Route for serving assets
        Route::get('/media', function () {
            $path = request()->query('path');
            \Illuminate\Support\Facades\Log::info('Media Request', ['path' => $path]);
            
            if (!$path) abort(404);

            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            
            if ($disk->exists($path)) {
                $fullPath = $disk->path($path);
                \Illuminate\Support\Facades\Log::info('File found', ['fullPath' => $fullPath]);
                
                $mimeType = mime_content_type($fullPath) ?: 'image/jpeg';
                return response()->file($fullPath, [
                    'Content-Type' => $mimeType,
                    'Cache-Control' => 'public, max-age=3600'
                ]);
            } else {
                \Illuminate\Support\Facades\Log::warning('File not found', ['path' => $path, 'disk_root' => $disk->path('')]);
            }
            
            abort(404);
        })->name('tenant.media');

        // Dynamic PWA Manifest
        Route::get('/manifest.json', function () {
            $tenant = app('tenant');
            $settings = $tenant->data ?? [];
            $logoUrl = \App\Helpers\LogoHelper::getLogo(512); // Get large logo if possible, generic helper returns url

            return response()->json([
                'name' => $settings['pwa_name'] ?? $tenant->name,
                'short_name' => $settings['pwa_short_name'] ?? \Illuminate\Support\Str::limit($tenant->name, 12, ''),
                'start_url' => route('admin.dashboard', ['tenant' => $tenant->slug]),
                'display' => 'standalone',
                'background_color' => $settings['pwa_background_color'] ?? '#ffffff',
                'theme_color' => $settings['pwa_theme_color'] ?? '#4f46e5',
                'orientation' => 'portrait',
                'icons' => [
                    [
                        'src' => $logoUrl,
                        'sizes' => '512x512',
                        'type' => 'image/png'
                    ]
                ]
            ]);
        })->name('tenant.manifest');
    
    /*
    |--------------------------------------------------------------------------
    | Tenant Admin Routes
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | Tenant Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
            ->name('tenant.login');
        Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])
            ->name('tenant.login.store');
    });

    Route::middleware('auth')->post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
        ->name('tenant.logout');

    /*
    |--------------------------------------------------------------------------
    | Tenant Start Profile Routes
    |--------------------------------------------------------------------------
    */
    // Subscription Selection (Must be outside 'admin' prefix to be accessible before admin dashboard, but protected by auth)
    Route::middleware(['auth'])->group(function () {
        Route::get('/subscription/plans', [\App\Http\Controllers\Tenant\SubscriptionController::class, 'index'])->name('tenant.subscription.index');
        Route::post('/subscription/plans', [\App\Http\Controllers\Tenant\SubscriptionController::class, 'store'])->name('tenant.subscription.store');
        Route::get('/subscription/callback', [\App\Http\Controllers\Tenant\SubscriptionController::class, 'callback'])->name('tenant.subscription.callback');
        Route::post('/subscription/payment', [\App\Http\Controllers\Tenant\SubscriptionController::class, 'submitPayment'])->name('tenant.subscription.submit-payment');
    });

    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('admin')->middleware(['auth', 'subscription'])->name('admin.')->group(function () {
        
        // Dashboard
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Basic Inventory (Protected by inventory feature)
        Route::middleware(['feature:inventory'])->group(function () {
            Route::get('products/bulk-upload', [Admin\BulkImageUploadController::class, 'index'])->name('products.bulk-upload.index');
            Route::post('products/bulk-upload', [Admin\BulkImageUploadController::class, 'upload'])->name('products.bulk-upload.store');
            Route::post('products/bulk-action', [Admin\ProductController::class, 'bulkAction'])->name('products.bulk-action');
            Route::get('products/export', [Admin\ProductImportExportController::class, 'export'])->name('products.export');
            Route::get('products/template', [Admin\ProductImportExportController::class, 'template'])->name('products.template');
            Route::post('products/import', [Admin\ProductImportExportController::class, 'import'])->name('products.import');
            Route::resource('products', Admin\ProductController::class);
            Route::resource('categories', Admin\CategoryController::class);
            Route::resource('brands', Admin\BrandController::class);
        });
        
        // Orders & Sales (Protected by sales feature)
        Route::middleware(['feature:sales'])->group(function () {
            Route::resource('orders', Admin\OrderController::class);
            Route::resource('abandoned-carts', Admin\AbandonedCartController::class);
        });
        
        // POS Terminal (Protected by pos_retail feature)
        Route::middleware(['feature:pos_retail'])->group(function () {
            Route::get('/pos', [Admin\PosController::class, 'index'])->name('pos.index');
            Route::post('/pos', [Admin\PosController::class, 'store'])->name('pos.store');
            Route::get('/pos/display', [Admin\PosController::class, 'display'])->name('pos.display');
            Route::get('/pos/receipt/{order}', [Admin\PosController::class, 'receipt'])->name('pos.receipt');
        });

        // Support Tickets (Protected by support feature)
        Route::middleware(['feature:support'])->group(function () {
             Route::get('support', [Admin\SupportController::class, 'index'])->name('support.index');
             Route::post('support', [Admin\SupportController::class, 'store'])->name('support.store');
             Route::get('support/{support}', [Admin\SupportController::class, 'show'])->name('support.show');
             Route::post('support/{support}/reply', [Admin\SupportController::class, 'reply'])->name('support.reply');
        });
        
        // CRM Features (Protected by crm feature)
        Route::middleware(['feature:crm'])->group(function () {
            // Quick customer creation for POS/Sales
            Route::post('customers/quick-store', [Admin\CustomerController::class, 'quickStore'])->name('customers.quick-store');
            Route::resource('customers', Admin\CustomerController::class);
            
            // Renters Management
            Route::resource('renters', Admin\RenterController::class);
            Route::post('renters/{renter}/invoice', [Admin\RenterController::class, 'generateInvoice'])->name('renters.invoice');
            Route::post('renters/{renter}/payment', [Admin\RenterController::class, 'recordPayment'])->name('renters.payment');
        });
        
        // Marketing Features (Protected by marketing feature)
        Route::middleware(['feature:marketing'])->group(function () {
             Route::resource('enquiries', Admin\ProductEnquiryController::class);
             Route::resource('coupons', Admin\CouponController::class);
             Route::resource('banners', Admin\BannerController::class);
        });
        
        // Removed - Basic Inventory moved up and Accounts moved into accounting_core

        // Advanced Inventory (Purchase Orders, Stock Transfers, Warehouses)
        Route::middleware(['feature:inventory_advanced'])->group(function () {
             Route::resource('warehouses', Admin\WarehouseController::class);
             Route::resource('stock-transfers', Admin\StockTransferController::class);
             
             // Purchase Order Actions
             Route::get('purchase-orders/{purchase_order}/return', [Admin\PurchaseOrderController::class, 'returnsCreate'])->name('purchase-orders.returns.create');
             Route::post('purchase-orders/{purchase_order}/return', [Admin\PurchaseOrderController::class, 'returnsStore'])->name('purchase-orders.returns.store');
             Route::post('purchase-orders/bulk-action', [Admin\PurchaseOrderController::class, 'bulkAction'])->name('purchase-orders.bulk-action');
             Route::post('purchase-orders/{purchase_order}/place', [Admin\PurchaseOrderController::class, 'placeOrder'])->name('purchase-orders.place');
             Route::post('purchase-orders/{purchase_order}/receive', [Admin\PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
             Route::post('purchase-orders/{purchase_order}/convert', [Admin\PurchaseOrderController::class, 'convertToBill'])->name('purchase-orders.convert');
             Route::post('purchase-orders/{purchase_order}/items', [Admin\PurchaseOrderController::class, 'storeItem'])->name('purchase-orders.items.store');
             Route::delete('purchase-orders/{purchase_order}/items/{item}', [Admin\PurchaseOrderController::class, 'destroyItem'])->name('purchase-orders.items.destroy');
             
             Route::resource('purchase-orders', Admin\PurchaseOrderController::class);
             Route::get('suppliers/{supplier}/ledger', [Admin\SupplierController::class, 'ledger'])->name('suppliers.ledger');
             Route::resource('suppliers', Admin\SupplierController::class);
        });

        
        // Accounting Core (Chart of Accounts, Incomes, Expenses)
        Route::middleware(['feature:accounting_core'])->group(function () {
            Route::resource('accounts', Admin\AccountController::class);
            Route::resource('incomes', Admin\IncomeController::class);
            Route::resource('expenses', Admin\ExpenseController::class);
            Route::resource('payments', Admin\PaymentController::class);
            Route::post('/payment-types', [Admin\PaymentTypeController::class, 'store'])->name('payment-types.store');
            Route::delete('/payment-types/{paymentType}', [Admin\PaymentTypeController::class, 'destroy'])->name('payment-types.destroy');
            
            // Basic Reports
            Route::get('/accounting/profit-loss', [Admin\AccountingController::class, 'profitLoss'])->name('accounting.profit-loss');
        });

        // Advanced Accounting (Balance Sheet, Trial Balance)
        Route::middleware(['feature:accounting_advanced'])->group(function () {
            Route::get('/accounting/balance-sheet', [Admin\AccountingController::class, 'balanceSheet'])->name('accounting.balance-sheet');
            Route::get('/accounting/trial-balance', [Admin\AccountingController::class, 'trialBalance'])->name('accounting.trial-balance');
        });
        
        // Settings
        // Settings
        Route::get('/settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-email', [Admin\SettingsController::class, 'sendTestEmail'])->name('settings.test-email');
        
        // Custom Domain Request (Protected by custom_domain feature)
        Route::middleware(['feature:custom_domain'])->group(function () {
            Route::get('/settings/domain', [Admin\SettingsController::class, 'domain'])->name('settings.domain');
            Route::post('/settings/domain', [Admin\SettingsController::class, 'requestDomain'])->name('settings.domain.request');
            Route::delete('/settings/domain/{id}', [Admin\SettingsController::class, 'cancelDomainRequest'])->name('settings.domain.cancel');
        });
        
        // POS (Protected by pos_retail feature)
        Route::middleware('feature:pos_retail')->group(function () {
            Route::get('/pos', [Admin\PosController::class, 'index'])->name('pos.index');
            Route::post('/pos', [Admin\PosController::class, 'store'])->name('pos.store');
            Route::get('/pos/receipt/{order}', [Admin\PosController::class, 'receipt'])->name('pos.receipt');
        });

        // Announcements
        Route::get('/announcements', [Admin\AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements/{id}/read', [Admin\AnnouncementController::class, 'markAsRead'])->name('announcements.read');

        // CMS & Pages (Protected by online_store feature)
        Route::middleware(['feature:online_store'])->group(function () {
            Route::resource('pages', Admin\PageController::class);
            Route::resource('posts', Admin\PostController::class);
        });
        
        // Reports (Protected by reports_basic feature)
        Route::middleware(['feature:reports_basic'])->group(function () {
            Route::get('/reports', [Admin\ReportsController::class, 'index'])->name('reports.index');
            Route::get('/reports/sales', [Admin\ReportsController::class, 'sales'])->name('reports.sales');
            Route::get('/reports/customers', [Admin\ReportsController::class, 'customers'])->name('reports.customers');
            Route::get('/reports/financial', [Admin\ReportsController::class, 'financial'])->name('reports.financial');
            Route::get('/reports/export', [Admin\ReportsController::class, 'export'])->name('reports.export');
        });

        // Inventory Reports (Protected by reports_inventory feature)
        Route::middleware(['feature:reports_inventory'])->group(function () {
             Route::get('/reports/inventory', [Admin\ReportsController::class, 'inventory'])->name('reports.inventory');
        });

        // Team & Access (Protected by team_management feature)
        Route::middleware(['feature:team_management'])->group(function () {
             Route::resource('users', Admin\UserController::class);
             Route::resource('roles', Admin\RoleController::class);
        });

        // Tax Codes (Settings - Protected by accounting_core)
        Route::middleware(['feature:accounting_core'])->group(function () {
             Route::resource('tax-codes', Admin\TaxCodeController::class);
        });


    });
});

// Middleware for Custom Domain (Optional/Future Phase)
Route::middleware(['web', \App\Http\Middleware\IdentifyTenantFromCustomDomain::class])->group(function () {
   // We can include the same route groups here if we want custom domain support immediately
   // For now, let's keep it simple with path-based
});
