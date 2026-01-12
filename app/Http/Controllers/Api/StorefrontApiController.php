<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\StoreConfig;
use Illuminate\Http\Request;
use App\Services\StorefrontService;

class StorefrontApiController extends Controller
{
    /**
     * Get homepage data including featured products and category sections
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function home(StorefrontService $storefrontService)
    {
        // Safely resolve tenant - check if bound first
        if (!app()->bound('tenant')) {
            return response()->json([
                'error' => 'Tenant context not found. Please access via tenant subdomain or custom domain.'
            ], 400);
        }
        
        $tenant = app('tenant');
        
        try {
            $data = $storefrontService->getHomeData($tenant);
            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Storefront API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
