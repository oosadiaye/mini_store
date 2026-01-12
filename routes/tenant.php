<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Storefront; (Removed)
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\CMSController;

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
    
    Route::middleware(['feature:online_store', 'storefront_active'])->group(function () {
        Route::get('/signin', [\App\Http\Controllers\Storefront\AuthController::class, 'showLoginForm'])->name('storefront.login');
        Route::post('/signin', [\App\Http\Controllers\Storefront\AuthController::class, 'login'])->name('storefront.login.post');
        Route::get('/signup', [\App\Http\Controllers\Storefront\AuthController::class, 'showRegisterForm'])->name('storefront.register');
        Route::post('/signup', [\App\Http\Controllers\Storefront\AuthController::class, 'register'])->name('storefront.register.post');
        Route::post('/signout', [\App\Http\Controllers\Storefront\AuthController::class, 'logout'])->name('storefront.logout');

        Route::get('/', [App\Http\Controllers\Storefront\HomeController::class, 'index'])->name('storefront.home');
        Route::get('/about', [App\Http\Controllers\Storefront\HomeController::class, 'about'])->name('storefront.about');
        Route::get('/contact', [App\Http\Controllers\Storefront\HomeController::class, 'contact'])->name('storefront.contact');
        Route::get('/product', [App\Http\Controllers\Storefront\ProductController::class, 'list'])->name('storefront.products.index');
        Route::get('/product/{slug}', [App\Http\Controllers\Storefront\ProductController::class, 'detail'])->name('storefront.product.detail');
        Route::get('/category/{slug}', [App\Http\Controllers\Storefront\HomeController::class, 'category'])->name('storefront.category');
        
        // Debug Route
        Route::get('/debug-route', function() { return 'debug'; })->name('storefront.debug');


        
        // Account Routes
        Route::middleware('auth:customer')->group(function () {
             Route::get('/account', [App\Http\Controllers\Storefront\AccountController::class, 'index'])->name('storefront.account.index');
        });

        // Content Pages
        Route::get('/faq', [App\Http\Controllers\Storefront\PageController::class, 'faq'])->name('storefront.faq');
        Route::get('/shipping-policy', [App\Http\Controllers\Storefront\PageController::class, 'shipping'])->name('storefront.shipping');
        Route::get('/returns-policy', [App\Http\Controllers\Storefront\PageController::class, 'returns'])->name('storefront.returns');
        
        // Cart Routes
        Route::get('/cart', [App\Http\Controllers\Storefront\CartController::class, 'index'])->name('storefront.cart.index');
        Route::post('/cart', [App\Http\Controllers\Storefront\CartController::class, 'store'])->name('storefront.cart.store');
        Route::patch('/cart/{item}', [App\Http\Controllers\Storefront\CartController::class, 'update'])->name('storefront.cart.update');
        Route::delete('/cart/{item}', [App\Http\Controllers\Storefront\CartController::class, 'destroy'])->name('storefront.cart.destroy');
        Route::post('/cart/coupon', [App\Http\Controllers\Storefront\CartController::class, 'applyCoupon'])->name('storefront.cart.coupon');
        Route::delete('/cart/coupon', [App\Http\Controllers\Storefront\CartController::class, 'removeCoupon'])->name('storefront.cart.coupon.remove');

        // Checkout Routes
        Route::get('/checkout', [App\Http\Controllers\Storefront\CheckoutController::class, 'index'])->name('storefront.checkout.index');
        Route::post('/checkout', [App\Http\Controllers\Storefront\CheckoutController::class, 'store'])->name('storefront.checkout.store');
        Route::get('/checkout/callback', [App\Http\Controllers\Storefront\CheckoutController::class, 'callback'])->name('storefront.checkout.callback'); // Added Callback
        Route::post('/checkout/callback', [App\Http\Controllers\Storefront\CheckoutController::class, 'callback']); // Callback fallback for POST based gateways
        Route::get('/checkout/success', [App\Http\Controllers\Storefront\CheckoutController::class, 'success'])->name('storefront.checkout.success');

        // Order Tracking
        Route::get('/track-order', [App\Http\Controllers\Storefront\OrderTrackingController::class, 'index'])->name('storefront.orders.track');
        Route::post('/track-order', [App\Http\Controllers\Storefront\OrderTrackingController::class, 'store'])->name('storefront.orders.track.store');

        // Dynamic Grid API
        Route::get('/api/products', [App\Http\Controllers\Storefront\ProductController::class, 'index'])->name('storefront.api.products');
        Route::get('/api/storefront/products', [App\Http\Controllers\Storefront\ProductController::class, 'index'])->name('storefront.api.products.v2');
        Route::get('/api/storefront/product/{slug}', [App\Http\Controllers\Storefront\ProductController::class, 'show'])->name('storefront.api.product.show');
        Route::get('/api/storefront/home-sections', [App\Http\Controllers\Storefront\SectionController::class, 'index'])->name('storefront.api.sections');
        // Contact Us API
        Route::post('/api/storefront/contact', [App\Http\Controllers\Storefront\ContactController::class, 'store'])->name('storefront.api.contact');



        // SEO Routes
        Route::get('/sitemap.xml', [Admin\SeoController::class, 'sitemap'])->name('storefront.sitemap');
        Route::get('/robots.txt', [Admin\SeoController::class, 'robots'])->name('storefront.robots');

        // Customer Account (Protected)
        Route::middleware(['auth:customer'])->prefix('account')->group(function () {
            Route::get('/', [App\Http\Controllers\Storefront\AccountController::class, 'index'])->name('storefront.account.index');
        });
    });
    
        // Tenant Media Route for serving assets
        // Tenant Media Route for serving assets
        Route::get('/media', function () {
            $path = request()->query('path');
            \Illuminate\Support\Facades\Log::info('Media Request', ['path' => $path]);
            
            $path = trim($path); 
            if (!$path) abort(404);
            
            // Failsafe: If a full URL is passed erroneously, redirect to it
            // Move this BEFORE truncation to preserve remote URL parameters
            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                return redirect($path);
            }

            // Fix for query strings being appended to the path value (e.g. ?path=file.png?v=123)
            if (str_contains($path, '?')) {
                $path = explode('?', $path)[0];
            }

            $disk = \Illuminate\Support\Facades\Storage::disk('tenant');
            
            if ($disk->exists($path)) {
                $fullPath = $disk->path($path);
                \Illuminate\Support\Facades\Log::info('File found', ['fullPath' => $fullPath]);
                
                $mimeType = mime_content_type($fullPath) ?: 'image/jpeg';
                return response()->file($fullPath, [
                    'Content-Type' => $mimeType,
                    'Cache-Control' => 'public, max-age=3600'
                ]);
            } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                // Fallback to Public Disk (for migrated assets like logos)
                $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
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
    
    // Guest Support Routes (BEFORE auth middleware)
    Route::get('/login/support', [\App\Http\Controllers\Tenant\GuestSupportController::class, 'create'])->name('tenant.support.guest');
    Route::post('/login/support', [\App\Http\Controllers\Tenant\GuestSupportController::class, 'store'])->name('tenant.support.guest.store');
    Route::get('/login/support/success', [\App\Http\Controllers\Tenant\GuestSupportController::class, 'success'])->name('tenant.support.guest.success');

    Route::middleware('guest')->group(function () {
        Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
            ->name('tenant.register');
        Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);

        Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
            ->name('tenant.login');
        Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:login')
            ->name('tenant.login.store');

        Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
            ->name('password.request');

        Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
            ->name('password.store');
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

    Route::get('/admin/preview-financial-email', function () {
        // Mock Data for Preview
        $topProduct = [
            'name' => 'Premium Leather Backpack',
            'sold_count' => 12,
            'revenue' => '₦24,000.00'
        ];
        return new \App\Mail\WeeklyFinancialReport(
            'Test Store',
            'test_tenant',
            'Dec 24',
            'Dec 30',
            '₦150,000.00',
            '₦45,000.00',
            '₦105,000.00',
            $topProduct
        );
    });

    // Storefront Control API (Restored here to bypass storefront_active middleware)
    Route::middleware(['auth', 'feature:online_store'])->group(function () {
        Route::post('/api/tenant/settings/toggle-storefront', [Admin\TenantSettingsController::class, 'toggleStorefront'])->name('api.settings.toggle-storefront');
    });

    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('admin')->middleware(['auth', 'subscription'])->name('admin.')->group(function () {
        
        // Store Content Editor
    Route::get('/store-content', [App\Http\Controllers\Admin\StoreContentController::class, 'edit'])->name('store-content.edit');
    Route::post('/store-content', [App\Http\Controllers\Admin\StoreContentController::class, 'update'])->name('store-content.update');
    Route::post('/store-content/regenerate', [App\Http\Controllers\Admin\StoreContentController::class, 'regenerate'])->name('store-content.regenerate');
    Route::post('/store-content/generate-policy', [App\Http\Controllers\Admin\StoreContentController::class, 'generatePolicy'])->name('store-content.generate-policy');

    // Admin Inbox
    Route::get('/messages', [App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/{id}/read', [App\Http\Controllers\Admin\ContactMessageController::class, 'markAsRead'])->name('messages.read');

    // Dashboard
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Notifications
        Route::get('/notifications/read-all', [Admin\DashboardController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
        Route::get('/notifications/read/{id}', [Admin\DashboardController::class, 'markNotificationRead'])->name('notifications.read');
        
        // Basic Inventory (Protected by inventory feature)
        Route::middleware(['feature:inventory'])->group(function () {
            Route::get('products/bulk-upload', [Admin\BulkImageUploadController::class, 'index'])->name('products.bulk-upload.index');
            Route::post('products/bulk-upload', [Admin\BulkImageUploadController::class, 'upload'])->name('products.bulk-upload.store');
            Route::post('products/bulk-action', [Admin\ProductController::class, 'bulkAction'])->name('products.bulk-action');
            Route::get('products/export', [Admin\ProductImportExportController::class, 'export'])->name('products.export');
            Route::get('products/template', [Admin\ProductImportExportController::class, 'template'])->name('products.template');
            Route::post('products/import', [Admin\ProductImportExportController::class, 'import'])->name('products.import');
            Route::post('products/{product}/quick-image', [Admin\ProductController::class, 'quickImageUpload'])->name('products.quick-image');
            Route::resource('products', Admin\ProductController::class);
            Route::resource('categories', Admin\CategoryController::class);
            Route::resource('brands', Admin\BrandController::class);
            Route::post('orders', [Admin\OrderController::class, 'store'])->name('orders.store');
            Route::get('orders/resources/customers', [Admin\OrderController::class, 'getCustomers'])->name('orders.resources.customers');
            Route::get('orders/resources/products', [Admin\OrderController::class, 'getProducts'])->name('orders.resources.products');
            Route::resource('orders', Admin\OrderController::class)->except(['store']);
        });
        
        // Orders & Sales (Protected by sales feature)
        Route::middleware(['feature:sales'])->group(function () {
            Route::get('orders/{order}/invoice', [Admin\OrderController::class, 'invoice'])->name('orders.invoice');
            Route::patch('orders/{order}/update-status', [Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
            Route::patch('orders/{order}/update-payment', [Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');
            Route::put('orders/{order}/tracking', [Admin\OrderController::class, 'updateTracking'])->name('orders.tracking');
            Route::post('orders/bulk-action', [Admin\OrderController::class, 'bulkAction'])->name('orders.bulk-action');
            Route::get('orders/returns/{order}/create', [Admin\OrderReturnController::class, 'create'])->name('orders.returns.create');
            
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
            Route::get('customers/{customer}/ledger', [Admin\CustomerController::class, 'ledger'])->name('customers.ledger');
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
             Route::get('stock-transfers/get-stock', [Admin\StockTransferController::class, 'getStock'])->name('stock-transfers.get-stock');
             Route::resource('warehouses', Admin\WarehouseController::class);
             Route::post('stock-transfers/{stock_transfer}/approve', [Admin\StockTransferController::class, 'approve'])->name('stock-transfers.approve');
             Route::post('stock-transfers/{stock_transfer}/reject', [Admin\StockTransferController::class, 'reject'])->name('stock-transfers.reject');
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
             Route::post('supplier-invoices/{invoice}/reverse', [Admin\PurchaseOrderController::class, 'reverseBill'])->name('supplier-invoices.reverse');
             
             Route::resource('purchase-orders', Admin\PurchaseOrderController::class);
             Route::get('suppliers/{supplier}/ledger', [Admin\SupplierController::class, 'ledger'])->name('suppliers.ledger');
             Route::resource('suppliers', Admin\SupplierController::class);

             // Tenant Settings
             Route::get('/settings/domain', [Admin\SettingsController::class, 'domain'])->name('settings.domain');
             Route::post('/settings/domain', [Admin\SettingsController::class, 'requestDomain'])->name('settings.domain.request');
             Route::delete('/settings/domain/{id}', [Admin\SettingsController::class, 'cancelDomainRequest'])->name('settings.domain.cancel');
        });

        
        // Accounting Core (Chart of Accounts, Incomes, Expenses)
        Route::middleware(['feature:accounting_core'])->group(function () {
            Route::resource('accounts', Admin\AccountController::class);
            Route::resource('incomes', Admin\IncomeController::class);
            Route::resource('expenses', Admin\ExpenseController::class);
            Route::resource('payments', Admin\PaymentController::class);
            Route::post('payments/advance', [Admin\PaymentController::class, 'storeAdvancePayment'])->name('payments.advance');
            Route::post('payments/{payment}/allocate', [Admin\PaymentController::class, 'allocatePayment'])->name('payments.allocate');
            Route::post('orders/{order}/payment', [Admin\PaymentController::class, 'storeCustomerPayment'])->name('orders.payment');
            Route::post('supplier-invoices/{supplier_invoice}/payment', [Admin\PaymentController::class, 'storeSupplierPayment'])->name('supplier-invoices.payment');
            Route::resource('journal-entries', Admin\JournalEntryController::class)->only(['index', 'show']);
            Route::post('/payment-types', [Admin\PaymentTypeController::class, 'store'])->name('payment-types.store');
            Route::delete('/payment-types/{paymentType}', [Admin\PaymentTypeController::class, 'destroy'])->name('payment-types.destroy');
            Route::post('/payment-types/{paymentType}/toggle', [Admin\PaymentTypeController::class, 'toggle'])->name('payment-types.toggle');
            Route::post('/payment-types/{paymentType}/toggle-storefront', [Admin\PaymentTypeController::class, 'toggleStorefront'])->name('payment-types.toggle-storefront');
            
            // Basic Reports
            Route::get('/accounting/profit-loss', [Admin\AccountingController::class, 'profitLoss'])->name('accounting.profit-loss');
            Route::get('/financial-analysis', [Admin\FinancialAnalysisController::class, 'index'])->name('financial-analysis.index');
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
        Route::post('/settings/seo-suggest', [Admin\SeoController::class, 'suggest'])->name('settings.seo-suggest');
        
        // Store Setup Wizard (Protected by storefront_active)
        Route::middleware(['storefront_active'])->group(function () {
            Route::get('/wizard', [Admin\StoreSetupWizardController::class, 'index'])->name('wizard.index');
            Route::post('/wizard/update', [Admin\StoreSetupWizardController::class, 'update'])->name('wizard.update');
            Route::post('/wizard/finish', [Admin\StoreSetupWizardController::class, 'finish'])->name('wizard.finish');
        });

        // CMS Visual Editor
        Route::post('/cms/save', [\App\Http\Controllers\Admin\CMSController::class, 'save'])->name('cms.save');
        
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
        Route::middleware(['feature:online_store', 'storefront_active'])->group(function () {
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
             Route::get('/reports/movement', [Admin\ReportsController::class, 'movement'])->name('reports.movement');
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

        // WooCommerce Integration
        Route::middleware(['feature:woocommerce'])->controller(Admin\WooCommerceController::class)->prefix('woocommerce')->name('woocommerce.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/settings', 'updateSettings')->name('settings');
            Route::post('/sync', 'manualSync')->name('sync');
            Route::post('/webhooks', 'setupWebhooks')->name('webhooks');
            Route::get('/orders', 'orders')->name('orders');
        });




    });
});

// WooCommerce Webhook (External) - Excluded from CSRF in VerifyCsrfToken middleware usually, 
// but since we are in tenant.php which is web middleware group, we need to be careful.
// Ideally usage of api.php is better, but tenant identification is needed.
// We will manually rely on the signature verification.
Route::middleware(['api', \App\Http\Middleware\IdentifyTenantFromPath::class])
    ->prefix('{tenant}/api/woocommerce')
    ->group(function () {
        Route::post('/webhook', [\App\Http\Controllers\Api\WooCommerceWebhookController::class, 'handle'])->name('api.woocommerce.webhook');
    });

// Middleware for Custom Domain (Optional/Future Phase)
Route::middleware(['web', \App\Http\Middleware\IdentifyTenantFromCustomDomain::class])->group(function () {
   // We can include the same route groups here if we want custom domain support immediately
   // For now, let's keep it simple with path-based
});

