<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::with(['products' => function($q) {
            $q->active()->with('images');
        }])->active()->get();
        
        $paymentTypes = \App\Models\PaymentType::where('is_active', true)->with('account')->get();
        $customers = \App\Models\Customer::orderBy('name')->get(); // Fetch customers
        
        $tenantData = app('tenant')->data ?? [];
        $taxRate = $tenantData['tax_rate'] ?? 0;
        $enableTax = $tenantData['enable_pos_tax'] ?? false;
        $currencySymbol = $tenantData['currency_symbol'] ?? '₦';
        
        // Fetch active tax codes
        $taxCodes = \App\Models\TaxCode::active()->get();
        
        // Logo for display
        $logoUrl = isset($tenantData['logo']) ? route('tenant.media', ['path' => $tenantData['logo']]) : null;

        return view('admin.pos.index', compact('categories', 'paymentTypes', 'customers', 'taxRate', 'enableTax', 'currencySymbol', 'logoUrl', 'taxCodes'));
    }

    public function display()
    {
        $tenantData = app('tenant')->data ?? [];
        $currencySymbol = $tenantData['currency_symbol'] ?? '₦';
        $logoUrl = isset($tenantData['logo']) ? route('tenant.media', ['path' => $tenantData['logo']]) : null;
        
        return view('admin.pos.display', compact('currencySymbol', 'logoUrl'));
    }

    public function receipt(Order $order)
    {
        $tenantData = app('tenant')->data ?? [];
        $currencySymbol = $tenantData['currency_symbol'] ?? '₦';
        $logoUrl = isset($tenantData['logo']) ? route('tenant.media', ['path' => $tenantData['logo']]) : null;

        return view('admin.pos.receipt', compact('order', 'currencySymbol', 'logoUrl', 'tenantData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_code_id' => 'nullable|exists:tax_codes,id',
            'payment_method_id' => 'required|exists:payment_types,id',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total' => 'required|numeric',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        try {
            DB::beginTransaction();
            
            $paymentType = \App\Models\PaymentType::with('account')->findOrFail($request->payment_method_id);
            
            $customerId = $request->customer_id;
            if (!$customerId) {
                // Find or create Walk-in Customer
                $guest = \App\Models\Customer::firstOrCreate(
                    ['email' => 'walkin@' . app('tenant')->id . '.local'], 
                    [
                        'name' => 'Walk-in Customer',
                        'phone' => '0000000000',
                        'password' => bcrypt(Str::random(16))
                    ]
                );
                $customerId = $guest->id;
            }

            $order = Order::create([
                'order_number' => 'POS-' . strtoupper(Str::random(8)),
                'customer_id' => $customerId,
                'status' => 'completed',
                'subtotal' => $request->subtotal,
                'tax' => $request->tax,
                'shipping' => 0,
                'discount' => $request->discount ?? 0,
                'total' => $request->total,
                'payment_method' => $paymentType->name,
                'payment_status' => 'paid',
                'order_source' => 'pos',
            ]);

            $totalTax = 0;
            $totalCost = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);
                $itemTotal = $item['quantity'] * $item['price'];
                
                // Calculate item tax
                $itemTax = 0;
                $taxCodeId = $item['tax_code_id'] ?? null;
                if ($taxCodeId) {
                    $taxCode = \App\Models\TaxCode::find($taxCodeId);
                    if ($taxCode) {
                        $itemTax = ($itemTotal * $taxCode->rate) / 100;
                    }
                }
                $totalTax += $itemTax;

                OrderItem::create([
                    'tenant_id' => app('tenant')->id,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_amount' => $itemTax,
                    'tax_code_id' => $taxCodeId,
                    'total' => $itemTotal,
                ]);
                
                // Deduct Inventory
                if ($product->manage_stock) {
                     $stock = \App\Models\WarehouseStock::firstOrCreate(
                        ['warehouse_id' => 1, 'product_id' => $product->id], // Assuming Warehouse 1 for POS
                        ['quantity' => 0]
                    );
                    $stock->decrement('quantity', $item['quantity']);
                }

                $cost = $product->cost_price ?? 0;
                $totalCost += ($cost * $item['quantity']);
            }

            // Accounting Integration
            try {
                $jeService = app(\App\Services\JournalEntryService::class);
                
                // Determine Debit Account from Payment Type
                $debitAccount = $paymentType->account->account_code ?? '1100'; // Default Cash

                // Prepare Credits
                $netRevenue = $request->subtotal; // Subtotal usually excludes tax in frontend calc, but let's be safe.
                // In this logic, subtotal is sum of (price * qty), handling tax separately.
                
                $credits = [];
                $credits[] = [ 'account_code' => '4000', 'debit' => 0, 'credit' => $netRevenue ]; // Sales Revenue

                if ($request->tax > 0) {
                     // We use the tax from request which should match calculated tax
                    $credits[] = [ 'account_code' => '2100', 'debit' => 0, 'credit' => $request->tax ]; // Sales Tax Payable
                }

                $jeService->recordTransaction("POS Sale #{$order->order_number} ({$paymentType->name})", array_merge([
                    [ 'account_code' => $debitAccount, 'debit' => $request->total, 'credit' => 0 ],
                ], $credits), now());

                // Record COGS
                if ($totalCost > 0) {
                     $jeService->recordTransaction("COGS for POS Sale #{$order->order_number}", [
                        [ 'account_code' => '5000', 'debit' => $totalCost, 'credit' => 0 ], // COGS
                        [ 'account_code' => '1200', 'debit' => 0, 'credit' => $totalCost ] // Inventory
                    ], now());
                }

            } catch (\Exception $e) {
                // Log but continue
                \Illuminate\Support\Facades\Log::error("Accounting Error POS: " . $e->getMessage());
            }

            DB::commit();

            return response()->json(['success' => true, 'order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
