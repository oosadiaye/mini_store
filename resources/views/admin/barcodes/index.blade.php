@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-bold text-gray-800">Barcode Labels</h3>
        <p class="text-xs text-gray-500">Select products and quantities to generate printable labels.</p>
    </div>
    
    <form action="{{ route('admin.barcodes.print') }}" method="POST" target="_blank">
        @csrf
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 w-16">Select</th>
                        <th class="px-6 py-3">Product Name</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Barcode Value</th>
                        <th class="px-6 py-3 w-32">Print Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $index => $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3">
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $product->id }}">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                                onchange="toggleQty(this, {{ $index }})">
                        </td>
                        <td class="px-6 py-3 font-medium text-gray-900">
                            {{ $product->name }}
                        </td>
                        <td class="px-6 py-3 font-mono text-gray-500">{{ $product->sku }}</td>
                        <td class="px-6 py-3 font-mono text-xs text-gray-400">
                            {{ $product->barcode ?? $product->sku }}
                        </td>
                        <td class="px-6 py-3">
                            <input type="number" name="items[{{ $index }}][quantity]" value="0" min="0" 
                                id="qty-{{ $index }}"
                                class="w-20 rounded border-gray-300 text-sm disabled:bg-gray-100 disabled:text-gray-400" disabled>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center sticky bottom-0">
             <div>
                {{ $products->links() }}
             </div>
             <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded font-medium hover:bg-indigo-700 shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Generate Labels
             </button>
        </div>
    </form>
</div>

<script>
    function toggleQty(checkbox, index) {
        const qtyInput = document.getElementById('qty-' + index);
        qtyInput.disabled = !checkbox.checked;
        if (checkbox.checked) {
            qtyInput.value = 1; // Default to 1
            qtyInput.focus();
        } else {
            qtyInput.value = 0;
        }
    }
</script>
@endsection
