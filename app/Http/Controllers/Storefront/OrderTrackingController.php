<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    public function index()
    {
        return view('storefront.orders.track');
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->whereHas('customer', function ($query) use ($request) {
                $query->where('email', $request->email);
            })
            ->with(['items.product', 'shippingAddress'])
            ->first();

        if (!$order) {
            return back()->with('error', 'Order not found. Please check your order number and email address.')->withInput();
        }

        return view('storefront.orders.status', compact('order'));
    }
}
