@extends('admin.layout')

@section('content')
<div x-data="adminDashboard()" v-pre>
<!-- Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
        <div class="flex items-center space-x-3">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Overview</h2>
        </div>
        <p class="text-gray-500 mt-1">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! Here's what's happening today. <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 animate-pulse" x-show="loading">Updating...</span></p>
    </div>
    <div class="mt-4 sm:mt-0">
        <button @click="fetchData()" class="inline-flex items-center px-4 py-2 bg-white border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-all duration-200">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Refresh Data
        </button>
    </div>
</div>

{{-- Subscription Banner --}}
@php
    $plan = $tenant->currentPlan;
    $isOnTrial = $tenant->trial_ends_at && now()->lt($tenant->trial_ends_at);
    $subscriptionEnds = $tenant->subscription_ends_at;
    $daysRemaining = $subscriptionEnds ? now()->diffInDays($subscriptionEnds, false) : null;
    $isExpired = $daysRemaining !== null && $daysRemaining < 0;
    $isExpiringSoon = $daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 7;
@endphp

<div class="mb-8">
    @if($isExpired)
        {{-- Expired Subscription --}}
        <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg p-6 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-red-900">Subscription Expired</h3>
                    <div class="mt-2 text-sm text-red-800">
                        <p>Your <strong>{{ $plan->name ?? 'subscription' }}</strong> expired {{ abs($daysRemaining) }} day(s) ago on <strong>{{ $subscriptionEnds->format('M d, Y') }}</strong>.</p>
                        <p class="mt-1">Please renew to continue using all features.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('tenant.subscription.index', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Renew Subscription
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif($isExpiringSoon)
        {{-- Expiring Soon --}}
        <div class="bg-gradient-to-r from-yellow-50 to-amber-100 border-l-4 border-yellow-500 rounded-lg p-6 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-yellow-900">Subscription Expiring Soon</h3>
                    <div class="mt-2 text-sm text-yellow-800">
                        <p>Your <strong>{{ $plan->name ?? 'subscription' }}</strong> will expire in <strong>{{ $daysRemaining }} day(s)</strong> on <strong>{{ $subscriptionEnds->format('M d, Y') }}</strong>.</p>
                        @if($isOnTrial)
                            <p class="mt-1">🎉 You're currently on a <strong>trial period</strong> (ends {{ $tenant->trial_ends_at->format('M d, Y') }}).</p>
                        @endif
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('tenant.subscription.index', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Renew Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Active Subscription --}}
        <div class="bg-gradient-to-r from-indigo-50 to-blue-100 border-l-4 border-indigo-500 rounded-lg p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex items-start flex-1">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-indigo-900">Active Subscription</h3>
                        <div class="mt-2 text-sm text-indigo-800 space-y-1">
                            <p><strong>Plan:</strong> {{ $plan->name ?? 'N/A' }}</p>
                            @if($subscriptionEnds)
                                <p><strong>Started:</strong> {{ $subscriptionEnds->copy()->subDays($plan->duration_days ?? 30)->format('M d, Y') }}</p>
                                <p><strong>Expires:</strong> {{ $subscriptionEnds->format('M d, Y') }} ({{ $daysRemaining }} days remaining)</p>
                            @endif
                            @if($isOnTrial)
                                <p class="mt-2 inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    🎉 Trial Period (ends {{ $tenant->trial_ends_at->format('M d, Y') }})
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="ml-4">
                    <a href="{{ route('tenant.subscription.index', ['tenant' => $tenant->slug]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-indigo-300 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-50 transition shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Manage Plan
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Stats Grid -->
<form method="GET" action="{{ route('admin.dashboard', ['tenant' => request()->route('tenant')]) }}" id="filterForm" class="mb-8" @submit.prevent="updateFilter($event)">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Analytics</h2>
         <div>
            <select name="filter" x-model="filter" @change="updateFilter()" class="block w-full pl-3 pr-10 py-2 text-base border-2 border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md shadow-sm">
                <option value="daily">Daily (Today)</option>
                <option value="weekly">Weekly (Last 7 Days)</option>
                <option value="monthly">Monthly (Last 30 Days)</option>
                <option value="yearly">Yearly (This Year)</option>
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
            <div class="text-2xl font-bold text-gray-900" x-text="currencySymbol + formatNumber(stats.total_sales, 2)">{{ $tenant->currency_symbol }}{{ number_format($stats['total_sales'], 2) }}</div>
             <div class="text-xs text-gray-500 mt-2" x-text="capitalize(filter) + ' Sales'">{{ ucfirst($filter ?? 'daily') }} Sales</div>
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
            <div class="text-2xl font-bold text-gray-900" x-text="formatNumber(stats.total_orders)">{{ number_format($stats['total_orders']) }}</div>
             <div class="text-xs text-gray-500 mt-2" x-text="capitalize(filter) + ' Orders'">{{ ucfirst($filter ?? 'daily') }} Orders</div>
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
            <div class="text-2xl font-bold text-gray-900" x-text="formatNumber(stats.total_customers)">{{ number_format($stats['total_customers']) }}</div>
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
            <div class="text-2xl font-bold text-gray-900" x-text="formatNumber(stats.total_products)">{{ number_format($stats['total_products']) }}</div>
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
             <div class="text-2xl font-bold text-gray-900" x-text="formatNumber(stats.low_stock_products)">{{ number_format($stats['low_stock_products']) }}</div>
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
             <div class="text-2xl font-bold text-gray-900" x-text="formatNumber(stats.expiring_soon)">{{ number_format($stats['expiring_soon'] ?? 0) }}</div>
             <div class="text-xs text-gray-500 mt-2">In 6 months</div>
        </div>
    </div>

    <!-- Receivables -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider text-emerald-700">Receivables</h3>
                <span class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
             <div class="text-2xl font-bold text-emerald-600" x-text="currencySymbol + formatNumber(stats.total_receivables, 0)">{{ $tenant->currency_symbol }}{{ number_format($stats['total_receivables'] ?? 0, 0) }}</div>
             <div class="text-xs text-emerald-500 mt-2">Unpaid Invoices</div>
        </div>
    </div>

    <!-- Payables -->
    <div class="bg-white rounded-xl p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-full -mr-8 -mt-8 group-hover:scale-110 transition-transform duration-300"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-500 text-sm font-semibold uppercase tracking-wider text-rose-700">Payables</h3>
                <span class="p-2 bg-rose-100 text-rose-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path></svg>
                </span>
            </div>
             <div class="text-2xl font-bold text-rose-600" x-text="currencySymbol + formatNumber(stats.total_payables, 0)">{{ $tenant->currency_symbol }}{{ number_format($stats['total_payables'] ?? 0, 0) }}</div>
             <div class="text-xs text-rose-500 mt-2">Unpaid Bills</div>
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
            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-md" x-text="capitalize(filter) + ' Trend'">{{ ucfirst($filter ?? 'daily') }} Trend</span>
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
             <template x-for="(item, index) in topProducts" :key="item.product_id">
                <div class="flex items-center justify-between text-xs md:text-sm">
                    <span class="flex items-center text-gray-600 truncate max-w-[70%]">
                        <span class="w-2 h-2 rounded-full mr-2" :style="'background-color: ' + ['#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981'][index % 5]"></span>
                        <span x-text="item.product.name"></span>
                    </span>
                    <span class="font-bold text-gray-900" x-text="item.total_quantity + ' sold'"></span>
                </div>
             </template>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-8">
    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 md:px-6 py-4 md:py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-base md:text-lg font-bold text-gray-900">Recent Orders</h3>
            <a href="{{ route('admin.orders.index', ['tenant' => $tenant->slug]) }}" class="text-xs md:text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">View All</a>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left" x-show="recentOrders.length > 0">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                    <tr>
                        <th class="px-6 py-3">Order</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="order in recentOrders" :key="order.id">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-900" x-text="'#' + order.order_number"></span>
                                <div class="text-xs text-gray-500" x-text="formatRelativeDate(order.created_at)"></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide"
                                    :class="{
                                        'bg-green-100 text-green-700': order.status === 'completed',
                                        'bg-red-100 text-red-700': order.status === 'cancelled',
                                        'bg-blue-100 text-blue-700': order.status === 'processing',
                                        'bg-yellow-100 text-yellow-700': !['completed', 'cancelled', 'processing'].includes(order.status)
                                    }" x-text="order.status"></span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900" x-text="currencySymbol + formatNumber(order.total, 2)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div class="p-8 text-center" x-show="recentOrders.length === 0">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-4 text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900">No orders yet</h3>
            </div>
        </div>
        
        <!-- Mobile Card View -->
        <div class="md:hidden">
            <div class="divide-y divide-gray-100" x-show="recentOrders.length > 0">
                <template x-for="order in recentOrders" :key="order.id">
                    <div class="p-3 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <span class="text-sm font-semibold text-gray-900" x-text="'#' + order.order_number"></span>
                                <div class="text-xs text-gray-500 mt-0.5" x-text="formatRelativeDate(order.created_at)"></div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold uppercase"
                                :class="{
                                    'bg-green-100 text-green-700': order.status === 'completed',
                                    'bg-red-100 text-red-700': order.status === 'cancelled',
                                    'bg-blue-100 text-blue-700': order.status === 'processing',
                                    'bg-yellow-100 text-yellow-700': !['completed', 'cancelled', 'processing'].includes(order.status)
                                }" x-text="order.status"></span>
                        </div>
                        <div class="text-sm font-bold text-gray-900" x-text="currencySymbol + formatNumber(order.total, 2)"></div>
                    </div>
                </template>
            </div>
            <div class="p-6 text-center" x-show="recentOrders.length === 0">
                <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 mb-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-xs font-medium text-gray-900">No orders yet</h3>
            </div>
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
        <div class="divide-y divide-gray-100" x-show="lowStockProducts.length > 0">
            <template x-for="product in lowStockProducts" :key="product.id">
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden mr-3 border border-gray-200">
                            <template x-if="product.images && product.images.length > 0">
                                <img :src="product.images[0].url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!(product.images && product.images.length > 0)">
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </template>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition" x-text="product.name"></div>
                            <div class="text-xs text-gray-500" x-text="'SKU: ' + product.sku"></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-red-600 bg-red-50 px-2 py-1 rounded inline-block" x-text="product.stock_quantity + ' left'"></div>
                        <div class="mt-1">
                            <a :href="'{{ url('/') }}/' + '{{ $tenant->slug }}' + '/admin/products/' + product.id + '/edit'" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Restock &rarr;</a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div class="p-8 text-center text-gray-500" x-show="lowStockProducts.length === 0">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-50 mb-4 text-green-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="font-medium text-gray-900">All Good!</p>
        </div>
    </div>

    <!-- Expiring Soon -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Expiring Soon
            </h3>
        </div>
        <div class="divide-y divide-gray-100" x-show="expiringProducts.length > 0">
            <template x-for="product in expiringProducts" :key="product.id">
                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition group">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden mr-3 border border-gray-200">
                            <template x-if="product.images && product.images.length > 0">
                                <img :src="product.images[0].url" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!(product.images && product.images.length > 0)">
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            </template>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition" x-text="product.name"></div>
                            <div class="text-xs text-gray-500" x-text="'Exp: ' + formatDate(product.expiry_date)"></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded inline-block" x-text="diffInDays(product.expiry_date) + ' days'"></div>
                    </div>
                </div>
            </template>
        </div>
        <div class="p-8 text-center text-gray-500" x-show="expiringProducts.length === 0">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-50 mb-4 text-green-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="font-medium text-gray-900">No expiring items</p>
            <p class="text-sm text-gray-500">Nothing expiring in 6 months.</p>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function adminDashboard() {
    return {
        loading: false,
        filter: '{{ $filter ?? "daily" }}',
        stats: {!! json_encode($stats) !!},
        recentOrders: {!! json_encode($recent_orders) !!},
        lowStockProducts: {!! json_encode($low_stock_products) !!},
        expiringProducts: {!! json_encode($expiring_products) !!},
        topProducts: {!! json_encode($top_products) !!},
        salesChartData: {!! json_encode($sales_chart) !!},
        currencySymbol: '{{ $tenant->currency_symbol }}',
        salesChart: null,
        topProductsChart: null,

        init() {
            this.initSalesChart();
            this.initTopProductsChart();
            setInterval(() => this.fetchData(), 60000); // Poll every 60 seconds
        },

        initSalesChart() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            this.salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.salesChartData.map(i => i.label),
                    datasets: [{
                        label: 'Revenue',
                        data: this.salesChartData.map(i => i.total),
                        borderColor: '#6366F1',
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
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            padding: 12,
                            titleFont: { size: 13 },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: (context) => {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += this.currencySymbol + new Intl.NumberFormat('en-US').format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#F1F5F9' },
                            ticks: {
                                font: { size: 11 },
                                color: '#64748B',
                                callback: (value) => this.currencySymbol + (value >= 1000 ? (value/1000) + 'k' : value)
                            }
                        },
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748B' } }
                    }
                }
            });
        },

        initTopProductsChart() {
            const ctx = document.getElementById('topProductsChart').getContext('2d');
            this.topProductsChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: this.topProducts.map(i => i.product.name.length > 15 ? i.product.name.substring(0, 15) + '...' : i.product.name),
                    datasets: [{
                        data: this.topProducts.map(i => i.total_quantity),
                        backgroundColor: ['#6366F1', '#8B5CF6', '#EC4899', '#F59E0B', '#10B981'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1E293B', padding: 12, cornerRadius: 8 }
                    }
                }
            });
        },

        async fetchData() {
            if (this.loading) return;
            this.loading = true;
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('filter', this.filter);
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await response.json();
                
                this.stats = data.stats;
                this.recentOrders = data.recent_orders;
                this.lowStockProducts = data.low_stock_products;
                this.expiringProducts = data.expiring_products;
                this.topProducts = data.top_products;
                this.salesChartData = data.sales_chart;

                // Update Charts
                this.salesChart.data.labels = this.salesChartData.map(i => i.label);
                this.salesChart.data.datasets[0].data = this.salesChartData.map(i => i.total);
                this.salesChart.update();

                this.topProductsChart.data.labels = this.topProducts.map(i => i.product.name.length > 15 ? i.product.name.substring(0, 15) + '...' : i.product.name);
                this.topProductsChart.data.datasets[0].data = this.topProducts.map(i => i.total_quantity);
                this.topProductsChart.update();

            } catch (error) {
                console.error('Failed to fetch real-time data:', error);
            } finally {
                this.loading = false;
            }
        },

        async updateFilter(event) {
            if (event) event.preventDefault();
            await this.fetchData();
            // Update URL without reload
            const url = new URL(window.location.href);
            url.searchParams.set('filter', this.filter);
            window.history.pushState({}, '', url);
        },

        formatNumber(num, decimals = 0) {
            return Number(num).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },

        capitalize(str) {
            if (!str) return '';
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        },

        formatRelativeDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            if (diffInSeconds < 60) return 'just now';
            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' mins ago';
            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
            return Math.floor(diffInSeconds / 86400) + ' days ago';
        },

        diffInDays(dateStr) {
            if (!dateStr) return 0;
            const date = new Date(dateStr);
            const now = new Date();
            const diffInTime = date.getTime() - now.getTime();
            return Math.ceil(diffInTime / (1000 * 3600 * 24));
        }
    };
}
</script>
@endpush
