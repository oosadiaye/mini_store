<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\WarehouseStock;
use App\Models\StorefrontProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedCustomer;
use App\Mail\OrderPlacedAdmin;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $paymentService;

    public function __construct(\App\Services\PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $tenant = app('tenant');
        $config = \App\Models\StoreConfig::firstOrNew(['id' => 1]); 
        
        $cart = $this->getCart();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty.');
        }

        // Determine active gateways
        $settings = $tenant->data ?? [];
        $gateways = [];
        $providers = ['paystack', 'flutterwave', 'opay', 'moniepoint'];
        foreach ($providers as $p) {
            if (!empty($settings["gateway_{$p}_active"])) {
                $gateways[] = $p;
            }
        }

        return view('storefront.checkout.index', compact('cart', 'config', 'gateways'));
    }

    public function store(Request $request)
    {
        $tenant = app('tenant');
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address_line_1' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'payment_method' => 'required|string', // cod, paystack, flutterwave, etc.
        ]);

        $cart = $this->getCart();
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('storefront.cart.index')->with('error', 'Cart is empty');
        }

        DB::beginTransaction();
        try {
            // 1. Create/Find Customer
            $customer = Customer::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'phone' => $request->phone ?? null,
                    'password' => bcrypt(Str::random(16)), 
                ]
            );
            
            // 2. Create Order
            $discountAmount = $cart->discount_amount;
            $couponId = $cart->coupon_id;

            $order = Order::create([
                'tenant_id' => $tenant->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id' => $customer->id,
                'total' => $cart->total, 
                'subtotal' => $cart->subtotal,
                'discount' => $discountAmount,
                'shipping' => $cart->shipping_cost,
                'status' => 'pending', 
                'payment_status' => 'unpaid',
                'payment_method' => $request->payment_method,
            ]);

            // Increment Coupon Usage
            if ($couponId) {
                \App\Models\Coupon::where('id', $couponId)->increment('used_count');
            }

            // Create Shipping Record
            $order->shippingAddress()->create([
                'address_line1' => $request->address_line_1, 
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ]);

            // 3. Create Order Items & Decrement Stock
            $warehouse = \App\Models\Warehouse::first(); 
            
            foreach ($cart->items as $item) {
                $product = StorefrontProduct::lockForUpdate()->find($item->product_id);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $product->name, 
                    'quantity' => $item->quantity,
                    'price' => $item->price, 
                    'total' => $item->line_total, 
                    'tax_amount' => 0,
                ]);

                if ($product->track_inventory) {
                    $warehouseId = $warehouse ? $warehouse->id : 1;
                    $product->recordMovement($warehouseId, -$item->quantity, 'sale', 'order', $order->id);
                }
            }

            // 4. Clear Cart
            $cart->items()->delete();
            $cart->delete(); 

            DB::commit();

            // 5. Handle Payment
            if ($request->payment_method === 'cod') {
                $this->sendOrderEmails($order);
                return redirect()->route('storefront.checkout.success', ['order' => $order->order_number]);
            } else {
                // Initialize Online Payment
                try {
                    $initParams = $this->paymentService->initializeTransaction($order, $request->payment_method);
                    if ($initParams['success']) {
                        return redirect($initParams['checkout_url']);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Payment Init Failed: ' . $e->getMessage());
                    // Decide: Cancel order or redirect to success with "Unpaid" status? 
                    // Let's redirect to success but show warning, or back to cart? keeping order valid but unpaid.
                    return redirect()->route('storefront.checkout.success', ['order' => $order->order_number])->with('error', 'Payment initialization failed, please try paying from your order history or contact support.');
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        $provider = $request->query('provider'); // We appended this
        // Some gateways put reference in different keys
        $reference = $request->except(['provider']); 

        // Normalized reference extraction
        $refId = $request->reference ?? $request->tx_ref ?? $request->transaction_id ?? null;

        if (!$provider || !$refId) {
             return redirect()->route('storefront.home')->with('error', 'Invalid payment callback.');
        }

        try {
            $verify = $this->paymentService->verifyTransaction($refId, $provider);
            
            if ($verify['success']) {
                // Find Order by Reference logic 
                // We constructed reference as: order_number . '_' . time
                // We can't query by reference directly unless we saved it. 
                // But we can parse it.
                // Assuming format: ORDER-XXX_TIMESTAMP
                $parts = explode('_', $verify['reference']);
                $orderNumber = $parts[0];
                
                $order = Order::where('order_number', $orderNumber)->first();
                if ($order) {
                    $order->update([
                        'payment_status' => 'paid',
                        'transaction_id' => $verify['reference'], // Save gateway ref
                    ]);
                    
                    $this->sendOrderEmails($order);
                    
                    // Record Payment in Accounting?
                    // app(\App\Services\AccountingService::class)->recordPayment(...) // Optimized for later
                    
                    return redirect()->route('storefront.checkout.success', ['order' => $order->order_number])->with('success', 'Payment successful!');
                }
            }
            
            // If failed verification
            return redirect()->route('storefront.home')->with('error', 'Payment verification failed.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment Verify Failed: ' . $e->getMessage());
            return redirect()->route('storefront.home')->with('error', 'Payment verification error.');
        }
    }

    public function success(Request $request)
    {
        $orderNumber = $request->query('order');
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        
        return view('storefront.checkout.success', compact('order'));
    }

    private function sendOrderEmails($order)
    {
        try {
            $tenant = app('tenant');
            // Notify Customer
            Mail::to($order->customer->email)->send(new OrderPlacedCustomer($order, $tenant));
            
            // Notify Admin (Tenant Email)
            Mail::to($tenant->email)->send(new OrderPlacedAdmin($order, $tenant));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order emails failed: ' . $e->getMessage());
        }
    }

    private function getCart()
    {
        // Reusing helper logic or inject service
        $sessionId = Session::getId();
        $user = auth()->user();
        $query = Cart::query();
        if ($user) {
            $query->where('customer_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }
        return $query->first();
    }
}
