<template>
    <div class="bg-white rounded-lg shadow p-8">
        <div v-if="successMessage" class="mb-6 p-4 bg-green-50 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ successMessage }}</p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Product Selection -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                    <select 
                        v-model="form.product_id"
                        @change="resetSelection"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        :class="{'border-red-500': errors.product_id}">
                        <option value="">Select Product</option>
                        <option v-for="product in products" :key="product.id" :value="product.id">
                            {{ product.name }} (SKU: {{ product.sku }})
                        </option>
                    </select>
                    <p v-if="errors.product_id" class="text-red-500 text-xs mt-1">{{ errors.product_id }}</p>
                </div>

                <!-- From Warehouse -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Warehouse *</label>
                    <select 
                        v-model="form.from_warehouse_id"
                        :disabled="!form.product_id"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        :class="{'border-red-500': errors.from_warehouse_id, 'bg-gray-100': !form.product_id}">
                        <option value="">Select Source Warehouse</option>
                        <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                            {{ warehouse.name }} ({{ warehouse.code }})
                        </option>
                    </select>
                    <div v-if="availableStock !== null" class="mt-1">
                        <p class="text-sm" :class="availableStock > 0 ? 'text-green-600' : 'text-red-600'">
                            Available: <span class="font-bold">{{ availableStock }}</span> units
                        </p>
                    </div>
                     <p v-if="errors.from_warehouse_id" class="text-red-500 text-xs mt-1">{{ errors.from_warehouse_id }}</p>
                </div>

                <!-- To Warehouse -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Warehouse *</label>
                    <select 
                        v-model="form.to_warehouse_id"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                         :class="{'border-red-500': errors.to_warehouse_id}">
                        <option value="">Select Destination Warehouse</option>
                        <option 
                            v-for="warehouse in warehouses" 
                            :key="warehouse.id" 
                            :value="warehouse.id"
                            :disabled="warehouse.id === form.from_warehouse_id">
                            {{ warehouse.name }} ({{ warehouse.code }})
                        </option>
                    </select>
                     <p v-if="errors.to_warehouse_id" class="text-red-500 text-xs mt-1">{{ errors.to_warehouse_id }}</p>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input 
                        type="number" 
                        v-model.number="form.quantity"
                        min="1"
                        :max="availableStock"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        :class="{'border-red-500': errors.quantity}">
                    <p v-if="availableStock !== null && form.quantity > 0 && form.quantity <= availableStock" class="text-xs text-gray-500 mt-1">
                        Remaining after transfer: {{ availableStock - form.quantity }} units
                    </p>
                    <p v-if="errors.quantity" class="text-red-500 text-xs mt-1">{{ errors.quantity }}</p>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea 
                        v-model="form.notes"
                        rows="3" 
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                        placeholder="Add any relevant notes about this transfer..."></textarea>
                     <p v-if="errors.notes" class="text-red-500 text-xs mt-1">{{ errors.notes }}</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="/admin/stock-transfers" 
                   class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    :disabled="isSubmitting || !isValid"
                    class="px-6 py-2 text-white rounded-lg transition"
                    :class="isValid && !isSubmitting ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-gray-400 cursor-not-allowed'">
                    <span v-if="isSubmitting">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                    </span>
                    <span v-else>
                        Create Transfer Request
                    </span>
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialProducts: {
        type: Array,
        required: true
    },
    initialWarehouses: {
        type: Array,
        required: true
    },
    tenantSlug: {
        type: String,
        required: true
    }
});

const products = ref(props.initialProducts);
const warehouses = ref(props.initialWarehouses);
const isSubmitting = ref(false);
const successMessage = ref('');
const errors = reactive({});

const form = reactive({
    product_id: '',
    from_warehouse_id: '',
    to_warehouse_id: '',
    quantity: '',
    notes: ''
});

// Computed available stock based on selected product and warehouse
const availableStock = computed(() => {
    if (!form.product_id || !form.from_warehouse_id) return null;
    
    // Use == for comparison as form IDs are often strings from <select>
    const product = products.value.find(p => p.id == form.product_id);
    if (!product || !product.warehouses) return 0;
    
    // Check if the product has the warehouse in its relation
    // The warehouse relation typically includes pivot data with stock
    const warehouseData = product.warehouses.find(w => w.id == form.from_warehouse_id);
    
    return warehouseData && warehouseData.pivot ? Number(warehouseData.pivot.quantity) : 0;
});

const isValid = computed(() => {
    return form.product_id && 
           form.from_warehouse_id && 
           form.to_warehouse_id && 
           form.from_warehouse_id !== form.to_warehouse_id &&
           form.quantity > 0 &&
           availableStock.value !== null && 
           form.quantity <= availableStock.value;
});

const resetSelection = () => {
    form.from_warehouse_id = '';
    form.to_warehouse_id = '';
    form.quantity = '';
    // Clear specific field errors
    delete errors.from_warehouse_id;
    delete errors.to_warehouse_id;
    delete errors.quantity;
};

const submitForm = async () => {
    if (!isValid.value) return;
    
    isSubmitting.value = true;
    Object.keys(errors).forEach(key => delete errors[key]); // Clear errors
    successMessage.value = '';

    try {
        const response = await axios.post(`/${props.tenantSlug}/admin/stock-transfers`, form);
        successMessage.value = 'Stock transfer request created successfully!';
        
        // Redirect after short delay
        setTimeout(() => {
            window.location.href = `/${props.tenantSlug}/admin/stock-transfers`;
        }, 1500);

    } catch (error) {
         if (error.response && error.response.status === 422) {
            Object.assign(errors, error.response.data.errors);
        } else {
             // General error
             alert(error.response?.data?.message || 'An error occurred while creating the transfer.');
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>
