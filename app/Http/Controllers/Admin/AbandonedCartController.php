<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;

class AbandonedCartController extends Controller
{
    public function index()
    {
        $abandonedCarts = Cart::with(['items.product', 'coupon'])
            ->whereHas('items')
            ->where('updated_at', '<', now()->subHour())
            ->whereDoesntHave('customer', function($query) {
                $query->whereHas('orders');
            })
            ->latest('updated_at')
            ->paginate(20);

        return view('admin.abandoned-carts.index', compact('abandonedCarts'));
    }
}
