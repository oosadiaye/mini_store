@extends('admin.layout')

@section('title', 'Renters')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Renters Management</h1>
        <a href="{{ route('admin.renters.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            + Add Renter
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." class="flex-1 rounded-md border-gray-300">
            <select name="status" class="rounded-md border-gray-300">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">Filter</button>
            <a href="{{ route('admin.renters.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Clear</a>
        </form>
    </div>

    <!-- Renters Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rental Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contract Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outstanding Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($renters as $renter)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $renter->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $renter->email }}</div>
                        <div>{{ $renter->phone }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        ₦{{ number_format($renter->rental_amount, 2) }}
                        <span class="text-xs text-gray-500">/{{ $renter->payment_frequency }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $renter->contract_start_date?->format('M d, Y') }} - {{ $renter->contract_end_date?->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $renter->outstanding_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₦{{ number_format($renter->outstanding_balance, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $renter->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $renter->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $renter->status == 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($renter->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.renters.show', $renter) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                        <a href="{{ route('admin.renters.edit', $renter) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No renters found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $renters->links() }}
    </div>
</div>
@endsection
