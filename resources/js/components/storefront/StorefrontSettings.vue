<template>
    <div v-if="canAccessStorefrontFeature" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900">Storefront Visibility</h3>
            <p class="text-sm text-gray-500">Enable or disable your public-facing online store.</p>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-1">Status</h4>
                    <p class="text-sm text-gray-600">
                        Your store is currently <strong>{{ isEnabled ? 'Online' : 'Offline' }}</strong>.
                    </p>
                </div>
                <div class="flex items-center">
                    <button 
                        type="button" 
                        @click="handleToggle"
                        :class="isEnabled ? 'bg-indigo-600' : 'bg-gray-200'"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                        role="switch"
                        :aria-checked="isEnabled"
                    >
                        <span 
                            :class="isEnabled ? 'translate-x-5' : 'translate-x-0'"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        ></span>
                    </button>
                    <span class="ml-3 text-sm font-medium text-gray-900">{{ isEnabled ? 'Enabled' : 'Disabled' }}</span>
                </div>
            </div>

            <!-- Warning message when disabling -->
            <div v-if="showWarning" class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700 font-bold">
                            Warning: Disabling this will take your public store offline.
                        </p>
                        <div class="mt-2 text-sm text-amber-600">
                            Customers will not be able to browse products or place orders until re-enabled.
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <button @click="confirmToggle" class="bg-amber-100 hover:bg-amber-200 text-amber-800 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                Disable Anyway
                            </button>
                            <button @click="showWarning = false" class="text-amber-800 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        initialStatus: {
            type: Boolean,
            required: true
        },
        hasFeature: {
            type: Boolean,
            required: true
        },
        tenantSlug: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isEnabled: this.initialStatus,
            canAccessStorefrontFeature: this.hasFeature,
            showWarning: false,
            isProcessing: false
        };
    },
    methods: {
        handleToggle() {
            if (this.isProcessing) return;

            if (this.isEnabled) {
                // Show warning when disabling
                this.showWarning = true;
            } else {
                // Enabling is safe
                this.confirmToggle();
            }
        },
        async confirmToggle() {
            this.isProcessing = true;
            this.showWarning = false;

            try {
                const response = await axios.post(`/${this.tenantSlug}/api/tenant/settings/toggle-storefront`);
                this.isEnabled = response.data.enabled;
                
                // Redirect to wizard if enabled
                if (this.isEnabled) {
                    window.location.href = `/${this.tenantSlug}/admin/wizard`;
                }
            } catch (error) {
                console.error('Failed to toggle storefront:', error);
                if (error.response && error.response.status === 403) {
                    alert('You do not have permission to access this feature.');
                } else {
                    alert('An error occurred. Please try again.');
                }
            } finally {
                this.isProcessing = false;
            }
        }
    }
};
</script>
