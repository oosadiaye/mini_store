<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 150px;
            float: left;
        }
        .company-info {
            float: right;
            text-align: right;
        }
        .invoice-details {
            clear: both;
            margin-bottom: 30px;
        }
        .invoice-details .left {
            float: left;
            width: 50%;
        }
        .invoice-details .right {
            float: right;
            width: 50%;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
            text-align: left;
            padding: 12px;
            font-weight: bold;
        }
        td {
            border-bottom: 1px solid #eee;
            padding: 12px;
        }
        .totals {
            float: right;
            width: 40%;
        }
        .totals table th {
            text-align: right;
            background: none;
            border: none;
            padding: 5px;
        }
        .totals table td {
            text-align: right;
            border: none;
            padding: 5px;
        }
        .total-row {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #333 !important;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #e6fffa; color: #047481; border: 1px solid #b2f5ea; }
        .status-pending { background-color: #fffaf0; color: #9c4221; border: 1px solid #feebc8; }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <div class="logo">
                @if(tenant('logo'))
                     <img src="{{ tenant('logo') }}" alt="{{ tenant('id') }}" style="max-height: 50px;">
                @else
                    <h1 style="margin:0;">{{ tenant('id') }}</h1>
                @endif
            </div>
            <div class="company-info">
                <strong>{{ tenant('company_name') ?? tenant('id') }}</strong><br>
                {{ tenant('data')['company_address'] ?? '' }}<br>
                {{ tenant('data')['company_email'] ?? '' }}<br>
                {{ tenant('data')['company_phone'] ?? '' }}
            </div>
        </div>

        <div class="invoice-details clearfix">
            <div class="left">
                <h3>Bill To:</h3>
                <strong>{{ $order->customer->name ?? 'Guest' }}</strong><br>
                {{ $order->shippingAddress->address_line_1 ?? '' }}<br>
                {{ $order->shippingAddress->city ?? '' }}, {{ $order->shippingAddress->state ?? '' }} {{ $order->shippingAddress->postal_code ?? '' }}<br>
                {{ $order->customer->email ?? $order->guest_email }}
            </div>
            <div class="right">
                <h3>Invoice #{{ $order->order_number }}</h3>
                <p>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                    <strong>Status:</strong> <span class="status-badge status-{{ $order->payment_status }}">{{ $order->payment_status }}</span>
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="width: 100px; text-align: center;">Qty</th>
                    <th style="width: 150px; text-align: right;">Price</th>
                    <th style="width: 150px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->variant)
                            <br><small class="text-gray-500">{{ $item->variant->name }}</small>
                        @endif
                        <br><small>SKU: {{ $item->variant ? $item->variant->sku : $item->product->sku }}</small>
                    </td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">{{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals">
                <table>
                    <tr>
                        <th>Subtotal:</th>
                        <td>{{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <th>Discount:</th>
                        <td>-{{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($order->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Tax:</th>
                        <td>{{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                     <tr>
                        <th>Shipping:</th>
                        <td>{{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($order->shipping_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="total-row">
                            {{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($order->total, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            @if(isset(tenant('data')['invoice_footer']))
                <p>{{ tenant('data')['invoice_footer'] }}</p>
            @endif
        </div>
    </div>
</body>
</html>
