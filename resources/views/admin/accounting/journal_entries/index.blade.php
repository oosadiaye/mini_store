@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-bold text-gray-800">General Ledger</h3>
        <p class="text-xs text-gray-500">A chronological list of all journal entries.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 w-32">Date</th>
                    <th class="px-6 py-3 w-32">Number</th>
                    <th class="px-6 py-3">Description / Accounts</th>
                    <th class="px-6 py-3 text-right w-32">Debit</th>
                    <th class="px-6 py-3 text-right w-32">Credit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($entries as $entry)
                <tr class="hover:bg-gray-50 transition align-top">
                    <td class="px-6 py-4 text-gray-600 font-medium">{{ $entry->entry_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $entry->entry_number }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800 mb-2">{{ $entry->description }}</div>
                        <div class="space-y-1">
                            @foreach($entry->lines as $line)
                            <div class="flex justify-between text-xs border-b border-gray-50 pb-1 last:border-0 last:pb-0">
                                <span class="font-medium {{ $line->debit > 0 ? 'text-gray-800' : 'text-gray-500 pl-4' }}">
                                    {{ $line->account->account_name }} 
                                    <span class="text-gray-400 font-mono">({{ $line->account->account_code }})</span>
                                    @if($line->entity)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ class_basename($line->entity_type) }}: {{ $line->entity->name }}
                                        </span>
                                    @endif
                                </span>
                                <div class="flex w-48 text-right">
                                     <span class="w-24 {{ $line->debit > 0 ? 'font-bold text-gray-900' : 'text-gray-300' }}">
                                        {{ $line->debit > 0 ? number_format($line->debit, 2) : '-' }}
                                     </span>
                                     <span class="w-24 {{ $line->credit > 0 ? 'font-bold text-gray-900' : 'text-gray-300' }}">
                                        {{ $line->credit > 0 ? number_format($line->credit, 2) : '-' }}
                                     </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-400 align-bottom border-t border-gray-50">
                        {{ number_format($entry->total_debit, 2) }}
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-gray-400 align-bottom border-t border-gray-50">
                        {{ number_format($entry->total_credit, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        No journal entries found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $entries->links() }}
    </div>
</div>
@endsection
