<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $tenant = app('tenant');
        $config = \App\Models\StoreConfig::firstOrNew(['id' => 1]); 
        $menuCategories = \App\Models\StoreCollection::take(5)->get();

        $customer = Auth::guard('customer')->user();
        
        $orders = $customer->orders()
            ->with(['items.product', 'shippingAddress'])
            ->latest()
            ->paginate(10);

        return view('storefront.account.index', compact('tenant', 'config', 'menuCategories', 'customer', 'orders'));
    }
}
