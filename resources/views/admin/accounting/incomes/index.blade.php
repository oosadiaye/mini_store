@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Income Log</h3>
            <p class="text-xs text-gray-500">Record contributions, sales, and other revenue manually.</p>
        </div>
        <a href="{{ route('admin.incomes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
            + Record Income
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Reference</th>
                    <th class="px-6 py-3">Category (Credit)</th>
                    <th class="px-6 py-3">Customer</th>
                    <th class="px-6 py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($incomes as $income)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-gray-500">{{ $income->transaction_date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $income->reference_number }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $income->account->account_name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $income->customer_name ?? '-' }}</td>
                    <td class="px-6 py-3 text-right font-bold text-green-600">+{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($income->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No income records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $incomes->links() }}
    </div>
</div>
@endsection
