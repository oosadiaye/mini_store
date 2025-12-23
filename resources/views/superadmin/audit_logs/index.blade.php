@extends('layouts.superadmin')

@section('header', 'Audit Logs')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">System Activity</h2>
    <p class="text-gray-500 text-sm">Track important actions performed by administrators.</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">User</th>
                <th class="p-4 border-b border-gray-100">Action</th>
                <th class="p-4 border-b border-gray-100">Description</th>
                <th class="p-4 border-b border-gray-100">IP Address</th>
                <th class="p-4 border-b border-gray-100">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 text-sm font-medium text-gray-900">
                    {{ $log->user->name ?? 'System/Unknown' }}
                    <div class="text-xs text-gray-500">{{ $log->user->email ?? '' }}</div>
                </td>
                <td class="p-4 text-sm font-mono text-indigo-600">
                    {{ $log->action }}
                </td>
                <td class="p-4 text-sm text-gray-600">
                    {{ $log->description }}
                </td>
                <td class="p-4 text-sm text-gray-500 font-mono">
                    {{ $log->ip_address }}
                </td>
                <td class="p-4 text-sm text-gray-500">
                    {{ $log->created_at->format('M d, Y H:i:s') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    No activity logs found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
</div>
@endsection
