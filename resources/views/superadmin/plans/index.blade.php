@extends('layouts.superadmin')

@section('header', 'Subscription Plans')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Plans</h2>
        <p class="text-gray-500 text-sm">Manage subscription packages and their features.</p>
    </div>
    <a href="{{ route('superadmin.plans.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border-2 border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Create Plan
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Name</th>
                <th class="p-4 border-b border-gray-100">Price</th>
                <th class="p-4 border-b border-gray-100">Duration</th>
                <th class="p-4 border-b border-gray-100">Features</th>
                <th class="p-4 border-b border-gray-100">Status</th>
                <th class="p-4 border-b border-gray-100 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($plans as $plan)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 font-medium text-gray-900">
                    {{ $plan->name }}
                </td>
                <td class="p-4 text-gray-700">
                    ₦{{ number_format($plan->price, 2) }}
                </td>
                <td class="p-4 text-gray-700">
                    {{ $plan->duration_days }} Days
                </td>
                <td class="p-4 text-gray-500 text-sm">
                    <div class="flex flex-wrap gap-1">
                        @if($plan->features)
                            @foreach($plan->features as $feature)
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">{{ ucfirst($feature) }}</span>
                            @endforeach
                        @else
                            <span class="text-gray-400 italic">No features</span>
                        @endif
                    </div>
                </td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('superadmin.plans.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Edit</a>
                    <form action="{{ route('superadmin.plans.destroy', $plan) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium text-sm ml-2">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-8 text-center text-gray-500">
                    No plans found. Create one to get started.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
