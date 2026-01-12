@extends('admin.layout')

@php
    $routes = [
        "index" => route("admin.products.index"),
        "storeCategory" => route("admin.categories.store"),
        "storeBrand" => route("admin.brands.store"),
        "deleteImageBase" => url("admin/product-images")
    ];
@endphp

@section('content')
    <product-form
        is-edit
        :initial-product='@json($product)'
        :categories='@json($categories)'
        :brands='@json($brands)'
        :warehouses='@json($warehouses)'
        :existing-images='@json($product->images)'
        :old-input='@json(session()->getOldInput())'
        :errors='@json($errors->getMessages())'
        action-url="{{ route('admin.products.update', $product->id) }}"
        csrf-token="{{ csrf_token() }}"
        :routes='@json($routes)'
    ></product-form>

    <!-- Variants Section -->
    <div class="max-w-4xl mx-auto mt-6">
        <div class="bg-white rounded-lg shadow p-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Variants</h3>
            
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ app('tenant')->data['currency_symbol'] ?? '$' }}{{ number_format($variant->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $variant->stock_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <button form="delete-variant-{{ $variant->id }}" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-sm">No variants configured.</p>
            @endif

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-4">
                <h4 class="text-sm font-bold text-gray-700 mb-3">Add New Variant</h4>
                <p class="text-xs text-gray-500">Variant creation is currently unavailable in this version.</p>
            </div>
        </div>
    </div>

    <!-- Product Bundle / Combos Section -->
    <div class="max-w-4xl mx-auto mt-6 bg-white rounded-lg shadow p-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Product Bundle / Combos</h3>
        <p class="text-sm text-gray-500 mb-6">Link other products to this one to create a bundle (e.g. "Frequently Bought Together").</p>

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
                                {{ $combo->pivot->discount_amount ? (app('tenant')->data['currency_symbol'] ?? '$') . number_format($combo->pivot->discount_amount, 2) : '-' }}
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
    </div>

    <!-- Danger Zone -->
    <div class="max-w-4xl mx-auto mt-6 bg-white rounded-lg shadow p-8 border-t-4 border-red-500">
        <h3 class="text-lg font-bold text-red-600 mb-4">Danger Zone</h3>
        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 border-2 border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                Delete Product
            </button>
        </form>
    </div>

    <!-- Hidden Delete Forms for Variants -->
    @foreach($product->variants as $variant)
        <form id="delete-variant-{{ $variant->id }}" action="{{ route('admin.products.variants.destroy', $variant->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
@endsection
