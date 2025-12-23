@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Financial Summary</h2>
            <p class="text-sm text-gray-600 mt-1">Profit margins, COGS, and revenue breakdown</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Reports
        </a>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
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
            <p class="text-3xl font-bold text-green-600">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($revenue, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">From paid orders</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-2">Cost of Goods Sold</p>
            <p class="text-3xl font-bold text-red-600">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($cogs, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Product costs</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-2">Gross Profit</p>
            <p class="text-3xl font-bold text-blue-600">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($grossProfit, 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Revenue - COGS</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600 mb-2">Gross Margin</p>
            <p class="text-3xl font-bold text-purple-600">{{ number_format($grossMargin, 1) }}%</p>
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
                        <p class="text-4xl font-bold text-green-600">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($revenue, 2) }}</p>
                    </div>
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">COGS</p>
                        <p class="text-4xl font-bold text-red-600">-{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($cogs, 2) }}</p>
                    </div>
                    <div class="pt-6 border-t-2 border-gray-300">
                        <p class="text-sm text-gray-600 mb-2">Gross Profit</p>
                        <p class="text-5xl font-bold text-blue-600">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($grossProfit, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Status Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Payment Status Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($paymentStatus as $status)
            <div class="border rounded-lg p-4 {{ $status->payment_status === 'paid' ? 'border-green-300 bg-green-50' : ($status->payment_status === 'pending' ? 'border-yellow-300 bg-yellow-50' : 'border-red-300 bg-red-50') }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700 uppercase">{{ $status->payment_status }}</span>
                    <span class="text-2xl font-bold {{ $status->payment_status === 'paid' ? 'text-green-600' : ($status->payment_status === 'pending' ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $status->count }}
                    </span>
                </div>
                <p class="text-lg font-semibold {{ $status->payment_status === 'paid' ? 'text-green-700' : ($status->payment_status === 'pending' ? 'text-yellow-700' : 'text-red-700') }}">
                    {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($status->amount, 2) }}
                </p>
            </div>
            @endforeach
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
                    @forelse($productProfits as $product)
                    @php
                        $margin = $product->revenue > 0 ? ($product->profit / $product->revenue) * 100 : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($product->units_sold) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($product->revenue, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right">
                            {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($product->cost, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                            {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($product->profit, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $margin >= 30 ? 'bg-green-100 text-green-800' : ($margin >= 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No sales data available for this period</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Profit Breakdown Chart
const profitCtx = document.getElementById('profitChart').getContext('2d');
new Chart(profitCtx, {
    type: 'doughnut',
    data: {
        labels: ['Gross Profit', 'Cost of Goods Sold'],
        datasets: [{
            data: [{{ $grossProfit }}, {{ $cogs }}],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(239, 68, 68, 0.8)',
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += '{{ tenant("data")["currency_symbol"] ?? "₦" }}' + context.parsed.toLocaleString();
                        return label;
                    }
                }
            }
        }
    }
});
</script>
@endsection
