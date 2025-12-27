@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Balance Sheet</h3>
            <p class="text-sm text-gray-500">Financial position as of {{ \Carbon\Carbon::parse($asOfDate)->format('M d, Y') }}</p>
        </div>
        <form action="{{ route('admin.accounting.balance-sheet') }}" method="GET" class="flex space-x-2 bg-white p-2 rounded shadow-sm border border-gray-200">
            <select name="branch_id" class="text-sm border-gray-300 rounded">
                <option value="">All Branches (Consolidated)</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" {{ isset($branchId) && $branchId == $wh->id ? 'selected' : '' }}>
                        {{ $wh->name }}
                    </option>
                @endforeach
            </select>
            <span class="text-sm text-gray-500 self-center px-2">As of:</span>
            <input type="date" name="date" value="{{ $asOfDate }}" class="text-sm border-gray-300 rounded">
            <button type="submit" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">Update</button>
        </form>
    </div>
    
    <div class="grid grid-cols-2 gap-6">
        <!-- Assets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 h-full">
            <h4 class="text-lg font-bold text-gray-800 border-b-2 border-indigo-500 pb-2 mb-4">Assets</h4>
            <div class="space-y-2">
                @foreach($assets as $account)
                <div class="flex justify-between text-sm group">
                    <span class="text-gray-600 group-hover:text-gray-900">{{ $account->account_name }}</span>
                    <span class="font-medium text-gray-900">{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($account->balance, 2) }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-8 pt-4 border-t border-gray-100 flex justify-between text-base font-bold text-indigo-900 bg-indigo-50 p-3 rounded">
                <span>Total Assets</span>
                <span>{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($totalAssets, 2) }}</span>
            </div>
        </div>

        <!-- Liabilities & Equity -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h4 class="text-lg font-bold text-gray-800 border-b-2 border-red-500 pb-2 mb-4">Liabilities</h4>
                <div class="space-y-2">
                    @foreach($liabilities as $account)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $account->account_name }}</span>
                        <span class="font-medium text-gray-900">{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($account->balance, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100 mt-2">
                        <span>Total Liabilities</span>
                        <span>{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($totalLiabilities, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h4 class="text-lg font-bold text-gray-800 border-b-2 border-blue-500 pb-2 mb-4">Equity</h4>
                <div class="space-y-2">
                    @foreach($equity as $account)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $account->account_name }}</span>
                        <span class="font-medium text-gray-900">{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($account->balance, 2) }}</span>
                    </div>
                    @endforeach
                    
                    <!-- Retained Earnings / Net Income -->
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Net Income (Current Period)</span>
                        <span class="font-medium {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($netIncome, 2) }}
                        </span>
                    </div>

                    <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-100 mt-2">
                        <span>Total Equity</span>
                        <span>{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($totalEquity, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 text-white rounded-lg p-4 flex justify-between font-bold shadow-md">
                <span>Total Liabilities & Equity</span>
                <span>{{ app('tenant')->data['currency_symbol'] ?? '₦' }}{{ number_format($totalLiabilities + $totalEquity, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
