@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto" x-data="movementReport()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Inventory Movement Dashboard</h2>
            <p class="text-sm text-gray-600 mt-1">Track every stock in and out across all warehouses <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse" x-show="loading">Updating...</span></p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Reports
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Warehouse</label>
                <select name="customer_id" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
                    <option value="">All Customers</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ ($customerId ?? 0) == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition text-sm">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-600">Total Stock In</p>
            <p class="text-3xl font-bold text-green-600 mt-2" x-text="'+' + formatNumber(stats.total_in)">+{{ number_format($stats['total_in']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Units added to inventory</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <p class="text-sm font-medium text-gray-600">Total Stock Out</p>
            <p class="text-3xl font-bold text-red-600 mt-2" x-text="'-' + formatNumber(stats.total_out)">-{{ number_format($stats['total_out']) }}</p>
            <p class="text-xs text-gray-400 mt-1">Units removed from inventory</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm font-medium text-gray-600">Net Movement</p>
            <p class="text-3xl font-bold mt-2" :class="(stats.total_in - stats.total_out) >= 0 ? 'text-blue-600' : 'text-orange-600'" x-text="formatNumber(stats.total_in - stats.total_out)">
                {{ number_format($stats['total_in'] - $stats['total_out']) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Net change in stock units</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Movement by Type</h3>
            <canvas id="typeChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Stock Velocity</h3>
            <p class="text-sm text-gray-500">Breakdown of movement intensity by type</p>
            <div class="mt-4 space-y-4">
                <template x-for="item in stats.by_type" :key="item.type">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 capitalize" x-text="item.type"></span>
                            <span class="text-sm font-bold text-gray-900" x-text="formatNumber(item.total) + ' units'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" :style="'width: ' + ((stats.total_in + stats.total_out) > 0 ? (item.total / (stats.total_in + stats.total_out)) * 100 : 0) + '%'"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Movement Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Movement History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Warehouse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="m in movementsData" :key="m.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(m.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900" x-text="m.product ? m.product.name : 'Deleted Product'"></div>
                                <div class="text-xs text-gray-500" x-text="m.product ? m.product.sku : '-'"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="m.warehouse ? m.warehouse.name : '-'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize" 
                                    :class="{
                                        'bg-green-100 text-green-800': m.type === 'purchase',
                                        'bg-blue-100 text-blue-800': m.type === 'sale',
                                        'bg-yellow-100 text-yellow-800': m.type === 'adjustment',
                                        'bg-purple-100 text-purple-800': m.type === 'transfer',
                                        'bg-gray-100 text-gray-800': !['purchase', 'sale', 'adjustment', 'transfer'].includes(m.type)
                                    }" x-text="m.type"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right" 
                                :class="m.quantity > 0 ? 'text-green-600' : 'text-red-600'" 
                                x-text="(m.quantity > 0 ? '+' : '') + m.quantity"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right" x-text="formatNumber(m.balance_after)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <template x-if="m.reference_type && m.reference_id">
                                    <span>
                                        <span class="capitalize" x-text="m.reference_type.replace(/_/g, ' ')"></span> #<span x-text="m.reference_id"></span>
                                    </span>
                                </template>
                                <template x-if="!(m.reference_type && m.reference_id)">
                                    <span>-</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="movementsData.length === 0">
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No movements found for the selected criteria.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4" x-show="!loading">
            {{ $movements->links() }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function movementReport() {
    return {
        loading: false,
        stats: {!! json_encode($stats) !!},
        movementsData: {!! json_encode($movements->items()) !!},
        chart: null,

        init() {
            this.initChart();
            setInterval(() => this.fetchData(), 45000);
        },

        initChart() {
            const ctx = document.getElementById('typeChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: this.stats.by_type.map(i => i.type),
                    datasets: [{
                        data: this.stats.by_type.map(i => i.total),
                        backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EF4444', '#6B7280']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
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
                
                this.stats = data.stats;
                if (data.movements && data.movements.data) {
                    this.movementsData = data.movements.data;
                }

                this.chart.data.labels = this.stats.by_type.map(i => i.type);
                this.chart.data.datasets[0].data = this.stats.by_type.map(i => i.total);
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

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    };
}
</script>
@endsection
