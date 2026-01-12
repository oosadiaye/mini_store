<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use App\Models\Account;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank',
            'bank_name' => 'nullable|required_if:type,bank|string',
            'account_number' => 'nullable|required_if:type,bank|string',
            'account_name' => 'nullable|required_if:type,bank|string',
            'gateway_provider' => 'nullable|in:opay,moniepoint,paystack,flutterwave',
            'require_gateway' => 'boolean',
        ]);

        // Auto-GL Logic for Banks
        // We want to create a sub-account or sibling in the 1020 range (Bank)
        // Let's assume we want to create distinct assets for each bank account.
        // Base is 1020.
        
        $latestBank = Account::where('account_code', 'like', '102%')
            ->whereRaw('LENGTH(account_code) = 4')
            ->orderBy('account_code', 'desc')
            ->first();
            
        $newCode = $latestBank ? (string)($latestBank->account_code + 1) : '1021';
        
        // Create the GL Account
        $account = Account::create([
            'account_code' => $newCode,
            'account_name' => $request->name . ' (' . ucfirst($request->type) . ')',
            'account_type' => 'asset', // Cash/Bank are assets
            'parent_id' => null, // Or could be child of a "Cash/Bank" header if we had one
            'description' => "Auto-created for Payment Type: " . $request->name,
            'is_active' => true,
        ]);

        // Create Payment Type
        $paymentType = PaymentType::create([
            'name' => $request->name,
            'type' => $request->type,
            'bank_details' => $request->type === 'bank' ? [
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
            ] : null,
            'gl_account_id' => $account->id,
            'gateway_provider' => $request->gateway_provider,
            'require_gateway' => $request->boolean('require_gateway'),
            'is_active' => true,
            'is_active_on_storefront' => $request->boolean('is_active_on_storefront'),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment Type created with GL Account ' . $newCode,
                'payment_type' => $paymentType->load('account')
            ]);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'payments'])->with('success', 'Payment Type created with GL Account ' . $newCode);
    }

    public function destroy(Request $request, PaymentType $paymentType)
    {
        // Optional: Check if used in transactions? 
        // For now, just delete. The GL account remains.
        $paymentType->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment Type deleted.'
            ]);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'payments'])->with('success', 'Payment Type deleted.');
    }
    
    public function toggle(Request $request, PaymentType $paymentType)
    {
        $paymentType->update(['is_active' => !$paymentType->is_active]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated.',
                'is_active' => $paymentType->is_active
            ]);
        }

        return back()->with('success', 'Status updated.');
    }

    public function toggleStorefront(Request $request, PaymentType $paymentType)
    {
        $paymentType->update(['is_active_on_storefront' => !$paymentType->is_active_on_storefront]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Storefront visibility updated.',
                'is_active_on_storefront' => $paymentType->is_active_on_storefront
            ]);
        }

        return back()->with('success', 'Storefront visibility updated.');
    }
}
