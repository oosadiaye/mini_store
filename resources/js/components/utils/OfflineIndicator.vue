<template>
  <div 
    id="offline-indicator" 
    v-show="!online || queueCount > 0"
    class="fixed bottom-4 right-4 bg-slate-800 text-white px-4 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-3 transition-transform duration-300"
  >
    <div v-if="!online" class="flex items-center">
      <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 011.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path>
      </svg>
      <span class="font-medium">You are offline.</span>
    </div>
    <div v-else-if="queueCount > 0" class="flex items-center">
      <svg class="animate-spin w-5 h-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <span class="font-medium">Syncing data...</span>
    </div>
    <div v-if="queueCount > 0" class="ml-2 bg-white text-slate-800 text-xs font-bold px-2 py-0.5 rounded-full">
      {{ queueCount }} pending
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const online = ref(navigator.onLine);
const queueCount = ref(0);

const updateQueueCount = async () => {
  if (window.OfflineManager) {
    queueCount.value = await window.OfflineManager.getQueueCount();
  }
};

const handleOnline = () => {
  online.value = true;
  setTimeout(() => {
    if (online.value && queueCount.value === 0) {
      // The v-show will handle hiding it
    }
  }, 3000);
};

const handleOffline = () => {
  online.value = false;
};

const handleQueueUpdated = async () => {
  await updateQueueCount();
};

onMounted(async () => {
  await updateQueueCount();
  window.addEventListener('online', handleOnline);
  window.addEventListener('offline', handleOffline);
  window.addEventListener('offline-queue-updated', handleQueueUpdated);
});

onUnmounted(() => {
  window.removeEventListener('online', handleOnline);
  window.removeEventListener('offline', handleOffline);
  window.removeEventListener('offline-queue-updated', handleQueueUpdated);
});
</script>
