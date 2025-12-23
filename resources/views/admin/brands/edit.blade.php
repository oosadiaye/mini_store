@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Brand</h1>
        <a href="{{ route('admin.brands.index') }}" class="text-gray-600 hover:text-gray-800">
            &larr; Back to Brands
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
        <form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Brand Name</label>
                <input type="text" name="name" value="{{ old('name', $brand->name) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Logo</label>
                @if($brand->logo)
                    <div class="mb-2">
                        <img src="{{ $brand->logo_url }}" class="h-20 w-auto border rounded p-2 bg-gray-50">
                    </div>
                @endif
                <input type="file" name="logo" class="w-full border rounded px-3 py-2 bg-gray-50">
                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current logo.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Website URL</label>
                <input type="url" name="url" value="{{ old('url', $brand->url) }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex gap-4 mb-6">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $brand->sort_order) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="w-1/2 pt-8">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $brand->is_active ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600">
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                 <a href="{{ route('admin.brands.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded font-bold hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                    Update Brand
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
