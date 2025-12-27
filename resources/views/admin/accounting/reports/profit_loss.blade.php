@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Profit & Loss Statement</h3>
            <p class="text-sm text-gray-500">Income Statement for period</p>
        </div>
        <form action="{{ route('admin.accounting.profit-loss') }}" method="GET" class="flex space-x-2 bg-white p-2 rounded shadow-sm border border-gray-200">
            <select name="branch_id" class="text-sm border-gray-300 rounded">
                <option value="">All Branches (Consolidated)</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ isset($branchId) && $branchId == $wh->id ? 'selected' : '' }}>
                        {{ $wh->name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm border-gray-300 rounded">
            <span class="text-gray-400 self-center">to</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm border-gray-300 rounded">
            <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">Filter</button>
        </form>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-8 space-y-8">
            <!-- Revenue Section -->
            <div>
                <h4 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">Revenue</h4>
                <div class="space-y-2">
                    @foreach($revenues as $account)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $account->account_name }}</span>
                        <span class="font-medium text-gray-900">{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($account->balance, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-100 mt-2">
                        <span>Total Revenue</span>
                        <span>{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($totalRevenue, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Expenses Section -->
            <div>
                <h4 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">Operating Expenses</h4>
                <div class="space-y-2">
                    @foreach($expenses as $account)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $account->account_name }}</span>
                        <span class="font-medium text-gray-900">{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($account->balance, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-100 mt-2">
                        <span>Total Expenses</span>
                        <span>{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($totalExpenses, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Net Income -->
            <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center border border-gray-200">
                <span class="text-lg font-bold text-gray-800">Net Income</span>
                <span class="text-xl font-bold {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($netIncome, 2) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
