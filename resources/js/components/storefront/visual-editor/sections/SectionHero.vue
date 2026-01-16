<template>
  <div 
    class="w-full py-16 px-8 flex flex-col items-center justify-center text-center transition-all duration-300"
    :style="{ backgroundColor: props.bg_color || '#1a1a1a', color: props.text_color || '#ffffff' }"
  >
    <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ props.title || 'Hero Title' }}</h1>
    <p class="text-xl opacity-90">{{ props.subtitle || 'Subtitle goes here' }}</p>
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
