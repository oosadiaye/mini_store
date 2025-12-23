<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function show(Order $order)
    {
        // Security Check
        $user = Auth::guard('customer')->user();
        $admin = Auth::user(); // Admin guard

        if (!$admin && (!$user || $order->customer_id !== $user->id)) {
            abort(403);
        }

        $order->load(['items.product', 'items.variant', 'customer', 'shippingAddress']);

        // Generate PDF
        $pdf = Pdf::loadView('invoices.pdf', compact('order'));

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
