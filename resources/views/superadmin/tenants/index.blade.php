@extends('layouts.superadmin')

@section('header', 'Tenant Management')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Tenants</h2>
        <p class="text-gray-500 text-sm">Manage enrolled stores and access their dashboards.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">ID</th>
                <th class="p-4 border-b border-gray-100">Store Name</th>
                <th class="p-4 border-b border-gray-100">Domains</th>
                <th class="p-4 border-b border-gray-100">Created At</th>
                <th class="p-4 border-b border-gray-100 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tenants as $tenant)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 text-gray-500 text-xs font-mono">
                    {{ $tenant->id }}
                </td>
                <td class="p-4">
                    <div class="font-medium text-gray-900">{{ $tenant->data['store_name'] ?? $tenant->name ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-500">{{ $tenant->email ?? '' }}</div>
                </td>
                <td class="p-4 text-gray-700 text-sm">
                    @foreach($tenant->domains as $domain)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $domain->domain }}
                        </span>
                    @endforeach
                </td>
                <td class="p-4 text-gray-500 text-sm">
                    {{ $tenant->created_at->format('M d, Y') }}
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('superadmin.tenants.impersonate', $tenant) }}" target="_blank" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        Login as Client
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    No tenants found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
