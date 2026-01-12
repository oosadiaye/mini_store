<template>
    <div>
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Inventory Value</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">
                            <span>{{ currencySymbol }}</span><span>{{ formatNumber(state.totalInventoryValue, 2) }}</span>
                        </p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Units in Stock</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ formatNumber(state.totalInventoryUnits) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ state.lowStock.length }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock by Warehouse -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Stock Distribution by Warehouse</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Units</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="stock in state.stockByWarehouse" :key="stock.warehouse" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ stock.warehouse }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ formatNumber(stock.product_count) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ formatNumber(stock.total_units) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 text-right">
                                <span>{{ currencySymbol }}</span><span>{{ formatNumber(stock.total_value, 2) }}</span>
                            </td>
                        </tr>
                        <tr v-if="state.stockByWarehouse.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No warehouse data available</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Inventory Activity -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Inventory Activity Report</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Opening</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Purchased</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sold</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Adj/Trans/Ret</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Closing</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="item in state.inventoryReportData" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ item.name }}</div>
                                <div class="text-xs text-gray-500">{{ item.sku }}</div>
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-gray-500">{{ formatNumber(item.opening_stock) }}</td>
                            <td class="px-6 py-4 text-right text-sm text-green-600">{{ '+' + formatNumber(item.purchased_qty) }}</td>
                            <td class="px-6 py-4 text-right text-sm text-red-600">{{ '-' + formatNumber(item.sold_qty) }}</td>
                            <td class="px-6 py-4 text-right text-sm text-blue-600">
                                {{ ((item.adjustment_qty + item.transfer_qty + item.return_qty) >= 0 ? '+' : '') + formatNumber(item.adjustment_qty + item.transfer_qty + item.return_qty) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">{{ formatNumber(item.closing_stock) }}</td>
                        </tr>
                        <tr v-if="state.inventoryReportData.length === 0">
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No data found</td>
                        </tr>
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-200" v-if="!state.loading && paginationHtml" v-html="paginationHtml"></div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Low Stock Alert -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-red-50 border-b border-red-200">
                    <h3 class="text-lg font-bold text-red-800">⚠️ Low Stock Alert</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Threshold</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in state.lowStock" :key="product.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium">{{ product.name }}</div>
                                    <div class="text-xs text-gray-500">{{ product.sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-right">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">{{ product.stock_quantity }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 text-right">{{ product.low_stock_threshold }}</td>
                            </tr>
                            <tr v-if="state.lowStock.length === 0">
                                <td colspan="3" class="px-4 py-8 text-center text-green-600">
                                    <i class="fas fa-check-circle text-2xl mb-2"></i>
                                    <div>All products are well stocked!</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fast Moving Products -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                    <h3 class="text-lg font-bold text-blue-800">🚀 Fast Moving Products</h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Sold Qty</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="product in state.fastMoving" :key="product.sku" class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    <div class="font-medium">{{ product.name }}</div>
                                    <div class="text-xs text-gray-500">{{ product.sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-blue-600 text-right">{{ formatNumber(product.sold_qty) }}</td>
                            </tr>
                            <tr v-if="state.fastMoving.length === 0">
                                <td colspan="2" class="px-4 py-8 text-center text-gray-500">
                                    <div>No sales data found!</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Recent Movements</h3>
                <a href="/admin/reports/movement" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">View All Dashboard →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="movement in state.stockMovements" :key="movement.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(movement.created_at) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ movement.product ? movement.product.name : 'Deleted' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ movement.warehouse ? movement.warehouse.name : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full capitalize" 
                                    :class="{
                                        'bg-green-100 text-green-800': movement.type === 'purchase',
                                        'bg-blue-100 text-blue-800': movement.type === 'sale',
                                        'bg-yellow-100 text-yellow-800': movement.type === 'adjustment',
                                        'bg-gray-100 text-gray-800': !['purchase', 'sale', 'adjustment'].includes(movement.type)
                                    }">{{ movement.type }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right" 
                                :class="movement.quantity > 0 ? 'text-green-600' : 'text-red-600'">
                                {{ (movement.quantity > 0 ? '+' : '') + movement.quantity }}
                            </td>
                        </tr>
                        <tr v-if="state.stockMovements.length === 0">
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No stock movements found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    totalInventoryValue: { type: Number, default: 0 },
    totalInventoryUnits: { type: Number, default: 0 },
    lowStock: { type: Array, default: () => [] },
    stockByWarehouse: { type: Array, default: () => [] },
    inventoryReportData: { type: Array, default: () => [] },
    fastMoving: { type: Array, default: () => [] },
    stockMovements: { type: Array, default: () => [] },
    currencySymbol: { type: String, default: '₦' },
    paginationHtml: { type: String, default: '' }
});

const state = reactive({
    loading: false,
    totalInventoryValue: props.totalInventoryValue,
    totalInventoryUnits: props.totalInventoryUnits,
    lowStock: props.lowStock,
    stockByWarehouse: props.stockByWarehouse,
    inventoryReportData: props.inventoryReportData,
    fastMoving: props.fastMoving,
    stockMovements: props.stockMovements
});

let pollInterval = null;

const fetchData = async () => {
    if (state.loading) return;
    // Don't show global loading for background updates to avoid flicker
    // state.loading = true; 
    
    try {
        const url = new URL(window.location.href);
        const response = await fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        
        if (!response.ok) throw new Error('Network response was not ok');
        
        const data = await response.json();
        
        state.totalInventoryValue = data.totalInventoryValue;
        state.totalInventoryUnits = data.totalInventoryUnits;
        state.lowStock = data.lowStock;
        state.stockByWarehouse = data.stockByWarehouse;
        if (data.inventoryReport && data.inventoryReport.data) {
            state.inventoryReportData = data.inventoryReport.data;
        }
        state.fastMoving = data.fastMoving;
        state.stockMovements = data.stockMovements;
        
    } catch (error) {
        console.error('Failed to fetch real-time data:', error);
    } finally {
        state.loading = false;
    }
};

const formatNumber = (num, decimals = 0) => {
    return Number(num).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
};

const formatDate = (dateStr) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
};

onMounted(() => {
    pollInterval = setInterval(fetchData, 45000);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>
