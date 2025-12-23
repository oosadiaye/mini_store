@extends('admin.layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Banners</h2>
    <a href="{{ route('admin.banners.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + Add Banner
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wide">
            <tr>
                <th class="p-4 border-b border-gray-100">Image</th>
                <th class="p-4 border-b border-gray-100">Title</th>
                <th class="p-4 border-b border-gray-100">Position</th>
                <th class="p-4 border-b border-gray-100">Status</th>
                <th class="p-4 border-b border-gray-100 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($banners as $banner)
            <tr class="hover:bg-gray-50 transition">
                <td class="p-4 w-24">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="h-12 w-20 object-cover rounded bg-gray-100">
                </td>
                <td class="p-4 text-sm font-medium text-gray-900">{{ $banner->title }}</td>
                <td class="p-4 text-sm text-gray-600">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ ucfirst(str_replace('_', ' ', $banner->position)) }}
                    </span>
                </td>
                <td class="p-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $banner->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $banner->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td class="p-4 text-center">
                    <div class="flex items-center justify-center space-x-3">
                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-gray-500">
                    No banners found. Create one to get started!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
