@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Expense Log</h3>
            <p class="text-xs text-gray-500">Record bills, salaries, rent, and other expenses.</p>
        </div>
        <a href="{{ route('admin.expenses.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700">
            + Record Expense
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Reference</th>
                    <th class="px-6 py-3">Category (Debit)</th>
                    <th class="px-6 py-3">Vendor</th>
                    <th class="px-6 py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($expenses as $expense)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-gray-500">{{ $expense->transaction_date->format('M d, Y') }}</td>
                    <td class="px-6 py-3 font-mono text-xs text-gray-500">{{ $expense->reference_number }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $expense->account->account_name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $expense->vendor_name ?? '-' }}</td>
                    <td class="px-6 py-3 text-right font-bold text-red-600">-{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($expense->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No expense records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $expenses->links() }}
    </div>
</div>
@endsection
