@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto" x-data="financialReport()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Financial Summary</h2>
            <p class="text-sm text-gray-600 mt-1">Profit margins, COGS, and revenue breakdown <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse" x-show="loading">Updating...</span></p>
        </div>
        <a href="{{ route('admin.reports.index', ['tenant' => $tenant->slug]) }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Reports
        </a>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md transition">
                Apply Filter
            </button>
        </form>
    </div>

    <!-- Key Financial Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-2">Total Revenue</p>
            <p class="text-3xl font-bold text-green-600">
                <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(revenue, 2)">{{ number_format($revenue, 2) }}</span>
            </p>
            <p class="text-xs text-gray-500 mt-1">From paid orders</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-2">Cost of Goods Sold</p>
            <p class="text-3xl font-bold text-red-600">
                <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(cogs, 2)">{{ number_format($cogs, 2) }}</span>
            </p>
            <p class="text-xs text-gray-500 mt-1">Product costs</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-2">Gross Profit</p>
            <p class="text-3xl font-bold text-blue-600">
                <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(grossProfit, 2)">{{ number_format($grossProfit, 2) }}</span>
            </p>
            <p class="text-xs text-gray-500 mt-1">Revenue - COGS</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-2">Gross Margin</p>
            <p class="text-3xl font-bold text-purple-600"><span x-text="formatNumber(grossMargin, 1)">{{ number_format($grossMargin, 1) }}</span>%</p>
            <p class="text-xs text-gray-500 mt-1">Profit percentage</p>
        </div>
    </div>

    <!-- Profit Breakdown Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Revenue vs Cost Breakdown</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <canvas id="profitChart" height="200"></canvas>
            </div>
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">Revenue</p>
                        <p class="text-4xl font-bold text-green-600">
                            <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(revenue, 2)">{{ number_format($revenue, 2) }}</span>
                        </p>
                    </div>
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">COGS</p>
                        <p class="text-4xl font-bold text-red-600">-<span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(cogs, 2)">{{ number_format($cogs, 2) }}</span></p>
                    </div>
                    <div class="pt-6 border-t-2 border-gray-300">
                        <p class="text-sm text-gray-600 mb-2">Gross Profit</p>
                        <p class="text-5xl font-bold text-blue-600">
                            <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(grossProfit, 2)">{{ number_format($grossProfit, 2) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Payment Status Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <template x-for="status in paymentStatus" :key="status.payment_status">
                <div class="border rounded-lg p-4" 
                    :class="{
                        'border-green-300 bg-green-50': status.payment_status === 'paid',
                        'border-yellow-300 bg-yellow-50': status.payment_status === 'pending',
                        'border-red-300 bg-red-50': status.payment_status !== 'paid' && status.payment_status !== 'pending'
                    }">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 uppercase" x-text="status.payment_status"></span>
                        <span class="text-2xl font-bold" 
                            :class="{
                                'text-green-600': status.payment_status === 'paid',
                                'text-yellow-600': status.payment_status === 'pending',
                                'text-red-600': status.payment_status !== 'paid' && status.payment_status !== 'pending'
                            }" x-text="status.count"></span>
                    </div>
                    <p class="text-lg font-semibold" 
                        :class="{
                            'text-green-700': status.payment_status === 'paid',
                            'text-yellow-700': status.payment_status === 'pending',
                            'text-red-700': status.payment_status !== 'paid' && status.payment_status !== 'pending'
                        }">
                        <span x-text="currencySymbol"></span><span x-text="formatNumber(status.amount, 2)"></span>
                    </p>
                </div>
            </template>
        </div>
    </div>

    <!-- Product Profitability Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Product Profitability Analysis</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Margin %</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="product in productProfits" :key="product.sku">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="product.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="product.sku"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right" x-text="formatNumber(product.units_sold)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                <span x-text="currencySymbol"></span><span x-text="formatNumber(product.revenue, 2)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right">
                                <span x-text="currencySymbol"></span><span x-text="formatNumber(product.cost, 2)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                <span x-text="currencySymbol"></span><span x-text="formatNumber(product.profit, 2)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full" 
                                    :class="getMarginClass(product.margin)" 
                                    x-text="formatNumber(product.margin, 1) + '%'"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="productProfits.length === 0">
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No sales data available for this period</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function financialReport() {
    return {
        loading: false,
        revenue: {{ $revenue }},
        cogs: {{ $cogs }},
        grossProfit: {{ $grossProfit }},
        grossMargin: {{ $grossMargin }},
        paymentStatus: {!! json_encode($paymentStatus) !!},
        productProfits: {!! json_encode($productProfits->map(function($p) { 
            $p->margin = $p->revenue > 0 ? ($p->profit / $p->revenue) * 100 : 0;
            return $p;
        })) !!},
        currencySymbol: '{{ $tenant->data["currency_symbol"] ?? "₦" }}',
        chart: null,

        init() {
            this.initChart();
            setInterval(() => this.fetchData(), 60000);
        },

        initChart() {
            const ctx = document.getElementById('profitChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Gross Profit', 'Cost of Goods Sold'],
                    datasets: [{
                        data: [this.grossProfit, this.cogs],
                        backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(239, 68, 68, 0.8)'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    return (context.label || '') + ': ' + this.currencySymbol + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        },

        async fetchData() {
            if (this.loading) return;
            this.loading = true;
            try {
                const url = new URL(window.location.href);
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                
                this.revenue = data.revenue;
                this.cogs = data.cogs;
                this.grossProfit = data.grossProfit;
                this.grossMargin = data.grossMargin;
                this.paymentStatus = data.paymentStatus;
                this.productProfits = data.productProfits.map(p => {
                    p.margin = p.revenue > 0 ? (p.profit / p.revenue) * 100 : 0;
                    return p;
                });

                this.chart.data.datasets[0].data = [this.grossProfit, this.cogs];
                this.chart.update();

            } catch (error) {
                console.error('Failed to fetch real-time data:', error);
            } finally {
                this.loading = false;
            }
        },

        formatNumber(num, decimals = 0) {
            return Number(num).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },

        getMarginClass(margin) {
            if (margin >= 30) return 'bg-green-100 text-green-800';
            if (margin >= 15) return 'bg-yellow-100 text-yellow-800';
            return 'bg-red-100 text-red-800';
        }
    };
}
</script>
@endsection
