<template>
    <div class="max-w-4xl mx-auto py-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Domain Settings</h2>
                <p class="text-gray-600">Manage your store's web address and custom domains.</p>
            </div>
            <a :href="routes.settingsIndex" class="text-indigo-600 hover:text-indigo-800 font-medium">← Back to Settings</a>
        </div>

        <!-- Notifications -->
        <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 relative">
            <p>{{ successMessage }}</p>
             <button @click="successMessage = ''" class="absolute top-2 right-2 text-green-500 hover:text-green-700">&times;</button>
        </div>

        <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 relative">
            <ul v-if="typeof errorMessage === 'object'" class="list-disc pl-5">
                <li v-for="(err, key) in errorMessage" :key="key">{{ err }}</li>
            </ul>
             <span v-else>{{ errorMessage }}</span>
             <button @click="errorMessage = ''" class="absolute top-2 right-2 text-red-500 hover:text-red-700">&times;</button>
        </div>

        <!-- Default Domain -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Default Store Address</h3>
                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Active</span>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-2">Your store is accessible at:</p>
                <div class="flex items-center space-x-2">
                    <code class="bg-gray-100 px-4 py-2 rounded text-indigo-600 font-mono text-lg block w-full border border-gray-200">
                        {{ initialData.storeUrl }}
                    </code>
                    <a :href="initialData.storeUrl" target="_blank" class="p-2 text-gray-500 hover:text-indigo-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Custom Domain -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900">Custom Domain</h3>
                <p class="text-sm text-gray-500">Connect your own domain name (e.g. myshop.com)</p>
            </div>
            
            <div class="p-6">
                <div v-if="currentRequest" class="border rounded-xl p-6" :class="currentRequest.status === 'approved' ? 'border-green-200 bg-green-50' : 'border-yellow-200 bg-yellow-50'">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-lg" :class="currentRequest.status === 'approved' ? 'text-green-800' : 'text-yellow-800'">
                                {{ currentRequest.domain }}
                            </h4>
                            <div class="mt-2 flex items-center space-x-2">
                                <span class="capitalize px-3 py-1 rounded-full text-xs font-bold" :class="currentRequest.status === 'approved' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800'">
                                    {{ currentRequest.status }}
                                </span>
                                <span v-if="currentRequest.status === 'pending'" class="text-sm text-gray-500">Submitted on {{ currentRequest.created_at }}</span>
                            </div>
                        </div>
                        
                        <div v-if="currentRequest.status === 'pending'">
                            <button @click="cancelRequest" :disabled="processing" class="text-red-600 hover:text-red-800 font-medium text-sm underline bg-transparent border-none cursor-pointer">
                                {{ processing ? 'Cancelling...' : 'Cancel Request' }}
                            </button>
                        </div>
                    </div>
                    
                    <div v-if="currentRequest.status === 'pending'" class="mt-6 border-t border-yellow-200 pt-4">
                        <h5 class="font-bold text-yellow-900 text-sm mb-2">DNS Configuration Required</h5>
                        <p class="text-yellow-800 text-sm mb-3">To verify ownership, please configure the following DNS record at your domain provider:</p>
                        
                        <div class="bg-white p-3 rounded border border-yellow-200 text-sm">
                            <div class="grid grid-cols-3 gap-4 mb-2">
                                <span class="font-semibold text-gray-600">Type</span>
                                <span class="font-semibold text-gray-600">Name</span>
                                <span class="font-semibold text-gray-600">Value / Target</span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 font-mono text-gray-800">
                                <span>CNAME</span>
                                <span>@ (or www)</span>
                                <span>{{ initialData.appHost }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-yellow-700 mt-2">Note: DNS propagation may take up to 24 hours.</p>
                    </div>
                </div>

                <div v-else>
                    <!-- New Request Form -->
                    <form @submit.prevent="submitRequest">
                        <div class="mb-6">
                            <label for="domain" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Domain Name</label>
                            <div class="relative rounded-md shadow-sm">
                                <input v-model="domain" type="text" id="domain" class="block w-full rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-4 pr-12 sm:text-sm" placeholder="e.g. myshop.com">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Enter your domain name without https:// or www.</p>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-bold text-blue-900 mb-1">Before you connect:</h4>
                            <p class="text-sm text-blue-800">
                                Make sure you have purchased the domain. You will need to add a <strong>CNAME Record</strong> pointing to 
                                <code class="bg-blue-100 px-1 py-0.5 rounded font-mono">{{ initialData.appHost }}</code> in your domain's DNS settings.
                            </p>
                        </div>

                        <button type="submit" :disabled="processing || !domain" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed">
                             {{ processing ? 'Submitting...' : 'Connect Domain' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialData: { type: Object, required: true },
    routes: { type: Object, required: true }
});

const currentRequest = ref(props.initialData.currentRequest);
const domain = ref('');
const processing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const submitRequest = async () => {
    processing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await axios.post(props.routes.domainRequest, { domain: domain.value });
        if (response.data.success) {
            currentRequest.value = {
                id: response.data.request.id,
                domain: response.data.request.domain,
                status: response.data.request.status,
                created_at: 'Just now' // Or format date
            };
            successMessage.value = response.data.message;
            domain.value = '';
        }
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            errorMessage.value = error.response.data.errors;
        } else if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = 'Failed to submit domain request.';
        }
    } finally {
        processing.value = false;
    }
};

const cancelRequest = async () => {
    if (!confirm('Are you sure you want to cancel this request?')) return;
    
    processing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const url = props.routes.domainCancel.replace(':id', currentRequest.value.id);
        const response = await axios.delete(url);
        
        if (response.data.success) {
            currentRequest.value = null;
            successMessage.value = response.data.message;
        }
    } catch (error) {
         if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
             errorMessage.value = 'Failed to cancel request.';
        }
    } finally {
        processing.value = false;
    }
};
</script>
