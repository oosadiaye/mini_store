@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto" x-data="salesReport()">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sales Analytics</h2>
            <p class="text-sm text-gray-600 mt-1">Comprehensive sales performance and revenue insights <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse" x-show="loading">Updating...</span></p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Reports
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Warehouse</label>
                <select name="warehouse_id" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ $warehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" class="w-full px-3 py-2 border-2 border-gray-300 rounded-md text-sm">
                    <option value="">All Customers</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ $customerId == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md transition text-sm">
                    Filter
                </button>
                <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate, 'warehouse_id' => $warehouseId, 'category_id' => $categoryId, 'customer_id' => $customerId]) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md transition text-sm" title="Export CSV">
                    <i class="fas fa-download text-sm"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" x-text="formatNumber(totalOrders)">{{ number_format($totalOrders) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(totalRevenue, 2)">{{ number_format($totalRevenue, 2) }}</span>
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg Order Value</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">
                        <span x-text="currencySymbol">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span><span x-text="formatNumber(averageOrderValue, 2)">{{ number_format($averageOrderValue, 2) }}</span>
                    </p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New Customers</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2" x-text="formatNumber(newCustomers)">{{ number_format($newCustomers) }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-lg">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Trend Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Sales Trend</h3>
        <canvas id="salesTrendChart" height="80"></canvas>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Category Revenue Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Revenue by Category</h3>
            <canvas id="categoryChart" height="200"></canvas>
        </div>

        <!-- Payment Methods Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Sales by Payment Method</h3>
            <canvas id="paymentChart" height="200"></canvas>
        </div>
    </div>

    <!-- Top Selling Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Top Selling Products</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="product in topProducts" :key="product.sku">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="product.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="product.sku"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right" x-text="formatNumber(product.total_quantity)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                <span x-text="currencySymbol"></span><span x-text="formatNumber(product.total_revenue, 2)"></span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="topProducts.length === 0">
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No sales data available for this period</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function salesReport() {
    return {
        loading: false,
        totalOrders: {{ $totalOrders }},
        totalRevenue: {{ $totalRevenue }},
        averageOrderValue: {{ $averageOrderValue }},
        newCustomers: {{ $newCustomers }},
        topProducts: {!! json_encode($topProducts) !!},
        currencySymbol: '{{ $tenant->data["currency_symbol"] ?? "₦" }}',
        charts: {},

        init() {
            this.initCharts();
            // Polling every 30 seconds
            setInterval(() => this.fetchData(), 30000);
        },

        initCharts() {
            // Sales Trend Chart
            const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
            this.charts.sales = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($salesData->pluck('date')) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($salesData->pluck('revenue')) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Orders',
                        data: {!! json_encode($salesData->pluck('orders')) !!},
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        y: {
                            type: 'linear', display: true, position: 'left',
                            title: { display: true, text: 'Revenue (' + this.currencySymbol + ')' }
                        },
                        y1: {
                            type: 'linear', display: true, position: 'right',
                            title: { display: true, text: 'Orders' },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });

            // Category Revenue Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            this.charts.category = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryRevenue->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($categoryRevenue->pluck('revenue')) !!},
                        backgroundColor: ['rgb(59, 130, 246)', 'rgb(16, 185, 129)', 'rgb(249, 115, 22)', 'rgb(139, 92, 246)', 'rgb(236, 72, 153)', 'rgb(14, 165, 233)']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });

            // Payment Methods Chart
            const paymentCtx = document.getElementById('paymentChart').getContext('2d');
            this.charts.payment = new Chart(paymentCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($paymentMethods->pluck('name')) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($paymentMethods->pluck('revenue')) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Revenue (' + this.currencySymbol + ')' }
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
                
                this.totalOrders = data.totalOrders;
                this.totalRevenue = data.totalRevenue;
                this.averageOrderValue = data.averageOrderValue;
                this.newCustomers = data.newCustomers;
                this.topProducts = data.topProducts;

                // Update Charts
                this.updateSalesChart(data.salesData);
                this.updateCategoryChart(data.categoryRevenue);
                this.updatePaymentChart(data.paymentMethods);

            } catch (error) {
                console.error('Failed to fetch real-time data:', error);
            } finally {
                this.loading = false;
            }
        },

        updateSalesChart(data) {
            this.charts.sales.data.labels = data.map(i => i.date);
            this.charts.sales.data.datasets[0].data = data.map(i => i.revenue);
            this.charts.sales.data.datasets[1].data = data.map(i => i.orders);
            this.charts.sales.update();
        },

        updateCategoryChart(data) {
            this.charts.category.data.labels = data.map(i => i.name);
            this.charts.category.data.datasets[0].data = data.map(i => i.revenue);
            this.charts.category.update();
        },

        updatePaymentChart(data) {
            this.charts.payment.data.labels = data.map(i => i.name);
            this.charts.payment.data.datasets[0].data = data.map(i => i.revenue);
            this.charts.payment.update();
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
