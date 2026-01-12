@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Supplier Ledger: {{ $supplier->name }}</h1>
        <p class="text-gray-600">Transaction History and Balance</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
            Back to List
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-700">Transactions</h3>
        <div class="text-right">
             <!-- Liability/Payable: Credit increases, Debit decreases -->
             @php
                 $totalDebit = $supplier->transactions()->sum('debit') ?? 0;
                 $totalCredit = $supplier->transactions()->sum('credit') ?? 0;
                 $balance = $totalCredit - $totalDebit;
             @endphp
             <span class="text-sm text-gray-500">Current Balance:</span>
             <span class="text-xl font-bold {{ $balance > 0 ? 'text-red-600' : ($balance < 0 ? 'text-green-600' : 'text-gray-800') }}">
                 {{ $tenant->data['currency_symbol'] ?? '$' }}{{ number_format($balance, 2) }}
             </span>
             <span class="text-xs text-gray-400 block">(Payables)</span>
        </div>
    </div>
    <!-- Invoices Section -->
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-700 mb-4">Supplier Invoices</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mb-6">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO #</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($invoice->purchaseOrder)
                            <a href="{{ route('admin.purchase-orders.show', $invoice->purchaseOrder->id) }}" class="text-indigo-600 hover:underline">{{ $invoice->purchaseOrder->po_number }}</a>
                            @else
                            -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                            {{ $tenant->data['currency_symbol'] ?? '$' }}{{ number_format($invoice->total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                            {{ $tenant->data['currency_symbol'] ?? '$' }}{{ number_format($invoice->amount_paid, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($invoice->status === 'unpaid')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Unpaid</span>
                            @elseif($invoice->status === 'paid')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                             @elseif($invoice->status === 'reversed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Reversed</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($invoice->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($invoice->status === 'unpaid' && $invoice->amount_paid == 0)
                            <form action="{{ route('admin.supplier-invoices.reverse', $invoice->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reverse this invoice? This action cannot be undone.')">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold hover:underline">Reverse</button>
                            </form>
                            @elseif($invoice->status === 'reversed')
                                <span class="text-gray-400 italic">Reversed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                        No transactions found for this supplier.
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
