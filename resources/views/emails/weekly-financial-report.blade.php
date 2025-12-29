<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        body {
            background-color: #f6f9fc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            color: #334155;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); /* Indigo to Blue */
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 24px;
            color: #1e293b;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 24px;
            border-spacing: 10px 0;
        }
        .metric-cell {
            display: table-cell;
            width: 33.33%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
            padding: 16px;
            vertical-align: middle;
        }
        .metric-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 4px;
            display: block;
        }
        .metric-value {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            display: block;
        }
        .metric-value.positive { color: #10b981; } /* Emerald */
        .metric-value.negative { color: #ef4444; } /* Red */
        
        .top-product-section {
            border-top: 1px solid #e2e8f0;
            padding-top: 24px;
            margin-top: 24px;
        }
        .section-title {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .product-row {
            display: table;
            width: 100%;
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 12px;
        }
        .product-info {
            display: table-cell;
            vertical-align: middle;
        }
        .product-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 15px;
        }
        .product-stat {
            font-size: 13px;
            color: #64748b;
        }
        .cta-button {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #4f46e5;
            color: #ffffff;
            text-decoration: none;
            padding: 14px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 32px;
            transition: background-color 0.2s;
        }
        .cta-button:hover {
            background-color: #4338ca;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Weekly Financial Snapshot</h1>
                <p>{{ $tenantName }}</p>
                <p style="font-size: 12px; margin-top: 4px; opacity: 0.7;">{{ $startDate }} - {{ $endDate }}</p>
            </div>
            
            <div class="content">
                <div class="greeting">
                    Hello Business Owner, taking a quick look at your week!
                </div>

                <div class="metrics-grid">
                    <div class="metric-cell">
                        <span class="metric-label">Revenue</span>
                        <span class="metric-value">{{ $totalRevenue }}</span>
                        <!-- <span style="font-size: 10px; color: #10b981;">▲ 12%</span> -->
                    </div>
                    <div class="metric-cell">
                        <span class="metric-label">Expenses</span>
                        <span class="metric-value">{{ $totalExpenses }}</span>
                    </div>
                    <div class="metric-cell">
                        <span class="metric-label">Net Profit</span>
                        <span class="metric-value {{ (floatval(preg_replace('/[^\d.-]/', '', $netProfit)) >= 0) ? 'positive' : 'negative' }}">
                            {{ $netProfit }}
                        </span>
                    </div>
                </div>

                @if($topProduct)
                <div class="top-product-section">
                    <div class="section-title">🏆 Top Performing Product</div>
                    <div class="product-row">
                        <div class="product-info">
                            <div class="product-name">{{ $topProduct['name'] }}</div>
                            <div class="product-stat">{{ $topProduct['sold_count'] }} units sold this week</div>
                        </div>
                        <div style="display: table-cell; vertical-align: middle; text-align: right;">
                             <span style="font-weight: bold; color: #4f46e5;">{{ $topProduct['revenue'] }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <a href="{{ route('admin.dashboard', ['tenant' => $tenantSlug]) }}" class="cta-button">View Detailed Analysis</a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>You received this email because you are the owner of {{ $tenantName }}.</p>
        </div>
    </div>
</body>
</html>
