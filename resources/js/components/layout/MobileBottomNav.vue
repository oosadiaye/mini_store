<template>
  <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50">
    <div class="grid grid-cols-4 h-16">
      <!-- Products -->
      <a :href="productsUrl" 
         class="flex flex-col items-center justify-center text-xs transition"
         :class="isProductsActive ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600'">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <span class="font-medium">Products</span>
      </a>
      
      <!-- POS -->
      <a :href="posUrl" 
         class="flex flex-col items-center justify-center text-xs transition"
         :class="isPosActive ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600'">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
        </svg>
        <span class="font-medium">POS</span>
      </a>
      
      <!-- Orders -->
      <a :href="ordersUrl" 
         class="flex flex-col items-center justify-center text-xs transition"
         :class="isOrdersActive ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600'">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <span class="font-medium">Orders</span>
      </a>
      
      <!-- Dashboard -->
      <a :href="dashboardUrl" 
         class="flex flex-col items-center justify-center text-xs transition"
         :class="isDashboardActive ? 'text-indigo-600' : 'text-gray-600 hover:text-indigo-600'">
        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
        </svg>
        <span class="font-medium">Dashboard</span>
      </a>
    </div>
  </nav>
</template>

<script setup>
const props = defineProps({
  productsUrl: String,
  posUrl: String,
  ordersUrl: String,
  dashboardUrl: String,
  activeRoute: String
});

const isProductsActive = props.activeRoute.includes('products');
const isPosActive = props.activeRoute.includes('pos');
const isOrdersActive = props.activeRoute.includes('orders');
const isDashboardActive = props.activeRoute === 'dashboard';
</script>
