<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StorefrontApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Authentication Routes
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

// Public Storefront Routes (with tenant context from path)
// Access via: /api/{tenant}/storefront/home
Route::middleware([\App\Http\Middleware\IdentifyTenantFromPath::class])
    ->prefix('{tenant}')
    ->group(function () {
        Route::get('/storefront/home', [StorefrontApiController::class, 'home']);
    });

// Protected Routes
Route::middleware(['auth:sanctum', \App\Http\Middleware\IdentifyTenantFromUser::class, 'tenant.access'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Dashboard Stats (Placeholder)
    Route::get('/dashboard-stats', function () {
        return response()->json([
            'sales_today' => 15000,
            'orders_pending' => 5,
            'new_customers' => 3
        ]);
    });
});
