@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Product: {{ $product->name }}</h2>
        <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:text-gray-900">← Back to Products</a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Selling Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Selling Price *</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Flash Sale Config -->
            <div>
                 <label class="block text-sm font-medium text-red-600 mb-2">⚡ Flash Sale Price</label>
                 <input type="number" name="flash_sale_price" value="{{ old('flash_sale_price', $product->flash_sale_price) }}" step="0.01" min="0"
                     class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-red-50" placeholder="Optional override">
            </div>

            <div>
                 <label class="block text-sm font-medium text-red-600 mb-2">⚡ Flash Sale Ends At</label>
                 <input type="datetime-local" name="flash_sale_end_date" value="{{ old('flash_sale_end_date', $product->flash_sale_end_date ? \Carbon\Carbon::parse($product->flash_sale_end_date)->format('Y-m-d\TH:i') : '') }}"
                     class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 bg-red-50">
            </div>

            <!-- Compare At Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Compare At Price</label>
                <input type="number" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Cost Price -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price</label>
                <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Barcode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Barcode</label>
                <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Short Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Short Description</label>
                <textarea name="short_description" rows="2"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('short_description', $product->short_description) }}</textarea>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Inventory -->
            <div class="md:col-span-2 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Inventory Management</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="track_inventory" value="1" {{ old('track_inventory', $product->track_inventory) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">Track Inventory</span>
                        </label>
                    </div>
                    
                    @if($warehouses->count() > 0)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-3">Stock by Warehouse</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($warehouses as $warehouse)
                                    @php
                                        $currentStock = $product->warehouses->firstWhere('id', $warehouse->id);
                                        $quantity = $currentStock ? $currentStock->pivot->quantity : 0;
                                    @endphp
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $warehouse->name }}
                                            <span class="text-xs text-gray-500">({{ $warehouse->code }})</span>
                                        </label>
                                        <input type="number" 
                                               name="warehouse_stock[{{ $warehouse->id }}]" 
                                               value="{{ old('warehouse_stock.' . $warehouse->id, $quantity) }}" 
                                               min="0"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 p-3 bg-indigo-50 rounded border border-indigo-200">
                                <p class="text-sm text-indigo-800">
                                    <strong>Total Stock:</strong> 
                                    <span id="total-stock">{{ $product->stock_quantity }}</span> units
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <p class="text-sm text-yellow-800">
                                No warehouses configured. Please create warehouses first to manage stock.
                            </p>
                        </div>
                        <!-- Fallback to simple stock input if no warehouses -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Existing Images -->
            @if($product->images->count() > 0)
            <div class="md:col-span-2 border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($product->images as $image)
                    <div class="relative group">
                        <img src="{{ $image->url }}" class="h-32 w-full object-cover rounded-lg border border-gray-200">
                        @if($image->is_primary)
                            <span class="absolute top-2 left-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded">Primary</span>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center rounded-lg">
                             <!-- Delete Image Button -->
                            <button type="button" onclick="if(confirm('Delete this image?')) document.getElementById('delete-img-{{ $image->id }}').submit()" class="text-white hover:text-red-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- New Images -->
            <div class="md:col-span-2 border-t pt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Add More Images</label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <!-- Status -->
            <div class="md:col-span-2 border-t pt-6">
                <div class="flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                        <span class="text-sm font-medium text-gray-700">Featured Product</span>
                    </label>
                </div>
            </div>
            </div>
        </div>

        <!-- Variants Section -->
        <div class="md:col-span-2 border-t pt-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Variants</h3>
            
            <!-- Existing Variants Table -->
            @if($product->variants->count() > 0)
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attributes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($product->variants as $variant)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $variant->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $variant->sku }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($variant->attributes)
                                        @foreach($variant->attributes as $key => $val)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $key }}: {{ $val }}
                                            </span>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($variant->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $variant->stock_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button form="delete-variant-{{ $variant->id }}" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Add Variant Form (Alpine.js) -->
            <div x-data="{ 
                attributes: [{key: 'Size', value: ''}], 
                addAttr() { this.attributes.push({key: '', value: ''}); },
                removeAttr(index) { this.attributes.splice(index, 1); }
            }" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h4 class="text-sm font-bold text-gray-700 mb-3">Add New Variant</h4>
                <!-- NOTE: This form is separate from the main update form -->
            </div>
        </div>

        <!-- Submit -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Update Product
            </button>
        </div>
    </form>

    <!-- Separate Add Variant Form -->
    <form action="{{ route('admin.products.variants.store', $product->id) }}" method="POST" class="mt-4 bg-white rounded-lg shadow p-8">
        @csrf
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Add Variant</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Variant Name</label>
                <input type="text" name="name" placeholder="e.g. Small Red" required class="w-full px-3 py-2 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <input type="number" name="price" step="0.01" value="{{ $product->price }}" required class="w-full px-3 py-2 border rounded-md">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                <input type="number" name="stock_quantity" value="10" required class="w-full px-3 py-2 border rounded-md">
            </div>
             <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Optional)</label>
                <input type="text" name="sku" class="w-full px-3 py-2 border rounded-md">
            </div>
        </div>
        
        <div x-data="{ rows: [{key: 'Size', value: ''}] }" class="mt-4">
             <label class="block text-sm font-medium text-gray-700 mb-2">Attributes</label>
             <template x-for="(row, index) in rows" :key="index">
                <div class="flex gap-2 mb-2">
                    <input type="text" :name="`attributes[${index}][key]`" x-model="row.key" placeholder="Key (e.g. Color)" class="w-1/3 px-3 py-2 border rounded-md">
                    <input type="text" :name="`attributes[${index}][value]`" x-model="row.value" placeholder="Value (e.g. Red)" class="w-1/3 px-3 py-2 border rounded-md">
                    <button type="button" @click="rows.splice(index, 1)" class="text-red-500 hover:text-red-700" x-show="rows.length > 1">×</button>
                </div>
             </template>
             <button type="button" @click="rows.push({key: '', value: ''})" class="text-sm text-indigo-600 hover:text-indigo-800">+ Add Attribute</button>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Add Variant</button>
        </div>
    </form>

    <!-- Product Bundle / Combos Section -->
    <div class="mt-6 bg-white rounded-lg shadow p-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Bundle / Combos</h3>
        <p class="text-sm text-gray-500 mb-6">Link other products to this one to create a bundle (e.g. "Frequently Bought Together").</p>

        <!-- List Existing Combos -->
        @if($product->combos->count() > 0)
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Discount</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($product->combos as $combo)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $combo->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $combo->sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $combo->pivot->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $combo->pivot->discount_amount ? '$' . number_format($combo->pivot->discount_amount, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <form action="{{ route('admin.products.combos.destroy', [$product->id, $combo->id]) }}" method="POST" onsubmit="return confirm('Remove from combo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Add Combo Form -->
        <form action="{{ route('admin.products.combos.store', $product->id) }}" method="POST" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            @csrf
            <h4 class="text-sm font-bold text-gray-700 mb-3">Add Product to Bundle</h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Child Product SKU</label>
                    <input type="text" name="child_sku" placeholder="Enter SKU of product to add" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" value="1" min="1" required class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Amount ($)</label>
                    <input type="number" name="discount_amount" value="0" min="0" step="0.01" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    <i class="fas fa-plus mr-1"></i> Add to Bundle
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Variant Forms -->
    @foreach($product->variants as $variant)
        <form id="delete-variant-{{ $variant->id }}" action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <div class="mt-12 border-t pt-8">
        <h3 class="text-lg font-bold text-red-600 mb-4">Danger Zone</h3>
        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                Delete Product
            </button>
        </form>
    </div>
</div>
@endsection
