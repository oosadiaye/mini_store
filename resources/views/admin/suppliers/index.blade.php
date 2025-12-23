@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Suppliers</h2>
    <a href="{{ route('admin.suppliers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition">
        + Add Supplier
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($suppliers as $supplier)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $supplier->company_name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($supplier->email)
                            <div class="text-sm text-gray-900">{{ $supplier->email }}</div>
                        @endif
                        @if($supplier->phone)
                            <div class="text-sm text-gray-500">{{ $supplier->phone }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $supplier->city ?? '-' }}
                        @if($supplier->country)
                            , {{ $supplier->country }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($supplier->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.suppliers.ledger', $supplier) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Ledger</a>
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('admin.suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        No suppliers found. <a href="{{ route('admin.suppliers.create') }}" class="text-indigo-600 hover:underline">Create your first supplier</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $suppliers->links() }}
</div>
@endsection
