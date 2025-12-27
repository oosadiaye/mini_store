@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Account Ledger</h2>
            <p class="text-gray-500 text-sm mt-1">{{ $account->account_code }} - {{ $account->account_name }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
             <a href="{{ route('admin.accounts.index') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-200 transition">
                Back to Accounts
            </a>
            <a href="{{ route('admin.accounts.edit', $account->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                Edit Account
            </a>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Current Balance</h3>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-gray-900">
                    {{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format(abs($account->balance), 2) }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $account->balance >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $account->balance >= 0 ? 'Dr' : 'Cr' }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500">
                Type: <span class="capitalize font-semibold">{{ $account->account_type }}</span>
            </p>
        </div>
        
        <!-- Filter Form -->
        <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center">
             <form action="{{ route('admin.accounts.show', $account->id) }}" method="GET" class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 transition w-full">
                            Filter
                        </button>
                        @if(request('start_date') || request('end_date'))
                            <a href="{{ route('admin.accounts.show', $account->id) }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-200 transition text-center">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Entry #</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4 text-right">Debit</th>
                        <th class="px-6 py-4 text-right">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $line)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                            {{ $line->journalEntry->entry_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-indigo-600">
                            {{ $line->journalEntry->entry_number }}
                        </td>
                        <td class="px-6 py-4 text-gray-900">
                            <span class="block font-medium">{{ $line->description }}</span>
                            @if($line->journalEntry->description != $line->description)
                                <span class="text-xs text-gray-500">{{ $line->journalEntry->description }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">
                            @if($line->debit > 0)
                                {{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($line->debit, 2) }}
                            @else
                                -
                            @endif
                        </td>
                         <td class="px-6 py-4 text-right font-medium text-gray-900">
                            @if($line->credit > 0)
                                {{ $tenant->data['currency_symbol'] ?? '₦' }}{{ number_format($line->credit, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No transactions found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
