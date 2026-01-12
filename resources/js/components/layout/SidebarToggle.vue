<template>
  <button 
    @click="toggleSidebar" 
    class="text-slate-500 focus:outline-none md:hidden mr-2 md:mr-4 hover:text-slate-700 flex-shrink-0"
  >
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
  </button>
</template>

<script setup>
const toggleSidebar = () => {
  if (window.uiState) {
    window.uiState.sidebarOpen = !window.uiState.sidebarOpen;
  }
};
</script>
