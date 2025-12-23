@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto px-3 md:px-0">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-2">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Add New Product</h2>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back to Products</a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-4 md:p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Brand -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                <select name="brand_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SKU (Auto-generated if empty)</label>
                <input type="text" name="sku" value="{{ old('sku') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price *</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Compare At Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Compare At Price</label>
                <input type="number" name="compare_at_price" value="{{ old('compare_at_price') }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Cost Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price</label>
                <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Barcode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                <input type="text" name="barcode" value="{{ old('barcode') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Short Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                <textarea name="short_description" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('short_description') }}</textarea>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
            </div>

            <!-- Inventory -->
            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="track_inventory" value="1" {{ old('track_inventory') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">Track Inventory</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 10) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Images -->
            <div class="md:col-span-2 border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <p class="text-sm text-gray-500 mt-1">You can select multiple images. First image will be the primary image.</p>
            </div>

            <!-- Status -->
            <div class="md:col-span-2 border-t pt-6">
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Featured Product</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="mt-6 md:mt-8 flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Create Product
            </button>
        </div>
    </form>
</div>
@endsection
