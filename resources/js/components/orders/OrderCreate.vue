<template>
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Record Credit Sale</h1>
            <a :href="routes.index" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Back to List</a>
        </div>

        <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ errorMessage }}
        </div>

        <form @submit.prevent="submitOrder">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Order Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Selection -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-800">Customer</h2>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Select Customer</label>
                            <select v-model="form.customer_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Choose Customer --</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.name }} ({{ customer.phone }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-800">Items</h2>
                        
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-sm font-medium text-gray-500 border-b border-gray-200">
                                    <th class="py-2 w-1/2">Product</th>
                                    <th class="py-2 w-20">Qty</th>
                                    <th class="py-2 w-32">Price</th>
                                    <th class="py-2 w-32 text-right">Total</th>
                                    <th class="py-2 w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in form.items" :key="index" class="border-b border-gray-100">
                                    <td class="py-3">
                                        <select v-model="item.product_id" @change="updatePrice(index)" class="w-full rounded border-gray-300 text-sm" required>
                                            <option value="">Select Product</option>
                                            <option v-for="product in products" :key="product.id" :value="product.id">
                                                {{ product.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="py-3">
                                        <input type="number" v-model.number="item.quantity" min="1" class="w-full rounded border-gray-300 text-sm" required>
                                    </td>
                                    <td class="py-3">
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">{{ currencySymbol }}</span>
                                            <input type="number" v-model.number="item.price" step="0.01" class="w-full rounded border-gray-300 pl-7 text-sm" required>
                                        </div>
                                    </td>
                                    <td class="py-3 text-right font-medium">
                                        {{ formatMoney(item.price * item.quantity) }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" @click="addItem" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-100 text-sm font-medium">
                            + Add Line Item
                        </button>
                    </div>
                </div>

                <!-- Right Column: Payment & Status -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-800">Order Summary</h2>
                        
                        <div class="flex justify-between py-2 text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">{{ formatMoney(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between py-2 text-sm border-t border-gray-100 pt-2">
                            <span class="text-gray-800 font-bold">Total</span>
                            <span class="font-bold text-lg text-indigo-600">{{ formatMoney(subtotal) }}</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold mb-4 text-gray-800">Sale Details</h2>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1 text-gray-700">Payment Due Date (Optional)</label>
                            <input type="date" v-model="form.due_date" class="w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="mb-4">
                             <label class="block text-sm font-medium mb-1 text-gray-700">Notes / Reference</label>
                             <input type="text" v-model="form.notes" class="w-full rounded border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="PO Number etc.">
                        </div>

                        <div class="p-3 bg-yellow-50 rounded-md mb-4 border border-yellow-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Credit Sale</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                    <p>This will record the order as <strong>Pending</strong>. You can update the status to <strong>Completed</strong> later to record revenue.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" :disabled="processing" class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 flex justify-center items-center">
                            <span v-if="processing" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                            Record Sale
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
    routes: { type: Object, required: true },
    currencySymbol: { type: String, default: '$' },
});

const customers = ref([]);
const products = ref([]);
const processing = ref(false);
const errorMessage = ref('');

const form = reactive({
    customer_id: '',
    items: [
        { product_id: '', quantity: 1, price: 0 }
    ],
    status: 'pending', // Default to Pending (Order Created)
    payment_status: 'pending', // Forced for Credit Sale
    due_date: '',
    notes: ''
});

import { onMounted } from 'vue';

onMounted(async () => {
    try {
        const [custRes, prodRes] = await Promise.all([
            axios.get(props.routes.customers),
            axios.get(props.routes.products)
        ]);
        customers.value = custRes.data;
        products.value = prodRes.data;
    } catch (e) {
        console.error("Failed to load resources", e);
        errorMessage.value = "Failed to load customers or products.";
    }
});

const subtotal = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
});

const addItem = () => {
    form.items.push({ product_id: '', quantity: 1, price: 0 });
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const updatePrice = (index) => {
    const item = form.items[index];
    const product = products.value.find(p => p.id === item.product_id);
    if (product) {
        item.price = parseFloat(product.price || 0);
    }
};

const formatMoney = (amount) => {
    return props.currencySymbol + Number(amount).toFixed(2);
};

const submitOrder = async () => {
    processing.value = true;
    errorMessage.value = '';

    try {
        const response = await axios.post(props.routes.store, form);
        // Assuming controller redirects or we redirect manually
        // If controller returns redirect: window.location.href = response.request.responseURL;
        // Or if JSON success:
        window.location.href = props.routes.index;
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data && error.response.data.errors) {
            errorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
             errorMessage.value = 'Failed to create order.';
        }
        processing.value = false;
    }
};
</script>
