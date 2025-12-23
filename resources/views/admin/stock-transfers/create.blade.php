@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Create Stock Transfer</h2>
            <p class="text-sm text-gray-600 mt-1">Transfer inventory between warehouses</p>
        </div>
        <a href="{{ route('admin.stock-transfers.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Transfers
        </a>
    </div>

    <form action="{{ route('admin.stock-transfers.store') }}" method="POST" class="bg-white rounded-lg shadow p-8" x-data="transferForm()">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Selection -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                <select name="product_id" 
                        x-model="productId" 
                        @change="resetWarehouses()"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} (SKU: {{ $product->sku }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- From Warehouse -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Warehouse *</label>
                <select name="from_warehouse_id" 
                        x-model="fromWarehouseId" 
                        @change="checkStock('from')"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Source Warehouse</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }} ({{ $warehouse->code }})
                        </option>
                    @endforeach
                </select>
                <p x-show="fromStock !== null" class="text-sm mt-1" :class="fromStock > 0 ? 'text-green-600' : 'text-red-600'">
                    Available: <span x-text="fromStock"></span> units
                </p>
                @error('from_warehouse_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- To Warehouse -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Warehouse *</label>
                <select name="to_warehouse_id" 
                        x-model="toWarehouseId"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Destination Warehouse</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" 
                                :disabled="fromWarehouseId == {{ $warehouse->id }}"
                                {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }} ({{ $warehouse->code }})
                        </option>
                    @endforeach
                </select>
                @error('to_warehouse_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                <input type="number" 
                       name="quantity" 
                       x-model="quantity"
                       value="{{ old('quantity') }}"
                       min="1" 
                       :max="fromStock"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <p x-show="quantity > 0 && fromStock > 0" class="text-xs text-gray-500 mt-1">
                    Remaining after transfer: <span x-text="fromStock - quantity"></span> units
                </p>
                @error('quantity')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" 
                          rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                          placeholder="Add any relevant notes about this transfer...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.stock-transfers.index') }}" 
               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                Cancel
            </a>
            <button type="submit" 
                    :disabled="!canSubmit()"
                    :class="canSubmit() ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400 cursor-not-allowed'"
                    class="px-6 py-2 text-white rounded-lg transition">
                Create Transfer Request
            </button>
        </div>
    </form>
</div>

<script>
function transferForm() {
    return {
        productId: '{{ old('product_id') }}',
        fromWarehouseId: '{{ old('from_warehouse_id') }}',
        toWarehouseId: '{{ old('to_warehouse_id') }}',
        quantity: {{ old('quantity', 0) }},
        fromStock: null,

        resetWarehouses() {
            this.fromWarehouseId = '';
            this.toWarehouseId = '';
            this.fromStock = null;
            this.quantity = 0;
        },

        async checkStock(type) {
            if (!this.productId || !this.fromWarehouseId) return;

            try {
                const response = await fetch(`{{ route('admin.stock-transfers.get-stock') }}?product_id=${this.productId}&warehouse_id=${this.fromWarehouseId}`);
                const data = await response.json();
                this.fromStock = data.stock;
            } catch (error) {
                console.error('Error fetching stock:', error);
            }
        },

        canSubmit() {
            return this.productId && 
                   this.fromWarehouseId && 
                   this.toWarehouseId && 
                   this.fromWarehouseId !== this.toWarehouseId &&
                   this.quantity > 0 && 
                   this.fromStock !== null &&
                   this.quantity <= this.fromStock;
        }
    }
}
</script>
@endsection
