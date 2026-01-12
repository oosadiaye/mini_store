@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Journal Entry: {{ $entry->entry_number }}</h1>
        <p class="text-xs text-gray-500">{{ $entry->description }}</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('admin.journal-entries.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
            Back to List
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Entry Date</label>
        <div class="text-lg font-bold text-gray-800">{{ $entry->entry_date->format('M d, Y') }}</div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Reference</label>
        <div class="text-lg font-bold text-gray-800">{{ $entry->reference_number ?? 'N/A' }}</div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Amount</label>
        <div class="text-lg font-bold text-indigo-600">
             {{ $tenant->data['currency_symbol'] ?? '$' }}{{ number_format($entry->total_debit, 2) }}
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="font-bold text-gray-700">Transaction Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($entry->lines as $line)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        <span class="text-gray-400 font-mono">({{ $line->account->account_code }})</span>
                        {{ $line->account->account_name }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($line->entity)
                             <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                {{ class_basename($line->entity_type) }}: {{ $line->entity->name }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $line->description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                         {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                         {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <th colspan="3" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-right text-sm font-black text-gray-900">
                        {{ number_format($entry->total_debit, 2) }}
                    </th>
                    <th class="px-6 py-3 text-right text-sm font-black text-gray-900">
                        {{ number_format($entry->total_credit, 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
