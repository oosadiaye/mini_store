@extends('layouts.superadmin')

@section('header', 'Tenant Details')

@section('content')
<div class="space-y-6">
    <!-- Tenant Overview -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $tenant->name }}</h3>
                <p class="text-sm text-gray-500">{{ $tenant->email }}</p>
                <div class="mt-2 flex items-center space-x-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tenant->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $tenant->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="text-sm text-gray-500">Plan: {{ $tenant->currentPlan ? $tenant->currentPlan->name : ($tenant->plan ?? 'No Plan') }}</span>
                </div>
            </div>
            <div class="flex space-x-3">
                 <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2 px-4 rounded-md shadow-sm transition">
                    Edit Tenant
                </a>
                <a href="{{ route('superadmin.tenants.impersonate', $tenant) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition">
                    Quick Impersonate
                </a>
            </div>
        </div>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-4 rounded-md">
                <span class="block text-xs text-gray-500 uppercase">Subdomain</span>
                <span class="block text-lg font-mono font-medium text-gray-900">{{ $tenant->slug }}</span>
            </div>
            <div class="bg-gray-50 p-4 rounded-md">
                <span class="block text-xs text-gray-500 uppercase">Subscription Ends</span>
                <span class="block text-lg font-medium text-gray-900">
                    {{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('M d, Y') : 'N/A' }}
                </span>
            </div>
            <div class="bg-gray-50 p-4 rounded-md">
                <span class="block text-xs text-gray-500 uppercase">Trial Ends</span>
                <span class="block text-lg font-medium text-gray-900">
                    {{ $tenant->trial_ends_at ? $tenant->trial_ends_at->format('M d, Y') : 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Tenant Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Tenant Users</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Joined</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            {{ $user->role ?? 'User' }}
                        </td>
                        <td class="px-6 py-4">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('superadmin.users.impersonate', $user->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">Impersonate</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No users found for this tenant.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
