<template>
  <div 
    class="relative mr-2 md:mr-4"
    @mouseenter="openOnHover"
    @mouseleave="closeOnHover"
  >
    <button 
      @click="toggle"
      class="relative p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none"
    >
      <span class="sr-only">View notifications</span>
      <svg class="h-5 md:h-6 w-5 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
    </button>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div 
        v-if="isOpen" 
        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
      >
        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
          <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
          <a 
            v-if="notifications.length > 0"
            :href="markAllReadUrl" 
            class="text-xs text-indigo-600 hover:text-indigo-800"
          >
            Mark all read
          </a>
        </div>
        <div class="max-h-60 overflow-y-auto">
          <div 
            v-for="notification in notifications" 
            :key="notification.id"
            class="px-4 py-3 hover:bg-gray-50 transition border-b border-gray-100 last:border-0"
          >
            <p class="text-sm font-medium text-gray-900">{{ notification.data?.title || 'Notification' }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ notification.data?.message || '' }}</p>
            <div class="mt-2 flex justify-between items-center">
              <span class="text-xs text-gray-400">{{ notification.created_time_ago }}</span>
              <a 
                :href="getReadUrl(notification.id)" 
                class="text-xs text-indigo-600 hover:text-indigo-800"
              >
                Dismiss
              </a>
            </div>
          </div>
          <div 
            v-if="notifications.length === 0" 
            class="px-4 py-6 text-center text-sm text-gray-500"
          >
            No new notifications
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  notifications: {
    type: Array,
    default: () => []
  },
  markAllReadUrl: String,
  readUrlPrefix: String, // e.g. /joshy/admin/notifications/read/
});

const isOpen = ref(false);

const toggle = () => {
  isOpen.value = !isOpen.value;
};

const openOnHover = () => {
  isOpen.value = true;
};

const closeOnHover = () => {
  isOpen.value = false;
};

const getReadUrl = (id) => {
  return `${props.readUrlPrefix}${id}`;
};
</script>
