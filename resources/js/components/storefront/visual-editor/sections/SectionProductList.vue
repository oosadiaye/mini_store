<template>
  <div class="w-full py-12 px-4 md:px-8 bg-white">
    <h2 class="text-2xl font-bold mb-8 text-center text-gray-800">{{ props.title || 'Featured Products' }}</h2>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
      <div 
        v-for="i in parseInt(props.limit || 4)" 
        :key="i"
        class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="aspect-square bg-gray-100 w-full flex items-center justify-center text-gray-300">
          <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div class="p-4">
          <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
          <div class="h-4 bg-gray-200 rounded w-1/4"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  props: {
    type: Object,
    default: () => ({})
  }
});
</script>
