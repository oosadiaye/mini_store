@extends('admin.layout')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
            <div>
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Create Purchase Order</h3>
                <p class="text-sm text-gray-500 font-medium mt-1">Fill in the details below to create a new order.</p>
            </div>
            <a href="{{ route('admin.purchase-orders.index') }}" class="text-gray-500 hover:text-gray-700 font-bold transition">Cancel</a>
        </div>
        
        <form action="{{ route('admin.purchase-orders.store') }}" method="POST" class="p-8 space-y-8" 
              x-data="purchaseOrderForm()">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Supplier -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Supplier</label>
                    <select name="supplier_id" required class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-semibold text-gray-800 transition-all">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Warehouse -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Destination Warehouse <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" required class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-semibold text-gray-800 transition-all">
                        <option value="">Select Warehouse</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Dates -->
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Order Date</label>
                    <input type="date" name="order_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-semibold text-gray-800 transition-all">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Expected Delivery</label>
                    <input type="date" name="expected_delivery_date" class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-semibold text-gray-800 transition-all">
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider">Notes</label>
                <textarea name="notes" rows="3" class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium text-gray-800 transition-all" placeholder="Enter any specific instructions..."></textarea>
            </div>

            <!-- Items Section -->
            <div class="border-t border-gray-100 pt-8">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-xl font-bold text-gray-900">Order Items</h4>
                    <button type="button" @click="addItem()" class="bg-indigo-50 text-indigo-700 px-4 py-2 rounded-xl font-bold hover:bg-indigo-100 hover:shadow-md transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Item
                    </button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid grid-cols-12 gap-4 items-start bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <!-- Product -->
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Product</label>
                                <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="updateCost(index)" required class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-semibold">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-cost="{{ $product->cost_price ?? 0 }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Qty -->
                            <div class="col-span-4 md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Qty</label>
                                <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="calculateItemTax(index)" required class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-bold text-center" min="1">
                            </div>

                            <!-- Cost -->
                            <div class="col-span-4 md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Unit Cost</label>
                                <input type="number" :name="`items[${index}][unit_cost]`" x-model.number="item.unit_cost" @input="calculateItemTax(index)" required class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-bold text-center" step="0.01" min="0">
                            </div>
                            
                            <!-- Tax Code -->
                            <div class="col-span-4 md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tax Code</label>
                                <select :name="`items[${index}][tax_code_id]`" x-model.number="item.tax_code_id" @change="calculateItemTax(index)" class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm font-semibold">
                                    <option value="">No Tax</option>
                                    @foreach($taxCodes as $taxCode)
                                        <option value="{{ $taxCode->id }}" data-rate="{{ $taxCode->rate }}">{{ $taxCode->name }} ({{ $taxCode->rate }}%)</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Total -->
                            <div class="col-span-10 md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Total</label>
                                <div class="w-full py-2 px-3 bg-white rounded-xl border border-gray-200 text-sm font-bold text-right text-gray-700" x-text="getItemTotal(index).toFixed(2)"></div>
                            </div>

                            <!-- Remove -->
                            <div class="col-span-2 md:col-span-1 flex justify-end pt-6">
                                <button type="button" @click="items.length > 1 ? removeItem(index) : null" class="text-red-400 hover:text-red-600 p-2 hover:bg-red-50 rounded-lg transition" :class="{'opacity-50 cursor-not-allowed': items.length <= 1}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <!-- Summary Section -->
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-end">
                <div class="w-full md:w-1/3 bg-gray-50 rounded-2xl p-6 space-y-4 shadow-inner">
                    <div class="flex justify-between items-center text-sm font-medium text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-bold text-gray-800" x-text="subtotal.toFixed(2)"></span>
                    </div>
                    
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-sm font-medium text-gray-600">Discount</span>
                         <input type="number" name="discount" x-model.number="discount" class="w-32 rounded-lg border-2 border-gray-300 text-right text-sm font-bold p-1 focus:ring-2 focus:ring-indigo-500/20" placeholder="0.00">
                    </div>
                    
                    <div class="flex justify-between items-center gap-4">
                         <span class="text-sm font-medium text-gray-600">Tax (from items)</span>
                         <span class="text-sm font-bold text-gray-800" x-text="tax.toFixed(2)"></span>
                    </div>
                    
                    <div class="flex justify-between items-center gap-4">
                         <span class="text-sm font-medium text-gray-600">Shipping</span>
                         <input type="number" name="shipping" x-model.number="shipping" class="w-32 rounded-lg border-2 border-gray-300 text-right text-sm font-bold p-1 focus:ring-2 focus:ring-indigo-500/20" placeholder="0.00">
                    </div>

                    <div class="border-t border-gray-200 pt-4 flex justify-between items-center text-xl font-black text-gray-900">
                        <span>Total</span>
                        <span x-text="total.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 shadow-lg shadow-indigo-500/30 hover:translate-y-[-2px] hover:shadow-xl transition-all duration-300">
                    Create Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function purchaseOrderForm() {
    return {
        items: [
            { product_id: '', quantity: 1, unit_cost: 0, tax_code_id: '', tax_rate: 0, tax_amount: 0 }
        ],
        discount: 0,
        taxRate: 0,
        tax: 0,
        shipping: 0,
        
        addItem() {
            this.items.push({ product_id: '', quantity: 1, unit_cost: 0, tax_code_id: '', tax_rate: 0, tax_amount: 0 });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
        },
        
        updateCost(index) {
            // Find the select element to get data-cost
            // Since we are inside x-model, accessing DOM element is a bit tricky via Alpine logic alone if options are dynamic
            // But we can just listen to change event
            const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
            if(select) {
                 const cost = select.options[select.selectedIndex].dataset.cost || 0;
                 this.items[index].unit_cost = parseFloat(cost);
            }
        },
        
        calculateItemTax(index) {
            const item = this.items[index];
            const select = document.querySelector(`select[name="items[${index}][tax_code_id]"]`);
            if (select && select.selectedIndex > 0) {
                const taxRate = parseFloat(select.options[select.selectedIndex].dataset.rate) || 0;
                item.tax_rate = taxRate;
                item.tax_amount = (item.quantity * item.unit_cost) * (taxRate / 100);
            } else {
                item.tax_rate = 0;
                item.tax_amount = 0;
            }
            this.calculateTax();
        },
        
        getItemTotal(index) {
            const item = this.items[index];
            return (item.quantity * item.unit_cost) + (item.tax_amount || 0);
        },
        
        calculateTax() {
            // Calculate total tax from all items
            this.tax = this.items.reduce((sum, item) => sum + (item.tax_amount || 0), 0);
        },
        
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_cost), 0);
        },
        
        get total() {
            let t = this.subtotal - this.discount + this.tax + this.shipping;
            return t > 0 ? t : 0;
        }
    }
}
</script>
@endsection
