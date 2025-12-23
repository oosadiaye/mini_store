<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are loaded for each tenant's subdomain
| Example: tenant1.localhost, tenant2.localhost
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\ConfigureTenantMail::class,
])->group(function () {
    // Storefront (Customer-facing)
    Route::get('/', [\App\Http\Controllers\Storefront\StorefrontController::class, 'index'])
        ->name('storefront.home');
        
    Route::get('/sitemap.xml', [\App\Http\Controllers\Storefront\SitemapController::class, 'index'])->name('storefront.sitemap');
    Route::get('/robots.txt', [\App\Http\Controllers\Storefront\RobotsController::class, 'index'])->name('storefront.robots');
    
    // Manual Tenant Asset Route (Fix for images not showing)
    // Manual Tenant Asset Route (Fix for images not showing)
    // Custom Tenant Media Route to avoid conflict with stancl/tenancy
    // Custom Tenant Media Route (Query Param) to handle PHP built-in server limitations
    Route::get('/media', function () {
        $path = request()->query('path');
        if (!$path) abort(404);

        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        
        // The 'public' disk already points to tenant-specific storage
        // Just check if the file exists at the given path
        if ($disk->exists($path)) {
            $fullPath = $disk->path($path);
            $mimeType = mime_content_type($fullPath) ?: 'image/jpeg';
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=3600'
            ]);
        }
        
        \Log::error("Tenant Media: File not found at path: $path");
        abort(404, "File not found: $path");
    })->name('tenant.media');

    Route::get('/products', [\App\Http\Controllers\Storefront\StorefrontController::class, 'products'])
        ->name('storefront.products');
    
    Route::get('/product/{product}', [\App\Http\Controllers\Storefront\StorefrontController::class, 'show'])
        ->name('storefront.product.show');
    
    Route::get('/category/{category}', [\App\Http\Controllers\Storefront\StorefrontController::class, 'category'])
        ->name('storefront.category');
    
    Route::get('/search', [\App\Http\Controllers\Storefront\StorefrontController::class, 'search'])
        ->name('storefront.search');

    Route::get('/blog/{post:slug}', [\App\Http\Controllers\Storefront\StorefrontController::class, 'blogPost'])
        ->name('storefront.blog.show');

    // Customer Authentication
    // Customer Authentication
    Route::get('login', [\App\Http\Controllers\Storefront\CustomerAuthController::class, 'showLoginForm'])
        ->name('storefront.login');
    
    Route::post('login', [\App\Http\Controllers\Storefront\CustomerAuthController::class, 'login'])
        ->middleware('guest:customer');

    Route::get('register', [\App\Http\Controllers\Storefront\CustomerAuthController::class, 'showRegisterForm'])
        ->name('storefront.register');

    Route::post('register', [\App\Http\Controllers\Storefront\CustomerAuthController::class, 'register'])
        ->middleware('guest:customer');

    Route::middleware('auth:customer')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Storefront\CustomerAuthController::class, 'logout'])->name('storefront.logout');
        Route::get('my-account', [\App\Http\Controllers\Storefront\CustomerAuthController::class, 'profile'])->name('storefront.customer.profile');
        Route::get('my-account/orders/{order}', [\App\Http\Controllers\Storefront\CustomerAuthController::class, 'showOrder'])->name('storefront.customer.order');
        Route::get('my-account/orders/{order}/invoice', [\App\Http\Controllers\Storefront\InvoiceController::class, 'show'])->name('storefront.customer.invoice');
        
        // Returns
        Route::get('my-account/orders/{order}/return', [\App\Http\Controllers\Storefront\OrderReturnController::class, 'create'])->name('storefront.order.return.create');
        Route::post('my-account/orders/{order}/return', [\App\Http\Controllers\Storefront\OrderReturnController::class, 'store'])->name('storefront.order.return.store');
    });

    // Add new product detail route and enquiry submission route
    Route::get('products', [\App\Http\Controllers\Storefront\StorefrontController::class, 'products'])->name('storefront.products.index');

    Route::get('products/{product}', [\App\Http\Controllers\Storefront\StorefrontController::class, 'show'])->name('storefront.product');
    Route::post('products/{product}/enquiry', [\App\Http\Controllers\Storefront\StorefrontController::class, 'submitEnquiry'])->name('storefront.product.enquiry');
    
    // Reviews
    Route::post('products/{product}/reviews', [\App\Http\Controllers\Storefront\ReviewController::class, 'store'])->name('storefront.product.reviews.store')->middleware('auth:customer');

    // Cart Routes
    Route::get('/cart', [\App\Http\Controllers\Storefront\CartController::class, 'index'])->name('storefront.cart.index');
    Route::post('/cart/add/{product}', [\App\Http\Controllers\Storefront\CartController::class, 'add'])->name('storefront.cart.add');
    Route::patch('/cart/{cartItem}', [\App\Http\Controllers\Storefront\CartController::class, 'update'])->name('storefront.cart.update');
    Route::delete('/cart/{cartItem}', [\App\Http\Controllers\Storefront\CartController::class, 'remove'])->name('storefront.cart.remove');
    Route::post('/cart/clear', [\App\Http\Controllers\Storefront\CartController::class, 'clear'])->name('storefront.cart.clear');
    Route::post('/cart/coupon', [\App\Http\Controllers\Storefront\CartController::class, 'applyCoupon'])->name('storefront.cart.coupon');
    Route::delete('/cart/coupon', [\App\Http\Controllers\Storefront\CartController::class, 'removeCoupon'])->name('storefront.cart.coupon.remove');
    Route::post('/cart/update-email', [\App\Http\Controllers\Storefront\CartController::class, 'updateEmail'])->name('storefront.cart.update-email');
    
    // Facebook Catalog Feed
    Route::get('/facebook-catalog.xml', function () {
        $products = \App\Models\Product::with(['images', 'category'])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->get();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', tenant('name'));
        $channel->addChild('link', url('/'));
        $channel->addChild('description', 'Product catalog for ' . tenant('name'));

        foreach ($products as $product) {
            $item = $channel->addChild('item');
            $item->addChild('g:id', $product->id, 'http://base.google.com/ns/1.0');
            $item->addChild('g:title', htmlspecialchars($product->name), 'http://base.google.com/ns/1.0');
            $item->addChild('g:description', htmlspecialchars(strip_tags($product->description ?? '')), 'http://base.google.com/ns/1.0');
            $item->addChild('g:link', route('storefront.product.show', $product), 'http://base.google.com/ns/1.0');
            
            if ($product->images->isNotEmpty()) {
                $item->addChild('g:image_link', $product->images->first()->url, 'http://base.google.com/ns/1.0');
            }
            
            $item->addChild('g:condition', 'new', 'http://base.google.com/ns/1.0');
            $item->addChild('g:availability', $product->stock_quantity > 0 ? 'in stock' : 'out of stock', 'http://base.google.com/ns/1.0');
            $item->addChild('g:price', number_format($product->price, 2) . ' ' . (tenant('data')['currency_code'] ?? 'USD'), 'http://base.google.com/ns/1.0');
            $item->addChild('g:brand', $product->brand->name ?? tenant('name'), 'http://base.google.com/ns/1.0');
            
            if ($product->category) {
                $item->addChild('g:product_type', $product->category->name, 'http://base.google.com/ns/1.0');
            }
        }

        return response($xml->asXML(), 200)
            ->header('Content-Type', 'application/xml');
    })->name('facebook.catalog');
    
    Route::post('/contact', [\App\Http\Controllers\Storefront\StorefrontController::class, 'contactSubmit'])->name('storefront.contact.submit');

    // Dynamic Pages
    Route::get('/pages/{slug}', [\App\Http\Controllers\Storefront\StorefrontController::class, 'page'])->name('storefront.page');

    // Checkout Routes
    Route::get('/checkout', [\App\Http\Controllers\Storefront\CheckoutController::class, 'index'])->name('storefront.checkout.index');
    Route::post('/checkout', [\App\Http\Controllers\Storefront\CheckoutController::class, 'store'])->name('storefront.checkout.store');
    Route::get('/checkout/callback', [\App\Http\Controllers\Storefront\CheckoutController::class, 'callback'])->name('storefront.checkout.callback');
    Route::get('/checkout/success/{order}', [\App\Http\Controllers\Storefront\CheckoutController::class, 'success'])->name('storefront.checkout.success');

    // Redirect /dashboard to /admin/dashboard
    Route::get('/dashboard', function () {
        return redirect('/admin/dashboard');
    })->middleware('auth')->name('dashboard');

    // Tenant authentication
    require __DIR__.'/tenant-auth.php';

    // Tenant Impersonation
    Route::get('/impersonate/{token}', function ($token) {
        return Stancl\Tenancy\Features\UserImpersonation::makeResponse($token);
    });

    Route::get('/impersonate/leave', function () {
        // Assuming the central app is at the root domain
        $centralDomain = config('app.url'); // This might need adjustment based on setup
        // Ideally we should use the tenancy config to find the central domain return path
        // For now, let's redirect to the central admin tenants page
        // "http://localhost:8000/admin/dashboard" - hardcoded for safety if config is ambiguous
        
         // Use the CENTRAL_DOMAIN env if available or parse from app.url
        // A robust way for local development
         $centralUrl = env('APP_URL', 'http://localhost:8000');
         
        return redirect($centralUrl . '/superadmin/tenants'); 
    })->name('impersonate.leave');

    // Admin Panel (Tenant Admin)
    Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
        // Redirect /admin to /admin/dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Notifications
        Route::get('notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.read.all');
        Route::get('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');

        Route::get('settings/theme', [\App\Http\Controllers\Admin\StorefrontSettingController::class, 'index'])->name('settings.storefront');
        Route::post('settings/theme', [\App\Http\Controllers\Admin\StorefrontSettingController::class, 'update'])->name('settings.storefront.update');

        // Theme Management
        Route::get('theme', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('theme.index');
        Route::post('theme/activate', [\App\Http\Controllers\Admin\ThemeController::class, 'activate'])->name('theme.activate');
        Route::get('theme/activate', function() { return redirect()->route('admin.theme.index')->with('error', 'Please click the Activate button to change themes.'); });
        Route::post('theme/update', [\App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('theme.update');
        Route::delete('theme/{theme}', [\App\Http\Controllers\Admin\ThemeController::class, 'destroy'])->name('theme.destroy');
        
        // Unified Theme Customizer with Page Builder
        Route::get('theme-customizer', [\App\Http\Controllers\Admin\ThemeController::class, 'customizer'])->name('theme.customizer');
        Route::post('theme-customizer/save', [\App\Http\Controllers\Admin\ThemeController::class, 'saveCustomizer'])->name('theme.customizer.save');
        
        // Page Builder
        Route::get('page-builder', [\App\Http\Controllers\Admin\PageBuilderController::class, 'index'])->name('page-builder.index');
        Route::post('page-builder', [\App\Http\Controllers\Admin\PageBuilderController::class, 'update'])->name('page-builder.update');
        Route::post('page-builder/reset', [\App\Http\Controllers\Admin\PageBuilderController::class, 'reset'])->name('page-builder.reset');
        Route::post('page-builder/template/save', [\App\Http\Controllers\Admin\PageBuilderController::class, 'saveAsTemplate'])->name('page-builder.template.save');
        Route::get('page-builder/template/{template}', [\App\Http\Controllers\Admin\PageBuilderController::class, 'loadTemplate'])->name('page-builder.template.load');
        Route::post('page-builder/upload', [\App\Http\Controllers\Admin\PageBuilderController::class, 'uploadImage'])->name('page-builder.upload');
        Route::post('page-builder/render', [\App\Http\Controllers\Admin\PageBuilderController::class, 'render'])->name('page-builder.render');

        Route::get('products/export', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'export'])->name('products.export');
        Route::get('products/template', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'template'])->name('products.template');
        Route::post('products/import', [\App\Http\Controllers\Admin\ProductImportExportController::class, 'import'])->name('products.import');
        
        // Product Combo Routes
        Route::post('products/{product}/combos', [\App\Http\Controllers\Admin\ProductComboController::class, 'store'])->name('products.combos.store');
        Route::delete('products/{product}/combos/{childProduct}', [\App\Http\Controllers\Admin\ProductComboController::class, 'destroy'])->name('products.combos.destroy');

        // Bulk Upload Routes (separate from products resource)
        Route::get('bulk-upload-images', [\App\Http\Controllers\Admin\BulkImageUploadController::class, 'index'])
            ->name('products.bulk-upload.index');
        Route::post('bulk-upload-images', [\App\Http\Controllers\Admin\BulkImageUploadController::class, 'upload'])
            ->name('products.bulk-upload.store');
        
        // Product Bulk Actions
        Route::post('products/bulk-action', [\App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])
            ->name('products.bulk-action');

        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        Route::delete('products/{image}/delete-image', [\App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])
            ->name('products.delete-image');
        Route::post('products/{product}/quick-image', [\App\Http\Controllers\Admin\ProductController::class, 'quickImageUpload'])
            ->name('products.quick-image');

        // Orders
        // Orders & POS
        // Orders & POS
        Route::get('orders/{order}/return', [\App\Http\Controllers\Admin\OrderReturnController::class, 'create'])->name('orders.returns.create');
        Route::post('orders/{order}/return', [\App\Http\Controllers\Admin\OrderReturnController::class, 'store'])->name('orders.returns.store');
    
        Route::put('orders/{order}/tracking', [\App\Http\Controllers\Admin\OrderController::class, 'updateTracking'])->name('orders.tracking');
        
        // Return Management Routes
        Route::get('returns', [\App\Http\Controllers\Admin\OrderReturnController::class, 'index'])->name('returns.index');
        Route::get('returns/{return}', [\App\Http\Controllers\Admin\OrderReturnController::class, 'show'])->name('returns.show');
        Route::put('returns/{return}', [\App\Http\Controllers\Admin\OrderReturnController::class, 'update'])->name('returns.update');
            
        Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::post('orders', [\App\Http\Controllers\Admin\OrderController::class, 'store'])->name('orders.store'); // Store Manual Order
        Route::get('orders/create', [\App\Http\Controllers\Admin\OrderController::class, 'create'])->name('orders.create'); // Manual Sales Order
        Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::patch('orders/{order}/payment', [\App\Http\Controllers\Admin\OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment');
        Route::get('orders/{order}/invoice', [\App\Http\Controllers\Storefront\InvoiceController::class, 'show'])->name('orders.invoice');

        // POS
        Route::get('pos', [\App\Http\Controllers\Admin\PosController::class, 'index'])->name('pos.index');
        Route::get('pos/display', [\App\Http\Controllers\Admin\PosController::class, 'display'])->name('pos.display');
        Route::get('pos/receipt/{order}', [\App\Http\Controllers\Admin\PosController::class, 'receipt'])->name('pos.receipt');
        Route::post('pos/store', [\App\Http\Controllers\Admin\PosController::class, 'store'])->name('pos.store');

        // AI Design Assistant
        Route::get('ai-design', [\App\Http\Controllers\Admin\AiDesignController::class, 'index'])->name('ai.index');
        Route::post('ai-design/generate', [\App\Http\Controllers\Admin\AiDesignController::class, 'generateDesign'])->name('ai.generate');
        Route::post('ai-design/copy', [\App\Http\Controllers\Admin\AiDesignController::class, 'generateCopy'])->name('ai.copy');

        // Categories
        Route::get('categories/export', [\App\Http\Controllers\Admin\CategoryImportExportController::class, 'export'])->name('categories.export');
        Route::get('categories/template', [\App\Http\Controllers\Admin\CategoryImportExportController::class, 'template'])->name('categories.template');
        Route::post('categories/import', [\App\Http\Controllers\Admin\CategoryImportExportController::class, 'import'])->name('categories.import');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

        // Theme Customization
        Route::get('theme', [\App\Http\Controllers\Admin\ThemeController::class, 'index'])->name('theme.index');
        Route::post('theme/activate', [\App\Http\Controllers\Admin\ThemeController::class, 'activate'])->name('theme.activate');
        Route::post('theme', [\App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('theme.update');
        Route::delete('theme/{theme}', [\App\Http\Controllers\Admin\ThemeController::class, 'destroy'])->name('theme.destroy');

        // Warehouses
        Route::resource('warehouses', \App\Http\Controllers\Admin\WarehouseController::class);

        // Suppliers
        Route::get('suppliers/{supplier}/ledger', [\App\Http\Controllers\Admin\SupplierController::class, 'ledger'])->name('suppliers.ledger');
        Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class);

        // Customers
        Route::get('customers/{customer}/ledger', [\App\Http\Controllers\Admin\CustomerController::class, 'ledger'])->name('customers.ledger');
        Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');

        // Banners & Reviews
        Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewController::class)->only(['index', 'update', 'destroy']);
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
        
        // Variants
        Route::post('products/{product}/variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'store'])->name('products.variants.store');
        Route::delete('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'destroy'])->name('variants.destroy');

        // Payments
        Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::post('purchase-orders/{purchase_order}/payment', [\App\Http\Controllers\Admin\PaymentController::class, 'storeSupplierPayment'])->name('purchase-orders.payment.store');
        Route::post('orders/{order}/payment', [\App\Http\Controllers\Admin\PaymentController::class, 'storeCustomerPayment'])->name('orders.payment.store');
        
        // Purchase Orders
        Route::get('purchase-orders/{purchase_order}/return', [\App\Http\Controllers\Admin\PurchaseReturnController::class, 'create'])->name('purchase-orders.returns.create');
        Route::post('purchase-orders/{purchase_order}/return', [\App\Http\Controllers\Admin\PurchaseReturnController::class, 'store'])->name('purchase-orders.returns.store');
        
        Route::post('purchase-orders/bulk', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'bulkAction'])->name('purchase-orders.bulk'); // Bulk Action
        Route::resource('purchase-orders', \App\Http\Controllers\Admin\PurchaseOrderController::class);
        Route::post('purchase-orders/{purchase_order}/place', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'placeOrder'])->name('purchase-orders.place');
        Route::post('purchase-orders/{purchase_order}/items', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'storeItem'])->name('purchase-orders.items.store');
        Route::delete('purchase-orders/{purchase_order}/items/{item}', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'destroyItem'])->name('purchase-orders.items.destroy');
        Route::post('purchase-orders/{purchase_order}/receive', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::post('purchase-orders/{purchase_order}/convert', [\App\Http\Controllers\Admin\PurchaseOrderController::class, 'convertToBill'])->name('purchase-orders.convert');

        // Blog Posts
        Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);

        // Pages
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
        Route::get('pages/{page}/builder', [\App\Http\Controllers\Admin\PageController::class, 'builder'])->name('pages.builder');
        Route::post('pages/{page}/reorder', [\App\Http\Controllers\Admin\PageController::class, 'reorderSections'])->name('pages.reorder');
        Route::post('pages/{page}/sections', [\App\Http\Controllers\Admin\PageController::class, 'storeSection'])->name('pages.sections.store');
        Route::put('pages/{page}/sections/{section}', [\App\Http\Controllers\Admin\PageController::class, 'updateSection'])->name('pages.sections.update');
        Route::delete('pages/{page}/sections/{section}', [\App\Http\Controllers\Admin\PageController::class, 'deleteSection'])->name('pages.sections.delete');

        // Barcodes
        Route::get('barcodes', [\App\Http\Controllers\Admin\BarcodeController::class, 'index'])->name('barcodes.index');
        Route::post('barcodes/print', [\App\Http\Controllers\Admin\BarcodeController::class, 'print'])->name('barcodes.print');

        // Accounting - Chart of Accounts
        Route::get('accounts/{account}/copy', [\App\Http\Controllers\Admin\AccountController::class, 'copy'])->name('accounts.copy');
        Route::resource('accounts', \App\Http\Controllers\Admin\AccountController::class);
        Route::resource('incomes', \App\Http\Controllers\Admin\IncomeController::class);
        Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);
        Route::resource('journal-entries', \App\Http\Controllers\Admin\JournalEntryController::class)->only(['index', 'show']);

        // Financial Reports
        Route::get('accounting/trial-balance', [\App\Http\Controllers\Admin\AccountingController::class, 'trialBalance'])->name('accounting.trial-balance');
        Route::get('accounting/profit-loss', [\App\Http\Controllers\Admin\AccountingController::class, 'profitLoss'])->name('accounting.profit-loss');
        Route::get('accounting/balance-sheet', [\App\Http\Controllers\Admin\AccountingController::class, 'balanceSheet'])->name('accounting.balance-sheet');

        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        
        // Payment Types
        Route::resource('payment-types', \App\Http\Controllers\Admin\PaymentTypeController::class)->only(['store', 'destroy']);
        Route::post('payment-types/{payment_type}/toggle', [\App\Http\Controllers\Admin\PaymentTypeController::class, 'toggle'])->name('payment-types.toggle');

        // Stock Transfers
        Route::resource('stock-transfers', \App\Http\Controllers\Admin\StockTransferController::class);
        Route::post('stock-transfers/{stock_transfer}/approve', [\App\Http\Controllers\Admin\StockTransferController::class, 'approve'])->name('stock-transfers.approve');
        Route::post('stock-transfers/{stock_transfer}/reject', [\App\Http\Controllers\Admin\StockTransferController::class, 'reject'])->name('stock-transfers.reject');
        Route::get('stock-transfers/stock/check', [\App\Http\Controllers\Admin\StockTransferController::class, 'getStock'])->name('stock-transfers.get-stock');

        // Reports & Analytics
        Route::get('reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
        Route::get('reports/sales', [\App\Http\Controllers\Admin\ReportsController::class, 'sales'])->name('reports.sales');
        Route::get('reports/inventory', [\App\Http\Controllers\Admin\ReportsController::class, 'inventory'])->name('reports.inventory');
        Route::get('reports/customers', [\App\Http\Controllers\Admin\ReportsController::class, 'customers'])->name('reports.customers');
        Route::get('reports/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'financial'])->name('reports.financial');
        Route::get('reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('reports.export');

        // Marketing
        Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
        
        // Product Enquiries
        Route::get('enquiries', [\App\Http\Controllers\Admin\ProductEnquiryController::class, 'index'])
            ->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [\App\Http\Controllers\Admin\ProductEnquiryController::class, 'show'])
            ->name('enquiries.show');
        Route::post('enquiries/{enquiry}/reply', [\App\Http\Controllers\Admin\ProductEnquiryController::class, 'reply'])
            ->name('enquiries.reply');
        Route::patch('enquiries/{enquiry}/status', [\App\Http\Controllers\Admin\ProductEnquiryController::class, 'updateStatus'])
            ->name('enquiries.status');

        // User Management
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Abandoned Carts
        Route::get('abandoned-carts', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'index'])->name('abandoned-carts.index');
    });
});
