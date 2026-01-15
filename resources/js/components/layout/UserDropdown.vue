<template>
  <div 
    class="flex items-center text-sm border-l pl-6 border-slate-200 h-8 relative"
    @mouseenter="openOnHover"
    @mouseleave="closeOnHover"
  >
    <button @click="toggle" class="flex items-center focus:outline-none">
        <div class="text-right mr-3 hidden sm:block">
            <p class="font-semibold text-slate-700">{{ userName }}</p>
            <p class="text-xs text-slate-500">{{ userRole }}</p>
        </div>
        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
            {{ userInitial }}
        </div>
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
          class="origin-top-right absolute right-0 top-10 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        >
            <div class="px-4 py-3 border-b border-gray-100 md:hidden">
                <p class="text-sm font-medium text-gray-900">{{ userName }}</p>
                <p class="text-xs text-gray-500 truncate">{{ userEmail }}</p>
            </div>

            <a :href="profileUrl" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <i class="fas fa-user-circle mr-2 w-4"></i> My Profile
            </a>
            
            <button @click="startTour" class="block w-full text-left px-4 py-2 text-sm text-indigo-600 hover:bg-gray-100 font-medium">
                <i class="fas fa-play-circle mr-2 w-4"></i> Replay Tour
            </button>

            <div class="border-t border-gray-100">
                <form method="POST" :action="logoutUrl">
                    <input type="hidden" name="_token" :value="csrfToken">
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-2 w-4"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </transition>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  userName: String,
  userEmail: String,
  userRole: {
    type: String,
    default: 'Admin'
  },
  userInitial: String,
  profileUrl: String,
  logoutUrl: String,
  csrfToken: String
});

import { TourService } from '../../services/TourService';

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

const startTour = () => {
    isOpen.value = false;
    TourService.startTour('full_tour');
};
</script>
