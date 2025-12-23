@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Brands</h1>
    <a href="{{ route('admin.brands.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        <i class="fas fa-plus mr-2"></i> Add Brand
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sort</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($brands as $brand)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $brand->sort_order }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($brand->logo)
                        <img src="{{ $brand->logo_url }}" class="h-10 w-10 object-contain rounded border bg-gray-50">
                    @else
                        <div class="h-10 w-10 bg-gray-100 rounded border flex items-center justify-center text-gray-400">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $brand->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                    @if($brand->url)
                        <a href="{{ $brand->url }}" target="_blank" class="text-blue-600 hover:underline">{{ Str::limit($brand->url, 30) }}</a>
                    @else
                        -
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $brand->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.brands.edit', $brand) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No brands found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t">
        {{ $brands->links() }}
    </div>
</div>
@endsection
