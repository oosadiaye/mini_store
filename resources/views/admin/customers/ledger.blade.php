@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Customer Ledger: {{ $customer->name }}</h1>
        <p class="text-gray-600">Transaction History and Balance</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('admin.customers.show', $customer) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
            Back to Profile
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-700">Transactions</h3>
        <div class="text-right">
             <!-- Current Balance Calculation (Simple Sum of Debits - Credits or vice versa depending on account type) -->
             <!-- Asset/Receivable: Debit increases, Credit decreases -->
             @php
                 $totalDebit = $customer->transactions()->sum('debit') ?? 0;
                 $totalCredit = $customer->transactions()->sum('credit') ?? 0;
                 $balance = $totalDebit - $totalCredit;
             @endphp
             <span class="text-sm text-gray-500">Current Balance:</span>
             <span class="text-xl font-bold {{ $balance > 0 ? 'text-green-600' : ($balance < 0 ? 'text-red-600' : 'text-gray-800') }}">
                 {{ tenant('data')['currency_symbol'] ?? '$' }}{{ number_format($balance, 2) }}
             </span>
             <span class="text-xs text-gray-400 block">(Receivables)</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $line)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $line->journalEntry->entry_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 hover:text-indigo-900">
                        <a href="{{ route('admin.journal-entries.show', $line->journalEntry) }}">
                            {{ $line->journalEntry->reference_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $line->description ?? $line->journalEntry->description }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $line->account->account_code }} - {{ $line->account->account_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                        @if($line->debit > 0)
                            {{ number_format($line->debit, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                         @if($line->credit > 0)
                            {{ number_format($line->credit, 2) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        No transactions found for this customer.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
