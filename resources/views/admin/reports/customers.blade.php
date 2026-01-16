@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto" x-data="customerReport()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Customer Analytics</h2>
            <p class="text-sm text-gray-600 mt-1">Customer behavior, lifetime value, and segmentation insights <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse" x-show="loading">Updating...</span></p>
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

    <!-- Customer Segments -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">New Customers</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <p class="text-4xl font-bold" x-text="formatNumber(segments.new)">{{ number_format($segments['new']) }}</p>
            <p class="text-sm opacity-80 mt-1">Last 30 days</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Active Customers</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-4xl font-bold" x-text="formatNumber(segments.active)">{{ number_format($segments['active']) }}</p>
            <p class="text-sm opacity-80 mt-1">Purchased in last 90 days</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Inactive Customers</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-4xl font-bold" x-text="formatNumber(segments.inactive)">{{ number_format($segments['inactive']) }}</p>
            <p class="text-sm opacity-80 mt-1">No purchase in 90+ days</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Customer Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Customer Acquisition Trend</h3>
            <canvas id="customerGrowthChart" height="200"></canvas>
        </div>

        <!-- Purchase Frequency Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Purchase Frequency Distribution</h3>
            <canvas id="frequencyChart" height="200"></canvas>
        </div>
    </div>

    <!-- Top Customers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Top Customers by Revenue</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Order Value</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="customer in topCustomers" :key="customer.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold" x-text="customer.name.substr(0, 1)"></span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900" x-text="customer.name"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="customer.email"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right" x-text="customer.order_count"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                <span x-text="currencySymbol"></span><span x-text="formatNumber(customer.total_spent, 2)"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                <span x-text="currencySymbol"></span><span x-text="formatNumber(customer.avg_order_value, 2)"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="topCustomers.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No customer data available for this period</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function customerReport() {
    return {
        loading: false,
        segments: {!! json_encode($segments) !!},
        topCustomers: {!! json_encode($topCustomers) !!},
        customerGrowth: {!! json_encode($customerGrowth) !!},
        frequencyDistribution: {!! json_encode($frequencyDistribution) !!},
        currencySymbol: '{{ $tenant->data["currency_symbol"] ?? "₦" }}',
        growthChart: null,
        freqChart: null,

        init() {
            this.initCharts();
            setInterval(() => this.fetchData(), 60000);
        },

        initCharts() {
            // Growth Chart
            const growthCtx = document.getElementById('customerGrowthChart').getContext('2d');
            this.growthChart = new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: this.customerGrowth.map(d => d.date),
                    datasets: [{
                        label: 'New Customers',
                        data: this.customerGrowth.map(d => d.new_customers),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });

            // Frequency Chart
            const freqCtx = document.getElementById('frequencyChart').getContext('2d');
            this.freqChart = new Chart(freqCtx, {
                type: 'bar',
                data: {
                    labels: this.frequencyDistribution.map(d => d.frequency_range),
                    datasets: [{
                        label: 'Customers',
                        data: this.frequencyDistribution.map(d => d.customer_count),
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                            'rgba(234, 179, 8, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
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
                
                this.segments = data.segments;
                this.topCustomers = data.topCustomers;
                this.customerGrowth = data.customerGrowth;
                this.frequencyDistribution = data.frequencyDistribution;

                // Update Growth Chart
                this.growthChart.data.labels = this.customerGrowth.map(d => d.date);
                this.growthChart.data.datasets[0].data = this.customerGrowth.map(d => d.new_customers);
                this.growthChart.update();

                // Update Frequency Chart
                this.freqChart.data.labels = this.frequencyDistribution.map(d => d.frequency_range);
                this.freqChart.data.datasets[0].data = this.frequencyDistribution.map(d => d.customer_count);
                this.freqChart.update();

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
        }
    };
}
</script>
@endsection
