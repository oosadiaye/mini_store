@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Add New Category</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-900">← Back</a>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
        @csrf

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Parent Category</label>
                <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">None (Root Category)</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Image</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center space-x-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="show_on_storefront" value="1" {{ old('show_on_storefront', true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                    <span class="text-sm font-medium text-gray-700">Show on Storefront</span>
                </label>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Create Category
            </button>
        </div>
    </form>
</div>
@endsection
