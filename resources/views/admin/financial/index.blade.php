@extends('admin.layout')

@section('title', 'Financial Analysis')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Financial Analysis</h1>
            <p class="mt-1 text-sm text-slate-500">
                Performance snapshot for {{ $start->format('M d, Y') }} - {{ $end->format('M d, Y') }}
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <!-- Timeframe Filter (Placeholder) -->
            <button class="bg-white border border-slate-300 text-slate-700 font-medium py-2 px-4 rounded-md shadow-sm text-sm hover:bg-slate-50">
                This Month
            </button>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Revenue Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-indigo-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-500 truncate">Total Revenue</dt>
                            <dd>
                                <div class="text-2xl font-bold text-slate-900">{{ number_format($revenue, 2) }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-green-600 font-bold">
                        <i class="fas fa-arrow-up"></i> {{ $revenueGrowth }}%
                    </span>
                    <span class="text-slate-500"> from last month</span>
                </div>
            </div>
        </div>

        <!-- Expense Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-red-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-500 truncate">Total Expenses</dt>
                            <dd>
                                <div class="text-2xl font-bold text-slate-900">{{ number_format($expenses, 2) }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-5 py-3">
                <div class="text-sm">
                    <span class="text-red-500 font-bold">
                        <i class="fas fa-arrow-up"></i> {{ $expenseGrowth }}%
                    </span>
                    <span class="text-slate-500"> from last month</span>
                </div>
            </div>
        </div>

        <!-- Profit Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-500">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                         <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-slate-500 truncate">Net Profit</dt>
                            <dd>
                                <div class="text-2xl font-bold text-slate-900">{{ number_format($profit, 2) }}</div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
             <div class="bg-slate-50 px-5 py-3">
                <div class="text-sm">
                     <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">View detailed breakdown &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section (Placeholder UI) -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-slate-900 mb-4">Revenue vs Expenses (Trend)</h3>
        <div class="h-64 bg-slate-50 flex items-end justify-between p-4 rounded border border-dashed border-slate-300">
             <!-- Mock Bars -->
             <div class="w-12 bg-indigo-200 hover:bg-indigo-300 rounded-t h-1/4 relative group"><div class="hidden group-hover:block absolute bottom-full mb-1 text-xs bg-black text-white p-1 rounded">Week 1</div></div>
             <div class="w-12 bg-indigo-300 hover:bg-indigo-400 rounded-t h-2/4 relative group"><div class="hidden group-hover:block absolute bottom-full mb-1 text-xs bg-black text-white p-1 rounded">Week 2</div></div>
             <div class="w-12 bg-indigo-400 hover:bg-indigo-500 rounded-t h-1/2 relative group"><div class="hidden group-hover:block absolute bottom-full mb-1 text-xs bg-black text-white p-1 rounded">Week 3</div></div>
             <div class="w-12 bg-indigo-500 hover:bg-indigo-600 rounded-t h-3/4 relative group"><div class="hidden group-hover:block absolute bottom-full mb-1 text-xs bg-black text-white p-1 rounded">Week 4</div></div>
        </div>
         <p class="text-center text-xs text-slate-400 mt-2">Comparison over last 4 weeks</p>
    </div>
</div>
@endsection
