<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .logo h1 {
            margin: 0;
            color: #333;
        }
        .invoice-details {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .totals {
            margin-left: auto;
            width: 300px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .totals-row.grand-total {
            font-weight: bold;
            font-size: 1.2em;
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
        }
        @media print {
            body { padding: 0; }
            .invoice-box { border: none; box-shadow: none; width: 100%; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                <h1>{{ $tenant->name }}</h1>
                <p>{{ $tenant->data['address'] ?? '' }}<br>{{ $tenant->data['email'] ?? '' }}</p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p>
                    <strong>Invoice #:</strong> {{ $order->order_number }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                    <strong>Status:</strong> {{ ucfirst($order->status) }}
                </p>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
            <div>
                <strong>Bill To:</strong><br>
                {{ $order->customer->name }}<br>
                {{ $order->customer->email }}<br>
                {{ $order->customer->phone ?? '' }}
            </div>
            @if($order->shippingAddress)
            <div>
                <strong>Ship To:</strong><br>
                {{ $order->shippingAddress->address_line1 }}<br>
                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}<br>
                {{ $order->shippingAddress->country }}
            </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}
                        @if($item->variant_name)
                            <br><small>Var: {{ $item->variant_name }}</small>
                        @endif
                    </td>
                    <td style="text-align: right;">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span>{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Shipping:</span>
                <span>{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($order->shipping, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Tax:</span>
                <span>{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($order->tax, 2) }}</span>
            </div>
            <div class="totals-row grand-total">
                <span>Total:</span>
                <span>{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div class="no-print" style="margin-top: 40px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; background-color: #333; color: white; border: none; cursor: pointer; border-radius: 4px;">Print Invoice</button>
        </div>
    </div>
    <script>
        // Auto-print if opened in new window (optional, can be annoying)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
