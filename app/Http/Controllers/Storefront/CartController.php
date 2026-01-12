<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\StorefrontProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index()
    {
        $tenant = app('tenant');
        $config = \App\Models\StoreConfig::firstOrNew(['id' => 1]); // For layout
        
        $cart = $this->getCart();

        return view('storefront.cart.index', compact('cart', 'config'));
    }

    /**
     * Add an item to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = StorefrontProduct::findOrFail($request->product_id);
        
        // Stock Check
        if ($product->available_stock < $request->input('quantity', 1)) {
            return response()->json(['error' => 'Insufficient stock.'], 422);
        }

        $cart = $this->getCart();

        // Check if item exists
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $request->input('quantity', 1));
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->input('quantity', 1),
                'price' => $product->price, // Snapshot price
            ]);
        }
        
        // Refresh to get new totals
        $cart->load('items');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Added to cart',
                'cart_count' => $cart->total_items
            ]);
        }

        return redirect()->back()->with('success', 'Item added to cart.');
    }

    /**
     * Update item quantity.
     */
    public function update(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        $cart = $this->getCart();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        
        // Stock Check (simplified for now, ideally check against product->available_stock)
        
        $item->update(['quantity' => $request->quantity]);

        return redirect()->route('storefront.cart.index')->with('success', 'Cart updated.');
    }

    /**
     * Remove item from cart.
     */
    public function destroy($itemId)
    {
        $cart = $this->getCart();
        $cart->items()->where('id', $itemId)->delete();

        return redirect()->route('storefront.cart.index')->with('success', 'Item removed.');
    }

    /**
     * Apply Coupon to Cart
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $code = strtoupper($request->code);
        $coupon = \App\Models\Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code.');
        }

        if (!$coupon->isValid()) {
             return back()->with('error', 'Coupon is expired or invalid.');
        }

        $cart = $this->getCart();
        
        // Check min spend
        if ($coupon->min_spend && $cart->subtotal < $coupon->min_spend) {
            return back()->with('error', 'Minimum spend of ' . number_format($coupon->min_spend, 2) . ' required.');
        }

        $cart->update(['coupon_id' => $coupon->id]);

        return back()->with('success', 'Coupon applied successfully!');
    }

    /**
     * Remove Coupon from Cart
     */
    public function removeCoupon()
    {
        $cart = $this->getCart();
        $cart->update(['coupon_id' => null]);

        return back()->with('success', 'Coupon removed.');
    }

    /**
     * Helper to get or create cart based on session.
     */
    private function getCart()
    {
        $sessionId = Session::getId();
        $user = auth()->user();

        // Find by session or user
        $query = Cart::query();
        if ($user) {
            $query->where('customer_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cart = $query->first();

        if (!$cart) {
            $cart = Cart::create([
                'session_id' => $sessionId,
                'customer_id' => $user ? $user->id : null,
            ]);
        }

        return $cart;
    }
}
