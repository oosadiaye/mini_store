@extends('admin.layout')

@section('content')
<!-- Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Overview</h2>
        <p class="text-gray-500 mt-1">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! Here's what's happening today.</p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-all duration-200">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh Data
        </button>
    </div>
</div>

<!-- Stats Grid -->
<form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm" class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Analytics</h2>
         <div>
            <select name="filter" onchange="document.getElementById('filterForm').submit()" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                <option value="daily" {{ ($filter ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily (Today)</option>
                <option value="weekly" {{ ($filter ?? 'daily') == 'weekly' ? 'selected' : '' }}>Weekly (Last 7 Days)</option>
                <option value="monthly" {{ ($filter ?? 'daily') == 'monthly' ? 'selected' : '' }}>Monthly (Last 30 Days)</option>
                <option value="yearly" {{ ($filter ?? 'daily') == 'yearly' ? 'selected' : '' }}>Yearly (This Year)</option>
            </select>
        </div>
    </div>

<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3 md:gap-6">
    <!-- Total Revenue -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Revenue</h3>
                 <span class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($stats['total_sales'], 2) }}</div>
             <div class="text-xs text-gray-500 mt-2">{{ ucfirst($filter ?? 'daily') }} Sales</div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Orders</h3>
                 <span class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_orders']) }}</div>
             <div class="text-xs text-gray-500 mt-2">{{ ucfirst($filter ?? 'daily') }} Orders</div>
        </div>
    </div>

     <!-- Total Customers -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-teal-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                 <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Customers</h3>
                <span class="p-2 bg-teal-100 text-teal-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_customers']) }}</div>
             <div class="text-xs text-gray-500 mt-2">Total Registered</div>
        </div>
    </div>

    <!-- Products -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-purple-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Products</h3>
                <span class="p-2 bg-purple-100 text-purple-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</div>
            <div class="text-xs text-gray-500 mt-2">Active items</div>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-red-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Low Stock</h3>
                <span class="p-2 bg-red-100 text-red-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
            </div>
             <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['low_stock_products']) }}</div>
             <div class="text-xs text-gray-500 mt-2">Items warn</div>
        </div>
    </div>

    <!-- Expiring Soon -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Expiring</h3>
                <span class="p-2 bg-orange-100 text-orange-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
             <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['expiring_soon'] ?? 0) }}</div>
             <div class="text-xs text-gray-500 mt-2">In 90 days</div>
        </div>
    </div>
</div>
</form>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8 mb-6 md:mb-8">
    <!-- Sales Overview Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h3 class="text-base md:text-lg font-bold text-gray-900">Sales Overview</h3>
            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md">{{ ucfirst($filter ?? 'daily') }} Trend</span>
        </div>
        <div class="relative h-64 md:h-80">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Top Products Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 md:p-6">
        <h3 class="text-base md:text-lg font-bold text-gray-900 mb-4 md:mb-6">Top Selling Products</h3>
        <div class="relative h-48 md:h-64 mb-4">
            <canvas id="topProductsChart"></canvas>
        </div>
        <div class="mt-4 space-y-2 md:space-y-3">
             @foreach($top_products as $index => $item)
                <div class="flex items-center justify-between text-xs md:text-sm">
                    <span class="flex items-center text-gray-600 truncate max-w-[70%]">
                        <span class="w-2 h-2 rounded-full mr-2" style="background-color: {{ ['#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981'][$index % 5] }}"></span>
                        {{ $item->product->name }}
                    </span>
                    <span class="font-bold text-gray-900">{{ $item->total_quantity }} sold</span>
                </div>
             @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 md:px-6 py-4 md:py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-base md:text-lg font-bold text-gray-900">Recent Orders</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs md:text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">View All</a>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            @if($recent_orders->count() > 0)
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-3">Order</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recent_orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-gray-900">#{{ $order->order_number }}</span>
                            <div class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : 
                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 
                                   ($order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                            {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($order->total, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-4 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900">No orders yet</h3>
            </div>
            @endif
        </div>
        
        <!-- Mobile Card View -->
        <div class="md:hidden">
            @if($recent_orders->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($recent_orders as $order)
                <div class="p-3 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <span class="text-sm font-semibold text-gray-900">#{{ $order->order_number }}</span>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold uppercase
                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-700' : 
                               ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 
                               ($order->status === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ $order->status }}
                        </span>
                    </div>
                    <div class="text-sm font-bold text-gray-900">
                        {{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($order->total, 2) }}
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-6 text-center">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 mb-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-xs font-medium text-gray-900">No orders yet</h3>
            </div>
            @endif
        </div>
    </div>

    <!-- Low Stock -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Low Stock Alerts
            </h3>
        </div>
        @if($low_stock_products->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($low_stock_products as $product)
            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition group">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden mr-3 border border-gray-200">
                         @if($product->images->count() > 0)
                            <img src="{{ $product->images->first()->url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $product->name }}</div>
                        <div class="text-xs text-gray-500">SKU: {{ $product->sku }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-red-600 bg-red-50 px-2 py-1 rounded inline-block">{{ $product->stock_quantity }} left</div>
                    <div class="mt-1">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Restock &rarr;</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 text-center text-gray-500">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-50 mb-4 text-green-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="font-medium text-gray-900">All Good!</p>
        </div>
        @endif
    </div>

    <!-- Expiring Soon -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Expiring Soon
            </h3>
        </div>
        @if(isset($expiring_products) && $expiring_products->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($expiring_products as $product)
            <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition group">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden mr-3 border border-gray-200">
                         @if($product->images->count() > 0)
                            <img src="{{ $product->images->first()->url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $product->name }}</div>
                        <div class="text-xs text-gray-500">Exp: {{ $product->expiry_date->format('M d, Y') }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded inline-block">
                        {{ now()->diffInDays($product->expiry_date) }} days
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 text-center text-gray-500">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-50 mb-4 text-green-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="font-medium text-gray-900">No expiring items</p>
            <p class="text-sm text-gray-500">Nothing expiring in 90 days.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($sales_chart);
        
        // Process data for Chart.js (Data is now an array of objects: {label, total})
        const labels = salesData.map(item => item.label);
        const data = salesData.map(item => item.total);

        // Gradient for line chart
        const gradient = salesCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)'); // Indigo-500 with opacity
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue',
                    data: data,
                    borderColor: '#6366F1', // Indigo-500
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366F1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        padding: 12,
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    // Use '₦' as fallback if currency symbol is issue in JS string
                                    const symbol = '{{ tenant('data')['currency_symbol'] ?? '₦' }}'; 
                                    label += symbol + new Intl.NumberFormat('en-US').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            color: '#F1F5F9'
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#64748B',
                            callback: function(value) {
                                const symbol = '{{ tenant('data')['currency_symbol'] ?? '₦' }}';
                                return symbol + (value >= 1000 ? (value/1000) + 'k' : value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 },
                            color: '#64748B'
                        }
                    }
                }
            }
        });

        // Top Products Chart
        const productsCtx = document.getElementById('topProductsChart').getContext('2d');
        const topProducts = @json($top_products);
        
        const productLabels = topProducts.map(item => item.product.name.length > 15 ? item.product.name.substring(0, 15) + '...' : item.product.name);
        const productData = topProducts.map(item => item.total_quantity);
        const colors = ['#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981'];

        new Chart(productsCtx, {
            type: 'doughnut',
            data: {
                labels: productLabels,
                datasets: [{
                    data: productData,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                         backgroundColor: '#1E293B',
                        padding: 12,
                        cornerRadius: 8,
                    }
                }
            }
        });
    });
</script>
@endpush
