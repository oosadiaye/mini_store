@extends('admin.layout')

@php
    $routes = [
        "base" => route("admin.products.index"),
        "create" => route("admin.products.create"),
        "export" => route("admin.products.export"),
        "template" => route("admin.products.template"),
        "import" => route("admin.products.import"),
        "bulkAction" => route("admin.products.bulk-action"),
        "bulkUpload" => route("admin.products.bulk-upload.index")
    ];
@endphp

@section('content')
    <product-list 
        :products='@json($products)' 
        currency-symbol="{{ app('tenant')->data['currency_symbol'] ?? '₦' }}"
        csrf-token="{{ csrf_token() }}"
        tenant-slug="{{ app('tenant')->slug }}"
        :routes='@json($routes)'
    >
        <template #filters>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." 
                    class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                
                <select name="category_id" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                </select>

                <select name="warehouse_id" class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                            {{ $wh->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition">
                    Filter
                </button>
            </form>
        </template>

        <template #pagination>
            {{ $products->links() }}
        </template>
    </product-list>
@endsection
