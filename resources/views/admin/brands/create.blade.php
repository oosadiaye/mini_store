@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add Brand</h1>
        <a href="{{ route('admin.brands.index') }}" class="text-gray-600 hover:text-gray-800">
            &larr; Back to Brands
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
        <form action="{{ route('admin.brands.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Brand Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Logo (Recommended: Square or Landscape PNG)</label>
                <input type="file" name="logo" class="w-full border rounded px-3 py-2 bg-gray-50">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Website URL (Optional)</label>
                <input type="url" name="url" value="{{ old('url') }}" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com">
            </div>

            <div class="flex gap-4 mb-6">
                <div class="w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="w-1/2 pt-8">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="form-checkbox h-5 w-5 text-blue-600">
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700">
                    Create Brand
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
