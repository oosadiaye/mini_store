@extends('admin.layout')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Edit Purchase Order</h3>
                <p class="text-xs text-gray-500">Update order details. Status: <span class="uppercase font-bold">{{ $purchaseOrder->status }}</span></p>
            </div>
            
        </div>
        
        <form action="{{ route('admin.purchase-orders.update', $purchaseOrder->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select name="supplier_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $purchaseOrder->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Warehouse -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destination Warehouse</label>
                    <select name="warehouse_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ $purchaseOrder->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Dates -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date</label>
                    <input type="date" name="order_date" value="{{ $purchaseOrder->order_date->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery</label>
                    <input type="date" name="expected_delivery_date" value="{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $purchaseOrder->notes }}</textarea>
            </div>

            <!-- Items Section -->
            <div class="border-t border-gray-100 pt-6">
                <h4 class="text-base font-bold text-gray-800 mb-4">Order Items</h4>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="items-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Unit Cost</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Tax Code</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="items-body">
                            @foreach($purchaseOrder->items as $index => $item)
                            <tr class="item-row">
                                <td class="px-4 py-2">
                                    <select name="items[{{ $index }}][product_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm product-select" onchange="updateCost(this)">
                                        <option value="" data-cost="0">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-cost="{{ $product->cost_price ?? 0 }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[{{ $index }}][quantity]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm quantity-input" min="1" value="{{ $item->quantity_ordered }}" oninput="calculateRowTotal(this)">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" name="items[{{ $index }}][unit_cost]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm cost-input" step="0.01" min="0" value="{{ $item->unit_cost }}" oninput="calculateRowTotal(this)">
                                </td>
                                <td class="px-4 py-2">
                                     <select name="items[{{ $index }}][tax_code_id]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm tax-select" onchange="calculateRowTotal(this)">
                                        <option value="" data-rate="0">No Tax</option>
                                        @php $taxCodes = \App\Models\TaxCode::active()->get(); @endphp
                                        @foreach($taxCodes as $taxCode)
                                            <option value="{{ $taxCode->id }}" data-rate="{{ $taxCode->rate }}" {{ $item->tax_code_id == $taxCode->id ? 'selected' : '' }}>{{ $taxCode->name }} ({{ $taxCode->rate }}%)</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="text" class="w-full bg-gray-50 rounded-md border-gray-300 shadow-sm sm:text-sm text-gray-500 row-total" readonly value="{{ number_format($item->total + ($item->tax_amount ?? 0), 2, '.', '') }}">
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <button type="button" class="text-red-600 hover:text-red-900" onclick="removeRow(this)">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="px-4 py-3">
                                    <button type="button" onclick="addItemRow()" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                                        + Add Another Item
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <a href="{{ route('admin.purchase-orders.show', $purchaseOrder->id) }}" class="mr-3 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-50 shadow-sm">Cancel</a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 shadow-sm">
                    Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let rowCount = {{ $purchaseOrder->items->count() + 1 }};

function updateCost(select) {
    const cost = select.options[select.selectedIndex].dataset.cost;
    const row = select.closest('tr');
    row.querySelector('.cost-input').value = cost;
    calculateRowTotal(select);
}

function calculateRowTotal(element) {
    const row = element.closest('tr');
    const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    
    // Calculate Tax
    const taxSelect = row.querySelector('.tax-select');
    const taxRate = taxSelect && taxSelect.selectedIndex > 0 
        ? parseFloat(taxSelect.options[taxSelect.selectedIndex].dataset.rate) 
        : 0;
    
    const subtotal = qty * cost;
    const taxAmount = subtotal * (taxRate / 100);
    const total = subtotal + taxAmount;
    
    row.querySelector('.row-total').value = total.toFixed(2);
}

function addItemRow() {
    const tbody = document.getElementById('items-body');
    // If no rows, we need a template. Usually there is at least one.
    // If empty, we need hardcoded HTML or clone a hidden template. 
    // Assuming at least one row exists or we clone from the loop.
    
    // Better: Clone the FIRST row if it exists, clean it.
    let template = tbody.querySelector('.item-row');
    
    // Fallback if no items initially (should be rare in update but possible if emptied)
    if (!template) {
        // Need to reconstruct row. Simplified approach: Reload page or assume items exist.
        // For robustness, returning early if no template found (should not happen if validation requires items).
        return;
    }

    const newRow = template.cloneNode(true);
    
    // Clear values
    newRow.querySelector('select').selectedIndex = 0;
    newRow.querySelector('.quantity-input').value = 1;
    newRow.querySelector('.cost-input').value = 0;
    newRow.querySelector('.row-total').value = '0.00';
    
    // Update names
    newRow.querySelector('select').name = `items[${rowCount}][product_id]`;
    newRow.querySelector('.quantity-input').name = `items[${rowCount}][quantity]`;
    newRow.querySelector('.cost-input').name = `items[${rowCount}][unit_cost]`;
    
    tbody.appendChild(newRow);
    rowCount++;
}

function removeRow(btn) {
    const tbody = document.getElementById('items-body');
    if (tbody.children.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('You must have at least one item.');
    }
}
</script>
@endsection
