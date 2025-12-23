<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

use App\Models\PaymentType;
use App\Services\PaymentGatewayService;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index()
    {
        // Check Guest Checkout Setting
        $settings = tenant()->data ?? [];
        $guestCheckout = $settings['guest_checkout'] ?? true;

        if (!$guestCheckout && !auth('customer')->check()) {
             session()->put('url.intended', route('storefront.checkout.index'));
             return redirect()->route('storefront.login')->with('error', 'Please login to complete your purchase.');
        }

        $cart = $this->getCart();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty.');
        }

        $paymentTypes = PaymentType::where('is_active', true)->get();

        return view('storefront.checkout.index', compact('cart', 'paymentTypes'));
    }

    public function store(Request $request, PaymentGatewayService $paymentService)
    {
        $cart = $this->getCart();
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'payment_type_id' => 'required|exists:payment_types,id',
        ]);

        DB::beginTransaction();

        try {
            $paymentType = PaymentType::findOrFail($validated['payment_type_id']);

            // 1. Find or Create Customer
            $customer = Customer::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make(Str::random(16)), // Temporary password
                ]
            );

            // 2. Calculate Totals
            $cart->load('coupon'); // Ensure coupon is loaded
            
            $subtotal = $cart->items->sum(function($item) {
                return $item->price * $item->quantity;
            });
            
            $discount = 0;
            if ($cart->coupon && $cart->coupon->isValid()) {
                $discount = $cart->coupon->calculateDiscount($subtotal);
                // Increment Usage
                $cart->coupon->increment('used_count');
            }
            
            $tax = 0; // TODO: Tax calculation

            // Shipping Calculation
            $settings = tenant()->data ?? [];
            $shippingCost = (float)($settings['shipping_cost'] ?? 0);
            $freeThreshold = (float)($settings['free_shipping_threshold'] ?? 0);
            $shipping = $shippingCost;

            if ($freeThreshold > 0 && $subtotal >= $freeThreshold) {
                $shipping = 0;
            }

            $total = max(0, $subtotal - $discount + $tax + $shipping);

            // 3. Create Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount, // Save discount
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $paymentType->name, // Legacy string column
                // 'payment_type_id' => $paymentType->id, // Consider adding this column if needed
                'payment_status' => 'pending',
                // 'coupon_code' => $cart->coupon->code ?? null, // Add if column exists
            ]);

            // 4. Create Order Items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant ? $item->variant->name : null,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                // Update Stock
                if ($item->product->track_inventory) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                    
                    if ($item->product->stock_quantity <= ($item->product->low_stock_threshold ?? 5)) {
                        $admins = \App\Models\User::role(['admin', 'super-admin'])->get();
                        foreach($admins as $admin) {
                            try {
                                $admin->notify(new \App\Notifications\LowStockNotification($item->product));
                            } catch (\Exception $e) {}
                        }
                    }
                }
            }

            // 5. Create Order Shipping Address
            $order->shippingAddress()->create([
                'address_line1' => $validated['address'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'],
            ]);

            // 6. Clear Cart
            $cart->items()->delete();
            $cart->delete();

            // Notify Admins of New Order
            $admins = \App\Models\User::role(['admin', 'super-admin'])->get();
            foreach($admins as $admin) {
                 try {
                    $admin->notify(new \App\Notifications\NewOrderNotification($order));
                } catch (\Exception $e) {}
            }
            
            // Notify Customer
            try {
                $customer->notify(new \App\Notifications\CustomerOrderConfirmation($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send customer confirmation: " . $e->getMessage());
            }

            DB::commit();

            // 7. Handle Payment Logic
            if ($paymentType->require_gateway && $paymentType->gateway_provider) {
                try {
                    $initData = $paymentService->initializeTransaction($order, $paymentType->gateway_provider);
                    
                    // Save reference/provider to order?
                    $order->update([
                        'payment_transaction_id' => $initData['reference'] ?? null,
                         // Store provider temporarily?
                    ]);
                    // Using session to store provider for callback verification if needed
                    session(['checkout_order_id' => $order->id, 'checkout_provider' => $paymentType->gateway_provider]);

                    return redirect($initData['checkout_url']);
                } catch (\Exception $e) {
                    Log::error("Payment Init Failed: " . $e->getMessage());
                    // Order is created but payment failed. Redirect to pay/view order?
                    // For now, redirect back with error.
                    return redirect()->route('storefront.checkout.success', $order->order_number)->with('warning', 'Order placed but payment initialization failed. Please contact support.');
                }
            }

            return redirect()->route('storefront.checkout.success', $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error processing order: ' . $e->getMessage())->withInput();
        }
    }

    public function callback(Request $request, PaymentGatewayService $paymentService)
    {
        // Paystack sends 'reference', Flutterwave sends 'transaction_id' and 'tx_ref'
        $reference = $request->query('reference') ?? $request->query('tx_ref'); // Paystack/FW
        $transactionId = $request->query('transaction_id'); // FW
        
        $provider = session('checkout_provider'); // Retrieve stored provider
        
        if (!$reference || !$provider) {
            return redirect()->route('storefront.home')->with('error', 'Invalid payment callback.');
        }

        // For FLW, we verify using transaction_id if present, else tx_ref might work depending on implementation
        $verifyRef = ($provider === 'flutterwave' && $transactionId) ? $transactionId : $reference;

        try {
            $result = $paymentService->verifyTransaction($verifyRef, $provider);

            if ($result['success']) {
                // Find order
                // The reference we sent was order_number_timestamp. Or we can store in session.
                // Or standard: query by transaction_id in DB if we saved it. 
                // We saved reference in 'payment_transaction_id'
                
                // If FLW, we verify by transaction_id but we stored our own ref. Result should contain 'reference' which matches our 'tx_ref'.
                $originalRef = $result['reference'] ?? $reference;
                
                $order = Order::where('payment_transaction_id', $originalRef)->first();
                
                if ($order) {
                    $order->update(['payment_status' => 'paid']);
                    return redirect()->route('storefront.checkout.success', $order->order_number)->with('success', 'Payment successful!');
                }
            }
        } catch (\Exception $e) {
            Log::error("Payment Verification Failed: " . $e->getMessage());
        }

        return redirect()->route('storefront.home')->with('error', 'Payment verification failed.');
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with(['items', 'shippingAddress'])->firstOrFail();
        return view('storefront.checkout.success', compact('order'));
    }

    private function getCart()
    {
        $sessionId = Session::getId();
        return Cart::where('session_id', $sessionId)
            ->with(['items.product', 'items.variant'])
            ->first();
    }
}
