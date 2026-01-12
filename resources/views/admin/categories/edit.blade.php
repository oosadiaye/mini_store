@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Category: {{ $category->name }}</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-900">← Back</a>
    </div>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                    class="w-full px-4 py-2 border-2 @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Parent Category</label>
                <select name="parent_id" class="w-full px-4 py-2 border-2 @error('parent_id') border-red-500 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">None (Root Category)</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category Image</label>
                @if($category->image)
                    <div class="mb-2">
                        <img src="{{ tenant_asset($category->image) }}" class="h-20 w-20 object-cover rounded border border-gray-200">
                    </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                @if($category->image)
                    <p class="text-sm text-gray-500 mt-1">Upload to replace existing image.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-center space-x-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                        class="rounded border-2 border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="show_on_storefront" value="1" {{ old('show_on_storefront', $category->show_on_storefront) ? 'checked' : '' }}
                        class="rounded border-2 border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                    <span class="text-sm font-medium text-gray-700">Show on Storefront</span>
                </label>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Update Category
            </button>
        </div>
    </form>

    <div class="mt-12 border-t pt-8">
        <h3 class="text-lg font-bold text-red-600 mb-4">Danger Zone</h3>
        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? Products in this category will be unassigned.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 border-2 border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                Delete Category
            </button>
        </form>
    </div>
</div>
@endsection
