<template>
  <div>
    <!-- Mobile Sidebar Backdrop -->
    <transition
      enter-active-class="transition-opacity ease-linear duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="isOpen" 
        @click="close"
        class="fixed inset-0 bg-slate-900/80 z-20 md:hidden"
      ></div>
    </transition>

    <!-- Mobile Sidebar Content -->
    <transition
      enter-active-class="transition ease-in-out duration-300 transform"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition ease-in-out duration-300 transform"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div 
        v-if="isOpen" 
        class="fixed inset-y-0 left-0 w-64 bg-slate-900 z-30 md:hidden overflow-y-auto"
      >
        <slot></slot>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const isOpen = computed(() => window.uiState?.sidebarOpen || false);

const close = () => {
  if (window.uiState) {
    window.uiState.sidebarOpen = false;
  }
};
</script>
