<template>
    <div class="bg-white rounded-lg shadow">
        <!-- Header & Filters -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                
                <!-- Source Tabs -->
                <div class="flex space-x-2 bg-gray-100 p-1 rounded-lg">
                    <button 
                        v-for="tab in tabs" 
                        :key="tab.value"
                        @click="setSource(tab.value)"
                        :class="[
                            'px-4 py-2 text-sm font-medium rounded-md transition-colors',
                            filters.source === tab.value 
                                ? 'bg-white text-gray-900 shadow' 
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <!-- Search & Actions -->
                <div class="flex items-center space-x-4 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            v-model="filters.search" 
                            @input="debouncedSearch"
                            type="text" 
                            placeholder="Search orders..." 
                            class="pl-10 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                    </div>
                    
                    <a :href="routes.create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        + New Order
                    </a>
                </div>
            </div>
            
            <!-- Bulk Actions Bar -->
            <div v-if="selectedOrders.length > 0" class="mt-4 flex items-center justify-between bg-indigo-50 p-3 rounded-lg border border-indigo-100 animate-in fade-in slide-in-from-top-2">
                <div class="flex items-center">
                    <span class="text-sm font-semibold text-indigo-700 ml-2">{{ selectedOrders.length }} orders selected</span>
                </div>
                <div class="flex items-center space-x-2">
                    <select 
                        v-model="bulkStatus" 
                        class="text-xs font-bold border-gray-300 rounded-md py-1 focus:ring-indigo-500 focus:border-indigo-500"
                        :disabled="isBulkProcessing"
                    >
                        <option value="">Status Update</option>
                        <option value="status_pending">Set Pending</option>
                        <option value="status_processing">Set Processing</option>
                        <option value="status_shipped">Set Shipped</option>
                        <option value="status_delivered">Set Delivered</option>
                        <option value="status_completed">Set Completed</option>
                        <option value="status_cancelled">Set Cancelled</option>
                        <option value="delete">Delete Orders</option>
                    </select>
                    <button 
                        @click="handleBulkAction" 
                        :disabled="!bulkStatus || isBulkProcessing"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 transition shadow-sm"
                    >
                        <svg v-if="isBulkProcessing" class="animate-spin -ml-1 mr-2 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Apply Action
                    </button>
                    <button 
                        @click="selectedOrders = []" 
                        class="text-xs font-bold text-gray-500 hover:text-gray-700 px-2"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            <!-- Real-time Indicator -->
            <div class="mt-4 flex items-center justify-end text-xs text-gray-500">
                <span class="flex h-2 w-2 relative mr-2">
                    <span v-if="isLoading" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span :class="['relative inline-flex rounded-full h-2 w-2', isLoading ? 'bg-indigo-500' : 'bg-green-500']"></span>
                </span>
                {{ isLoading ? 'Updating...' : 'Live' }}
            </div>
        </div>

        <!-- Order Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">
                            <input 
                                type="checkbox" 
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4"
                                :checked="isAllSelected"
                                @change="toggleSelectAll"
                            >
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order #
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                       <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Source
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="loadingInitial" class="animate-pulse">
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading orders...</td>
                    </tr>
                    <tr v-else-if="orders.data.length === 0">
                         <td colspan="7" class="px-6 py-4 text-center text-gray-500">No orders found.</td>
                    </tr>
                    <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50 transition-colors" :class="{ 'bg-indigo-50/30': selectedOrders.includes(order.id) }">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                                type="checkbox" 
                                v-model="selectedOrders" 
                                :value="order.id"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4"
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ order.order_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ order.customer ? order.customer.name : 'Guest' }}
                        </td>
                         <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDate(order.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="[
                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                statusClasses[order.status] || 'bg-gray-100 text-gray-800'
                            ]">
                                {{ capitalize(order.status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="flex items-center">
                                <svg v-if="order.order_source === 'storefront'" class="mr-1.5 h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <svg v-else-if="order.order_source === 'pos'" class="mr-1.5 h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <svg v-else class="mr-1.5 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ capitalize(order.order_source) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ formatMoney(order.total) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a :href="`${routes.base}/${order.id}`" class="text-indigo-600 hover:text-indigo-900">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200" v-if="orders.data.length > 0">
             <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ orders.from }}</span> to <span class="font-medium">{{ orders.to }}</span> of <span class="font-medium">{{ orders.total }}</span> results
                </div>
                <div class="space-x-2">
                    <button 
                        @click="fetchOrders(orders.prev_page_url)" 
                        :disabled="!orders.prev_page_url"
                        class="px-3 py-1 border-2 border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                    >
                        Previous
                    </button>
                    <button 
                        @click="fetchOrders(orders.next_page_url)" 
                        :disabled="!orders.next_page_url"
                        class="px-3 py-1 border-2 border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

// Simple debounce utility
const debounce = (func, wait) => {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

const props = defineProps({
    initialOrders: { type: Object, default: () => ({ data: [] }) },
    routes: { type: Object, required: true },
    currencySymbol: { type: String, default: '$' },
});

const orders = ref(props.initialOrders);
const loadingInitial = ref(false);
const isLoading = ref(false);
const pollingInterval = ref(null);
const selectedOrders = ref([]);
const bulkStatus = ref('');
const isBulkProcessing = ref(false);
const isAllSelected = ref(false);

const tabs = [
    { label: 'All Orders', value: 'all' },
    { label: 'Online Store', value: 'storefront' },
    { label: 'Manual Sales', value: 'admin' },
    { label: 'POS Sales List', value: 'pos' },
];

const filters = reactive({
    source: 'all',
    search: '',
});

const statusClasses = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const fetchOrders = async (url = null) => {
    isLoading.value = true;
    try {
        const fetchUrl = url || props.routes.index;
        const response = await axios.get(fetchUrl, {
            params: {
                source: filters.source === 'all' ? null : filters.source,
                search: filters.search,
            },
            headers: { 'Accept': 'application/json' }
        });
        orders.value = response.data;
        // Optionally clear selection on manual refresh or page change
        if (url) selectedOrders.value = []; 
    } catch (error) {
        console.error("Failed to fetch orders", error);
    } finally {
        isLoading.value = false;
        loadingInitial.value = false;
    }
};

const toggleSelectAll = () => {
    if (selectedOrders.value.length === orders.value.data.length) {
        selectedOrders.value = [];
        isAllSelected.value = false;
    } else {
        selectedOrders.value = orders.value.data.map(o => o.id);
        isAllSelected.value = true;
    }
};

const handleBulkAction = async () => {
    if (!bulkStatus.value || selectedOrders.value.length === 0) return;
    
    if (bulkStatus.value === 'delete' && !confirm(`Are you sure you want to delete ${selectedOrders.value.length} orders?`)) return;

    isBulkProcessing.value = true;
    try {
        const response = await axios.post(props.routes.bulkAction, {
            order_ids: selectedOrders.value,
            action: bulkStatus.value
        });

        if (response.data.success) {
            selectedOrders.value = [];
            bulkStatus.value = '';
            isAllSelected.value = false;
            fetchOrders(); // Refresh table
            alert(response.data.message);
        }
    } catch (error) {
        console.error("Bulk action failed", error);
        alert(error.response?.data?.message || "Bulk action failed. Please try again.");
    } finally {
        isBulkProcessing.value = false;
    }
};

const setSource = (source) => {
    filters.source = source;
    loadingInitial.value = true; // Show loading state for explicit filter change
    selectedOrders.value = [];
    isAllSelected.value = false;
    fetchOrders();
};

const debouncedSearch = debounce(() => {
    loadingInitial.value = true;
    fetchOrders();
}, 300);

const formatMoney = (amount) => {
    return props.currencySymbol + Number(amount).toFixed(2);
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1);

onMounted(() => {
    // Initial fetch if needed, or rely on props
    // Polling every 15 seconds
    pollingInterval.value = setInterval(() => {
        // Silent update (isLoading indicator only)
        fetchOrders();
    }, 15000);
});

onUnmounted(() => {
    if (pollingInterval.value) clearInterval(pollingInterval.value);
});
</script>
