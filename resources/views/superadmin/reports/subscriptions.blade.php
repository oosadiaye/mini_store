@extends('layouts.superadmin')

@section('header', 'Subscription Report')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header/Filter -->
    <div class="p-6 border-b border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="text-lg font-semibold text-slate-800">Subscribed Tenants</h2>
        
        <form method="GET" class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <!-- Search / Tenant Filter -->
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Filter by Tenant..." 
                   class="rounded-lg border-slate-300 text-sm focus:ring-blue-500 focus:border-blue-500 w-full md:w-48">
            
            <!-- Date Type -->
            <select name="date_type" class="rounded-lg border-slate-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Joined Date</option>
                <option value="subscription_end" {{ request('date_type') == 'subscription_end' ? 'selected' : '' }}>Subscription End</option>
                <option value="trial_end" {{ request('date_type') == 'trial_end' ? 'selected' : '' }}>Trial End</option>
            </select>

            <!-- Date Range -->
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-slate-300 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="From">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-slate-300 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="To">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'date_from', 'date_to']))
                <a href="{{ route('superadmin.reports.subscriptions') }}" class="text-slate-500 hover:text-slate-700 text-sm flex items-center px-2">Clear</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-800 uppercase font-medium">
                <tr>
                    <th class="px-6 py-4">Tenant Store</th>
                    <th class="px-6 py-4">Contact Person</th>
                    <th class="px-6 py-4">Plan / Status</th>
                    <th class="px-6 py-4">Subscription End</th>
                    <th class="px-6 py-4">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($tenants as $tenant)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-900">{{ $tenant->name }}</div>
                        <div class="text-xs text-slate-500">{{ $tenant->domains->first()->domain ?? $tenant->slug . '.' . config('app.url_base', 'localhost') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $admin = $tenant->users->first();
                        @endphp
                        @if($admin)
                            <div class="font-medium text-slate-900">{{ $admin->name }}</div>
                            <div class="text-xs text-slate-500">{{ $admin->email }}</div>
                            <div class="text-xs text-slate-500">{{ $admin->phone }}</div>
                        @else
                            <span class="text-slate-400 italic">No admin found</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            @if($tenant->currentPlan)
                                <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs inline-block w-fit font-medium">
                                    {{ $tenant->currentPlan->name }}
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs inline-block w-fit">Free/None</span>
                            @endif
                            
                            @if($tenant->onTrial())
                                <span class="text-xs text-green-600 font-medium">Trial Active</span>
                            @elseif($tenant->subscriptionActive())
                                <span class="text-xs text-blue-600 font-medium">Active</span>
                            @else
                                <span class="text-xs text-red-500 font-medium">Expired/Inactive</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($tenant->subscription_ends_at)
                            {{ $tenant->subscription_ends_at->format('M d, Y') }}
                            <div class="text-xs text-slate-400">({{ $tenant->subscription_ends_at->diffForHumans() }})</div>
                        @elseif($tenant->trial_ends_at)
                             {{ $tenant->trial_ends_at->format('M d, Y') }} (Trial)
                             <div class="text-xs text-slate-400">({{ $tenant->trial_ends_at->diffForHumans() }})</div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        {{ $tenant->created_at->format('M d, Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        No subscriptions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-6 border-t border-slate-200">
        {{ $tenants->links() }}
    </div>
</div>
@endsection
