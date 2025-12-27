<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 10px;
            width: 80mm; /* Standard Thermal Width */
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 8px; }
        .border-b { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 5px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 2px 0; }
        .total-row { font-size: 14px; font-weight: bold; }
        
        @media print {
            body { width: 100%; margin: 0; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="text-center mb-2">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" style="max-width: 50%; max-height: 50px; filter: grayscale(100%);">
        @else
            <h2 style="margin:0">{{ $tenant->name }}</h2>
        @endif
        <div>{{ $tenantData['address'] ?? '' }}</div>
        <div>{{ $tenantData['phone'] ?? '' }}</div>
    </div>

    <div class="border-b">
        <div>Order: {{ $order->order_number }}</div>
        <div>Date: {{ $order->created_at->format('Y-m-d H:i') }}</div>
        <div>Cashier: {{ auth()->user()->name ?? 'Admin' }}</div>
    </div>

    <table class="mb-2">
        <thead>
            <tr class="border-b">
                <th>Item</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td colspan="4">{{ $item->product_name }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-b">
        <table>
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->tax > 0)
            <tr>
                <td>Tax</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($order->tax, 2) }}</td>
            </tr>
            @endif
            @if($order->discount > 0)
            <tr>
                <td>Discount</td>
                <td class="text-right">-{{ $currencySymbol }}{{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($order->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="text-center" style="margin-top: 20px;">
        <p>Thank you for shopping!</p>
        <p class="no-print" style="margin-top: 20px; font-size: 10px; color: gray;">
            (Window should print automatically)
        </p>
    </div>

</body>
</html>
