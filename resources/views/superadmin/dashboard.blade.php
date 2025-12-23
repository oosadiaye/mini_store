@extends('layouts.superadmin')

@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
        <div class="flex items-center">
            <div class="p-3 bg-indigo-100 rounded-full mr-4">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Tenants</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalTenants ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">Monthly Revenue</p>
                <p class="text-2xl font-bold text-gray-800">$0.00</p>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full mr-4">
                 <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm font-medium">New Signups</p>
                <p class="text-2xl font-bold text-gray-800">0</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
    <div class="text-gray-500 text-sm text-center py-8">
        No recent activity logs found.
    </div>
</div>
@endsection
