@props(['heroData', 'featuredProducts', 'categorySections'])

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- B2B Header --}}
    <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quick Order Portal</h1>
            <p class="text-gray-500 text-sm mt-1">Wholesale Pricing Active • Net 30 Terms Available</p>
        </div>
        <div class="flex gap-3">
             <button class="text-gray-600 hover:text-gray-900 font-medium text-sm">Download Catalog (PDF)</button>
             <button class="bg-gray-900 text-white px-4 py-2 rounded text-sm font-medium hover:bg-gray-800">Reorder Last</button>
        </div>
    </div>

    {{-- Quick Search Bar --}}
    <div class="mb-8 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <label class="block text-sm font-medium text-gray-700 mb-1">Quick Add by SKU or Name</label>
        <div class="flex gap-2">
            <input type="text" placeholder="Enter SKU, Product Name..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-[color:var(--brand-color)] focus:ring-[color:var(--brand-color)]">
            <input type="number" placeholder="Qty" class="w-24 rounded-md border-gray-300 shadow-sm focus:border-[color:var(--brand-color)] focus:ring-[color:var(--brand-color)]">
            <button class="bg-[color:var(--brand-color)] text-white px-4 py-2 rounded-md font-medium hover:bg-opacity-90">Add to Order</button>
        </div>
    </div>

    {{-- Tabular Product Lists --}}
    <div class="space-y-8">
        @foreach($categorySections as $section)
            <div class="bg-white border rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-6 py-3 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">{{ $section['category_name'] }}</h3>
                    <span class="text-xs text-gray-500 font-mono">ID: {{ $section['category_id'] }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Image</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Info</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">SKU</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Stock</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Price</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($section['products'] as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded object-cover border" src="{{ $product['image_url'] }}" alt="">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product['name'] }}</div>
                                        @if($product['is_flash_sale'])
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                              Sale
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        SKU-{{ $product['id'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $product['stock_quantity'] > 0 ? $product['stock_quantity'] . ' in stock' : 'Out of stock' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="text-gray-900">${{ number_format($product['price'], 2) }}</div>
                                        @if($product['compare_at_price'])
                                            <div class="text-gray-400 line-through text-xs">${{ number_format($product['compare_at_price'], 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <input type="number" min="1" value="1" class="w-16 rounded border-gray-300 py-1 text-sm focus:border-[color:var(--brand-color)] focus:ring-[color:var(--brand-color)]">
                                            <button class="text-[color:var(--brand-color)] hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded">Add</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No products available for quick order in this category.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>
