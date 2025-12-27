@extends('layouts.superadmin')

@section('header', 'Support Tickets')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500">
            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Subject</th>
                    <th class="px-6 py-3">Tenant</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Priority</th>
                    <th class="px-6 py-3">Last Update</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $ticket->subject }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $ticket->tenant->name }} <br>
                        <span class="text-xs text-gray-400">{{ $ticket->tenant->id }}</span>
                    </td>
                    <td class="px-6 py-4">
                        {{ $ticket->category->name }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $ticket->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $ticket->status === 'resolved' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $ticket->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $ticket->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $ticket->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        {{ $ticket->updated_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('superadmin.tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-900 font-medium">Manage</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No tickets found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
