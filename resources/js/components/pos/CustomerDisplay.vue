<template>
    <div class="flex h-screen overflow-hidden font-sans">
        <!-- Left: Branding / Ads -->
        <div class="w-1/2 bg-indigo-900 flex flex-col items-center justify-center text-white p-12 relative">
            <template v-if="logoUrl">
                <img :src="logoUrl" class="max-w-xs mb-8 rounded-xl shadow-2xl bg-white p-4">
            </template>
            <template v-else>
                <h1 class="text-6xl font-black tracking-tighter mb-4">{{ tenantName }}</h1>
            </template>
            
            <p class="text-2xl opacity-75 text-center">Thank you for shopping with us!</p>
            
            <!-- Connection Status -->
            <div class="fixed bottom-4 left-4 flex items-center gap-2 text-xs opacity-50">
                <div class="w-2 h-2 rounded-full" :class="connected ? 'bg-green-400' : 'bg-red-400'"></div>
                <span>{{ connected ? 'Connected' : 'Waiting for POS...' }}</span>
            </div>
        </div>

        <!-- Right: Receipt -->
        <div class="w-1/2 bg-white p-8 flex flex-col shadow-2xl h-full text-black">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Your Order</h2>
            
            <!-- Items -->
            <div class="flex-1 overflow-y-auto space-y-4 pr-2">
                <template v-if="data.cart.length === 0">
                    <div class="h-full flex items-center justify-center text-gray-400 text-lg">
                        Processing...
                    </div>
                </template>

                <div v-for="item in data.cart" :key="item.id || item.uniqueId" class="flex justify-between items-center py-2 border-b-2 border-gray-200 last:border-0">
                    <div>
                        <div class="font-bold text-xl text-gray-800">{{ item.name }}</div>
                        <div class="text-gray-500">
                            <span>{{ item.quantity }}</span> x <span>{{ formatPrice(item.price) }}</span>
                        </div>
                    </div>
                    <div class="font-bold text-xl text-gray-900">{{ formatPrice(item.price * item.quantity) }}</div>
                </div>
            </div>

            <!-- Totals -->
            <div class="mt-auto pt-6 border-t-2 border-gray-100 space-y-3">
                <div class="flex justify-between text-xl text-gray-600">
                    <span>Subtotal</span>
                    <span>{{ formatPrice(data.subtotal || 0) }}</span>
                </div>
                <div v-if="data.tax > 0" class="flex justify-between text-xl text-gray-600">
                    <span>Tax</span>
                    <span>{{ formatPrice(data.tax || 0) }}</span>
                </div>
                
                <div class="flex justify-between text-4xl font-black text-indigo-900 py-4">
                    <span>Total</span>
                    <span>{{ formatPrice(data.total || 0) }}</span>
                </div>

                <div v-if="data.amountTendered > 0" class="bg-indigo-50 p-4 rounded-xl space-y-2">
                    <div class="flex justify-between text-lg text-indigo-800">
                        <span>Paid</span>
                        <span>{{ formatPrice(data.amountTendered) }}</span>
                    </div>
                     <div class="flex justify-between text-2xl font-bold" :class="data.change < 0 ? 'text-red-600' : 'text-green-600'">
                        <span>Change</span>
                        <span>{{ formatPrice(Math.max(0, data.change)) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    logoUrl: { type: String, default: null },
    tenantName: { type: String, default: 'Store' },
    currencySymbol: { type: String, default: '$' }
});

const connected = ref(false);
const data = ref({
    cart: [],
    subtotal: 0,
    tax: 0,
    total: 0,
    amountTendered: 0,
    change: 0,
    taxRate: 0,
    currencySymbol: props.currencySymbol
});

const loadState = () => {
    const stored = localStorage.getItem('pos_state');
    if (stored) {
        try {
            data.value = JSON.parse(stored);
            connected.value = true;
        } catch (e) {
            console.error('Error parsing POS state', e);
        }
    }
};

const handleStorage = (event) => {
    if (event.key === 'pos_state') {
        loadState();
    }
};

onMounted(() => {
    loadState();
    window.addEventListener('storage', handleStorage);
});

onUnmounted(() => {
    window.removeEventListener('storage', handleStorage);
});

const formatPrice = (amount) => {
    return (data.value.currencySymbol || props.currencySymbol) + Number(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
};
</script>
