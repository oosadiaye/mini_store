@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Reports & Analytics</h2>
        <p class="text-gray-600 mt-2">Comprehensive business insights and performance metrics</p>
    </div>

    <!-- Report Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Sales Report -->
        <a href="{{ route('admin.reports.sales') }}" class="group">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Sales Analytics</h3>
                <p class="text-blue-100 text-sm">Revenue trends, top products, and sales performance</p>
            </div>
        </a>

        <!-- Inventory Report -->
        <a href="{{ route('admin.reports.inventory') }}" class="group">
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Inventory Reports</h3>
                <p class="text-green-100 text-sm">Stock levels, valuations, and movement tracking</p>
            </div>
        </a>

        <!-- Customer Analytics -->
        <a href="{{ route('admin.reports.customers') }}" class="group">
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Customer Analytics</h3>
                <p class="text-purple-100 text-sm">Customer behavior, lifetime value, and segments</p>
            </div>
        </a>

        <!-- Financial Report -->
        <a href="{{ route('admin.reports.financial') }}" class="group">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform transition-all duration-200 hover:scale-105 hover:shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">Financial Summary</h3>
                <p class="text-orange-100 text-sm">Profit margins, COGS, and revenue breakdown</p>
            </div>
        </a>
    </div>

    <!-- Quick Stats Overview -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Overview (Last 30 Days)</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">{{ \App\Models\Order::where('created_at', '>=', now()->subDays(30))->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Orders</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format(\App\Models\Order::where('created_at', '>=', now()->subDays(30))->sum('total'), 2) }}</div>
                <div class="text-sm text-gray-600 mt-1">Revenue</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">{{ \App\Models\Customer::where('created_at', '>=', now()->subDays(30))->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">New Customers</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-orange-600">{{ \App\Models\Product::where('track_inventory', true)->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count() }}</div>
                <div class="text-sm text-gray-600 mt-1">Low Stock Items</div>
            </div>
        </div>
    </div>

    <!-- Available Export Options -->
    <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-300 p-8 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Export Reports</h3>
        <p class="text-gray-600 mb-4">Download detailed reports in CSV format for further analysis</p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('admin.reports.export', ['type' => 'sales']) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-download mr-2"></i>Sales Data
            </a>
            <a href="{{ route('admin.reports.export', ['type' => 'inventory']) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-download mr-2"></i>Inventory Data
            </a>
        </div>
    </div>
</div>
@endsection
