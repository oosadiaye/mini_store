@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Trial Balance</h2>
            <p class="mt-2 text-gray-600">Report as of {{ \Carbon\Carbon::parse($asOfDate)->format('M d, Y') }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
             <form action="{{ route('admin.accounting.trial-balance') }}" method="GET" class="flex items-center space-x-2 bg-white p-1 rounded-lg border border-gray-300 shadow-sm">
                <input type="date" name="date" value="{{ $asOfDate }}" class="border-0 focus:ring-0 text-sm rounded-md bg-transparent">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-md text-sm font-medium transition">
                    Update
                </button>
            </form>
             <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md hover:bg-indigo-700 hover:shadow-lg transition">
                Print Report
            </button>
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 p-6 flex flex-col md:flex-row justify-between items-center {{ abs($totalDebit - $totalCredit) < 0.01 ? 'border-l-4 border-l-green-500' : 'border-l-4 border-l-red-500' }}">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Ledger Status</h3>
             @if(abs($totalDebit - $totalCredit) < 0.01)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                    <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                    Balanced
                </span>
            @else
                 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                    <svg class="mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                    Unbalanced (Diff: {{ number_format(abs($totalDebit - $totalCredit), 2) }})
                </span>
            @endif
        </div>
        <div class="mt-4 md:mt-0 text-right">
             <div class="text-sm text-gray-500">Total Volume</div>
             <div class="text-2xl font-bold text-gray-900">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($totalDebit, 2) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 uppercase tracking-wider text-xs">Account Code</th>
                        <th class="px-6 py-4 uppercase tracking-wider text-xs">Account Name</th>
                        <th class="px-6 py-4 uppercase tracking-wider text-xs text-right">Debit</th>
                        <th class="px-6 py-4 uppercase tracking-wider text-xs text-right">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($accounts as $account)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-3 font-mono text-gray-500 text-xs">{{ $account->account_code }}</td>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $account->account_name }}</td>
                        <td class="px-6 py-3 text-right font-mono text-gray-700">
                            @if($account->net_debit > 0)
                                {{ number_format($account->net_debit, 2) }}
                            @endif
                        </td>
                         <td class="px-6 py-3 text-right font-mono text-gray-700">
                            @if($account->net_credit > 0)
                                {{ number_format($account->net_credit, 2) }}
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            No active balances found as of this date.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-200 font-bold text-gray-900">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-right uppercase tracking-wider text-xs">Totals</td>
                        <td class="px-6 py-4 text-right font-mono text-indigo-700">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($totalDebit, 2) }}</td>
                        <td class="px-6 py-4 text-right font-mono text-indigo-700">{{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($totalCredit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
