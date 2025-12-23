@extends('admin.layout')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="orderForm()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create Sales Order</h1>
        <a href="{{ route('admin.orders.index', ['source' => 'admin']) }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Back to List</a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.pos.store') }}" method="POST" id="salesOrderForm"> {{-- Note: We initially point to POS store, but we need to point to proper OrderController store logic or handle it. Wait, I updated OrderController@store, but need to check route mapping. The route for admin store is missing in route file? No, it's not resource route. Let's fix route first or assume standard REST. Ah, I did not add POST route to tenant.php yet. --}}
        {{-- Actually, let's use a dedicated route in tenant.php for this store method --}}
    </form>
    {{-- CORRECTING ON THE FLY: I need to add the route first or use the form correctly. 
       I will write the file assuming the route name 'admin.orders.store' exists (standard resource) or I added it.
       Wait, in tenant.php I only added 'orders/create'. I need to add 'orders' POST route.
       I will write this file with the correct action route, and then update tenant.php in the next step.
    --}}
    
    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Customer</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Customer</label>
                        <select name="customer_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Items</h2>
                    
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-sm font-medium text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                <th class="py-2 w-1/2">Product</th>
                                <th class="py-2 w-20">Qty</th>
                                <th class="py-2 w-32">Price</th>
                                <th class="py-2 w-32 text-right">Total</th>
                                <th class="py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="py-3">
                                        <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="updatePrice(index)" class="w-full rounded border-gray-300 text-sm">
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" class="w-full rounded border-gray-300 text-sm">
                                    </td>
                                    <td class="py-3">
                                        <input type="number" :name="'items['+index+'][price]'" x-model="item.price" step="0.01" class="w-full rounded border-gray-300 text-sm">
                                    </td>
                                    <td class="py-3 text-right font-medium" x-text="formatMoney(item.price * item.quantity)"></td>
                                    <td class="py-3 text-right">
                                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <button type="button" @click="addItem()" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 text-sm font-medium">
                        + Add Line Item
                    </button>
                </div>
            </div>

            <!-- Right Column: Payment & Status -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Order Summary</h2>
                    
                    <div class="flex justify-between py-2 text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                        <span class="font-medium" x-text="formatMoney(subtotal)"></span>
                    </div>
                    <div class="flex justify-between py-2 text-sm border-t border-gray-100 dark:border-gray-700 pt-2">
                        <span class="text-gray-800 font-bold dark:text-white">Total</span>
                        <span class="font-bold text-lg text-indigo-600" x-text="formatMoney(subtotal)"></span>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Status</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Order Status</label>
                        <select name="status" class="w-full rounded border-gray-300">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full rounded border-gray-300">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition">
                        Create Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function orderForm() {
    return {
        items: [
            { product_id: '', quantity: 1, price: 0 }
        ],
        
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        addItem() {
            this.items.push({ product_id: '', quantity: 1, price: 0 });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        updatePrice(index) {
            // Find the select element for this item row to get data-price
            // This is a bit tricky with Alpine inside loop. 
            // Better approach: create a map of products in JS.
            let pid = this.items[index].product_id;
            let product = productsMap[pid];
            if (product) {
                this.items[index].price = product.price;
            }
        },

        formatMoney(amount) {
            return '$' + Number(amount).toFixed(2);
        }
    }
}

// Generate product map from server-side data for easier lookup
const productsMap = {};
@foreach($products as $p)
    productsMap[{{ $p->id }}] = { price: {{ $p->price ?? 0 }} };
@endforeach

</script>
@endsection
