@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Customer Analytics</h2>
            <p class="text-sm text-gray-600 mt-1">Customer behavior, lifetime value, and segmentation insights</p>
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

    <!-- Customer Segments -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">New Customers</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($segments['new']) }}</p>
            <p class="text-sm opacity-80 mt-1">Last 30 days</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Active Customers</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($segments['active']) }}</p>
            <p class="text-sm opacity-80 mt-1">Purchased in last 90 days</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium opacity-90">Inactive Customers</h3>
                <svg class="w-8 h-8 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-4xl font-bold">{{ number_format($segments['inactive']) }}</p>
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
                    @forelse($topCustomers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 font-semibold">{{ substr($customer->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $customer->order_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                            {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($customer->total_spent, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                            {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($customer->avg_order_value, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No customer data available for this period</td>
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
// Customer Growth Chart
const growthCtx = document.getElementById('customerGrowthChart').getContext('2d');
new Chart(growthCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($customerGrowth->pluck('date')) !!},
        datasets: [{
            label: 'New Customers',
            data: {!! json_encode($customerGrowth->pluck('new_customers')) !!},
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Frequency Distribution Chart
const freqCtx = document.getElementById('frequencyChart').getContext('2d');
new Chart(freqCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($frequencyDistribution->pluck('frequency_range')) !!},
        datasets: [{
            label: 'Customers',
            data: {!! json_encode($frequencyDistribution->pluck('customer_count')) !!},
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
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection
