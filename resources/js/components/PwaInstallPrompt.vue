<template>
  <transition
    enter-active-class="ease-out duration-300"
    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
    leave-active-class="ease-in duration-200"
    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
  >
    <div v-if="showModal" class="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6 flex justify-center items-end pointer-events-none">
      <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md pointer-events-auto border border-gray-100">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h3 class="text-xl font-black text-gray-900 mb-2">Install App</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Install our app for a better experience, faster access, and offline capabilities.
            </p>
          </div>
          <button @click="dismiss" class="text-gray-400 hover:text-gray-500 p-1">
             <span class="sr-only">Close</span>
             <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
             </svg>
          </button>
        </div>
        
        <div class="mt-6 flex gap-3">
          <button @click="dismiss" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">
            Maybe Later
          </button>
          <button @click="installPwa" class="flex-1 px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
            Install Now
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "PwaInstallPrompt",
  data() {
    return {
      showModal: false,
      deferredPrompt: null,
    };
  },
  mounted() {
    window.addEventListener("beforeinstallprompt", (e) => {
      // Prevent the mini-infobar from appearing on mobile
      e.preventDefault();
      // Stash the event so it can be triggered later.
      this.deferredPrompt = e;
      // Update UI notify the user they can install the PWA
      this.showModal = true;
      console.log("PWA install prompt captured");
    });

    window.addEventListener("appinstalled", () => {
      this.showModal = false;
      this.deferredPrompt = null;
      console.log("PWA was installed");
    });
  },
  methods: {
    async installPwa() {
      if (!this.deferredPrompt) return;
      
      // Show the install prompt
      this.deferredPrompt.prompt();
      
      // Wait for the user to respond to the prompt
      const { outcome } = await this.deferredPrompt.userChoice;
      console.log(`User response to the install prompt: ${outcome}`);
      
      // We've used the prompt, and can't use it again, throw it away
      this.deferredPrompt = null;
      this.showModal = false;
    },
    dismiss() {
      this.showModal = false;
    }
  }
};
</script>
