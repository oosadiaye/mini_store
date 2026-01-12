<template>
    <div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Action</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="msg in localMessages" :key="msg.id" class="hover:bg-gray-50 cursor-pointer transition-colors" @click="openMessage(msg)">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-block h-3 w-3 rounded-full transition-colors duration-300"
                                          :class="msg.is_read ? 'bg-gray-300' : 'bg-blue-600'">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ msg.name }}</div>
                                    <div class="text-sm text-gray-500">{{ msg.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ truncate(msg.subject, 40) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(msg.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click.stop="openMessage(msg)" class="text-indigo-600 hover:text-indigo-900">View</button>
                                </td>
                            </tr>
                            
                            <tr v-if="localMessages.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No messages found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Slot for Pagination -->
                <div class="mt-4">
                    <slot name="pagination"></slot>
                </div>
            </div>
        </div>

        <!-- Slide-over / Modal for Message Details -->
        <Teleport to="body">
            <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden" role="dialog" aria-modal="true">
                <div class="absolute inset-0 overflow-hidden">
                    <!-- Backdrop -->
                    <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                         @click="closeModal"
                         aria-hidden="true"></div>

                    <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                        <!-- Drawer Panel -->
                        <div class="w-screen max-w-md transform transition ease-in-out duration-500 sm:duration-700"
                             :class="isOpen ? 'translate-x-0' : 'translate-x-full'">
                             
                            <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                                <div class="py-6 px-4 sm:px-6 bg-[#0A2540]">
                                    <div class="flex items-center justify-between">
                                        <h2 class="text-lg font-medium text-white">Message Details</h2>
                                        <div class="ml-3 h-7 flex items-center">
                                            <button type="button" class="bg-[#0A2540] rounded-md text-gray-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white" @click="closeModal">
                                                <span class="sr-only">Close panel</span>
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <p class="text-sm text-blue-100">
                                            Received on {{ formatDate(activeMessage.created_at) }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="relative flex-1 py-6 px-4 sm:px-6">
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">From</label>
                                            <div class="mt-1 flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold uppercase mr-3">
                                                    {{ activeMessage.name ? activeMessage.name.charAt(0) : '?' }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ activeMessage.name }}</p>
                                                    <a :href="'mailto:' + activeMessage.email" class="text-sm text-indigo-600 hover:text-indigo-900">{{ activeMessage.email }}</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Subject</label>
                                            <div class="mt-1 text-sm text-gray-900 font-semibold">{{ activeMessage.subject }}</div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-500">Message</label>
                                            <div class="mt-2 text-sm text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-wrap leading-relaxed border border-gray-100">{{ activeMessage.message }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 py-6 px-4 sm:px-6">
                                    <a :href="'mailto:' + activeMessage.email + '?subject=Re: ' + activeMessage.subject" 
                                       class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Reply via Email
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialMessages: { type: Array, default: () => [] },
    readUrlTemplate: { type: String, required: true },
    csrfToken: String
});

const localMessages = ref([...props.initialMessages]);
const isOpen = ref(false);
const activeMessage = ref({});

const openMessage = async (msg) => {
    activeMessage.value = msg;
    isOpen.value = true;

    if (!msg.is_read) {
        // Optimistic update
        const messageIndex = localMessages.value.findIndex(m => m.id === msg.id);
        if (messageIndex !== -1) {
            localMessages.value[messageIndex].is_read = true;
        }

        try {
            const url = props.readUrlTemplate.replace(':id', msg.id);
            await axios.post(url);
        } catch (e) {
            console.error('Failed to mark message as read', e);
            // Revert on failure? Maybe unnecessary for read status.
        }
    }
};

const closeModal = () => {
    isOpen.value = false;
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true });
};

const truncate = (text, length) => {
    if (!text) return '';
    return text.length > length ? text.substring(0, length) + '...' : text;
};

// If props update (e.g. navigation without reload, though unlikely in this hybrid setup)
watch(() => props.initialMessages, (newVal) => {
    localMessages.value = [...newVal];
});
</script>
