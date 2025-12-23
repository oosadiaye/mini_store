@extends('layouts.superadmin')

@section('header', 'Subscription Plans')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Plans</h2>
        <p class="text-gray-500 text-sm">Manage pricing tiers and features.</p>
    </div>
    <a href="{{ route('superadmin.plans.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        New Plan
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Name</th>
                <th class="p-4 border-b border-gray-100">Price</th>
                <th class="p-4 border-b border-gray-100">Duration</th>
                <th class="p-4 border-b border-gray-100">Status</th>
                <th class="p-4 border-b border-gray-100 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($plans as $plan)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4">
                    <div class="font-medium text-gray-900">{{ $plan->name }}</div>
                </td>
                <td class="p-4 text-gray-700 font-semibold">
                    {{ $plan->currency }} {{ number_format($plan->price, 2) }}
                </td>
                <td class="p-4 text-gray-500 text-sm">
                    {{ $plan->duration_months }} Month(s)
                </td>
                <td class="p-4">
                    <span class="px-2 py-1 text-xs rounded-full {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('superadmin.plans.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</a>
                    <form action="{{ route('superadmin.plans.destroy', $plan) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this plan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    No plans found. Create one to get started.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
