<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::all();
        
        // Initialize default gateways if none exist
        if ($gateways->isEmpty()) {
            $this->initializeGateways();
            $gateways = PaymentGateway::all();
        }

        return view('superadmin.payment-gateways.index', compact('gateways'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $validated = $request->validate([
            'is_active' => 'boolean',
            'config' => 'array',
            'config.*' => 'nullable|string',
        ]);

        $gateway->update([
            'is_active' => $request->has('is_active'),
            'config' => $validated['config'] ?? [],
        ]);

        return redirect()->back()->with('success', "{$gateway->display_name} updated successfully.");
    }

    private function initializeGateways()
    {
        $gateways = [
            [
                'name' => 'paystack',
                'display_name' => 'Paystack',
                'is_active' => false,
                'config' => [],
            ],
            [
                'name' => 'flutterwave',
                'display_name' => 'Flutterwave',
                'is_active' => false,
                'config' => [],
            ],
            [
                'name' => 'opay',
                'display_name' => 'OPay',
                'is_active' => false,
                'config' => [],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::create($gateway);
        }
    }
}
