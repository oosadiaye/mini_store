@extends('admin.layout')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Tax Codes</h2>
            <p class="text-gray-500 mt-1">Manage tax codes with auto-generated GL accounts</p>
        </div>
        <a href="{{ route('admin.tax-codes.create') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
            + Add Tax Code
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Rate</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sales GL</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Purchase GL</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($taxCodes as $taxCode)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $taxCode->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $taxCode->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">{{ $taxCode->rate }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $taxCode->type === 'sales' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $taxCode->type === 'purchase' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $taxCode->type === 'both' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ ucfirst($taxCode->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $taxCode->sales_tax_gl_account ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $taxCode->purchase_tax_gl_account ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $taxCode->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $taxCode->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.tax-codes.edit', $taxCode) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.tax-codes.destroy', $taxCode) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <p class="text-lg font-semibold">No tax codes found</p>
                            <p class="mt-2">Create your first tax code to get started</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
