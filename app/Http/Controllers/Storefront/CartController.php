<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        return view('storefront.cart', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);

        $cart = $this->getOrCreateCart();

        // Check if item already exists
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $price = $product->price;
            if(!empty($validated['variant_id'])) {
                $variant = \App\Models\ProductVariant::find($validated['variant_id']);
                if($variant) {
                    $price = $variant->price;
                }
            }

            $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $validated['variant_id'] ?? null,
                'quantity' => $validated['quantity'],
                'price' => $price,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $cart->items()->sum('quantity'),
        ]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->update(['quantity' => $validated['quantity']]);
        
        // Refresh cart to get totals (handled by model accessors)
        $cart = $cartItem->cart;

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'line_total' => number_format($cartItem->price * $cartItem->quantity, 2),
            'cart_subtotal' => number_format($cart->subtotal, 2),
            'cart_discount' => number_format($cart->discount_amount, 2),
            'cart_total' => number_format($cart->total, 2), 
            'cart_count' => $cart->total_items,
        ]);
    }

    public function remove(CartItem $cartItem)
    {
        $cart = $cartItem->cart;
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_subtotal' => number_format($cart->subtotal, 2),
            'cart_discount' => number_format($cart->discount_amount, 2),
            'cart_total' => number_format($cart->total, 2),
            'cart_count' => $cart->total_items,
        ]);
    }

    public function clear()
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()->route('storefront.cart')->with('success', 'Cart cleared');
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper($validated['code']);
        $coupon = \App\Models\Coupon::where('code', $code)->first();

        if (!$coupon) {
            return back()->withErrors(['coupon' => 'Invalid coupon code.']);
        }

        if (!$coupon->isValid()) {
             return back()->withErrors(['coupon' => 'This coupon is invalid or expired.']);
        }

        $cart = $this->getOrCreateCart();
        
        // Check minimum spend
        $subtotal = $cart->subtotal; // Use the accessor
        if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
             return back()->withErrors(['coupon' => 'Minimum spend of $' . number_format($coupon->min_spend, 2) . ' required.']);
        }

        $cart->update(['coupon_id' => $coupon->id]);
        $coupon->increment('used_count'); // Increment usage count immediately or at checkout? Usually at checkout. 
        // For now, let's just associate it. Incrementing usage should be at checkout really.
        // But to track "reserved" coupons, maybe. Let's stick to checkout for incrementing.

        return back()->with('success', 'Coupon applied successfully!');
    }

    public function removeCoupon()
    {
        $cart = $this->getCart();
        if ($cart) {
            $cart->update(['coupon_id' => null]);
        }
        return back()->with('success', 'Coupon removed.');
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $cart = $this->getOrCreateCart();
        $cart->update(['customer_email' => $validated['email']]);

        return response()->json(['success' => true]);
    }

    private function getOrCreateCart()
    {
        $sessionId = Session::getId();
        
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['session_id' => $sessionId]
        );
    }

    private function getCart()
    {
        $sessionId = Session::getId();
        return Cart::where('session_id', $sessionId)
            ->with(['items.product.images', 'items.variant', 'coupon'])
            ->first();
    }
}
