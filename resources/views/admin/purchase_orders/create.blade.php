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
        
        <div class="p-8">
            <purchase-order-form 
                :suppliers="{{ $suppliers->map->only(['id', 'name']) }}"
                :warehouses="{{ $warehouses->map->only(['id', 'name']) }}"
                :products="{{ $products->map->only(['id', 'name', 'cost_price']) }}"
                :tax-codes="{{ $taxCodes->map->only(['id', 'name', 'rate']) }}"
                currency="{{ app('tenant')->data['currency_symbol'] ?? 'USD' }}"
                submit-url="{{ route('admin.purchase-orders.store') }}"
                supplier-store-url="{{ route('admin.suppliers.store') }}"
                redirect-url="{{ route('admin.purchase-orders.index') }}"
            ></purchase-order-form>
        </div>
    </div>
</div>
@endsection
