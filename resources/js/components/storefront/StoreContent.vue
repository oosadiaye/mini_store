<template>
    <div>
        <!-- TABS -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'home'"
                    :class="activeTab === 'home' ? 'border-[#0A2540] text-[#0A2540]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Home Content
                </button>
                <button @click="activeTab = 'pages'"
                    :class="activeTab === 'pages' ? 'border-[#0A2540] text-[#0A2540]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Page Content
                </button>
                <button @click="activeTab = 'policies'"
                    :class="activeTab === 'policies' ? 'border-[#0A2540] text-[#0A2540]' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                    Store Policies
                </button>
            </nav>
        </div>

        <div v-if="successMessage" class="bg-green-50 border-l-4 border-green-400 p-4 mb-4" role="alert">
            <p class="font-bold text-green-700">Success</p>
            <p class="text-sm text-green-600">{{ successMessage }}</p>
        </div>
        <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4" role="alert">
            <p class="font-bold text-red-700">Error</p>
            <p class="text-sm text-red-600">{{ errorMessage }}</p>
        </div>

        <form @submit.prevent="submitForm">
            <!-- TAB: HOME CONTENT -->
            <div v-show="activeTab === 'home'">
                <!-- HERO SECTION -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Hero Section</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Main Headline</label>
                                    <input type="text" v-model="form.hero_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sub-Headline</label>
                                    <textarea v-model="form.hero_subtitle" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Call to Action Text</label>
                                    <input type="text" v-model="form.cta_text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Hero Image</label>
                                <div class="mt-2 flex items-center justify-center w-full">
                                    <label class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                            <img v-if="previewHero" :src="previewHero" class="h-48 object-cover rounded-md mb-2">
                                            <div v-else>
                                                <svg class="w-8 h-8 mb-4 text-gray-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <p class="text-sm text-gray-500">Click to upload</p>
                                            </div>
                                        </div>
                                        <input type="file" class="hidden" accept="image/*" @change="handleFileChange($event, 'hero_image', 'previewHero')">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PROMOTIONAL BANNERS -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Promotional Banners (Split Banner)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Left Image</label>
                                <div class="mt-2 text-center">
                                    <img v-if="previewSplitLeft" :src="previewSplitLeft" class="h-32 object-cover rounded-md mx-auto mb-2">
                                    <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*" @change="handleFileChange($event, 'split_image_left', 'previewSplitLeft')">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Right Image</label>
                                <div class="mt-2 text-center">
                                    <img v-if="previewSplitRight" :src="previewSplitRight" class="h-32 object-cover rounded-md mx-auto mb-2">
                                    <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*" @change="handleFileChange($event, 'split_image_right', 'previewSplitRight')">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Center Title</label>
                            <input type="text" v-model="form.split_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- SECTION VISIBILITY -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Section Visibility</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.show_new_arrivals" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900">Show New Arrivals</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.show_best_sellers" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900">Show Best Sellers</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- CONTACT INFO -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Contact Info</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Support Email</label>
                                <input type="email" v-model="form.contact_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Social Media Links</label>
                                <div class="space-y-2 mt-2">
                                    <div v-for="(link, index) in form.social_links" :key="index" class="flex gap-2">
                                        <select v-model="link.platform" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-1/3">
                                            <option value="facebook">Facebook</option>
                                            <option value="instagram">Instagram</option>
                                            <option value="twitter">Twitter/X</option>
                                            <option value="linkedin">LinkedIn</option>
                                            <option value="tiktok">TikTok</option>
                                            <option value="youtube">YouTube</option>
                                        </select>
                                        <input type="url" v-model="link.url" placeholder="https://..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm flex-1">
                                        <button type="button" @click="removeLink(index)" class="text-red-500 hover:text-red-700">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <button type="button" @click="addLink" class="mt-2 text-sm text-indigo-600 hover:text-indigo-900 font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Add Social Link
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: PAGE CONTENT -->
            <div v-show="activeTab === 'pages'">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center border-b pb-2 mb-4">
                            <h3 class="text-lg font-medium text-gray-900">About Us Page</h3>
                            <span class="bg-gray-100 text-gray-600 text-xs font-mono px-2 py-1 rounded">/about-us</span>
                        </div>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">About Hero Image</label>
                                <div class="mt-2 text-center">
                                    <img v-if="previewAboutHero" :src="previewAboutHero" class="h-32 object-cover rounded-md mx-auto mb-2">
                                    <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" accept="image/*" @change="handleFileChange($event, 'about_hero_image', 'previewAboutHero')">
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Page Headline</label>
                                    <input type="text" v-model="form.about_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-sm font-medium text-gray-700">Main Story (Content)</label>
                                        <button type="button" @click="generateAIContent('content')" :disabled="isGenerating" class="text-xs font-bold text-white bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 px-3 py-1 rounded-full shadow-md transition transform hover:scale-105 disabled:opacity-50">
                                            {{ isGenerating ? 'Generating...' : '✨ AI Suggest' }}
                                        </button>
                                    </div>
                                    <textarea v-model="form.about_content" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                            </div>
                            <!-- Mission Section -->
                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-md font-bold text-gray-900">Mission Statement</h4>
                                    <button type="button" @click="generateAIContent('mission')" :disabled="isGenerating" class="text-xs font-bold text-white bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 px-3 py-1 rounded-full shadow-md transition transform hover:scale-105 disabled:opacity-50">
                                        {{ isGenerating ? 'Generating...' : '✨ AI Suggest' }}
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="md:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700">Mission Title</label>
                                        <input type="text" v-model="form.about_mission_title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700">Mission Text</label>
                                        <textarea v-model="form.about_mission_text" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- Stats Repeater -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Company Stats</label>
                                <div class="space-y-3 mt-2">
                                    <div v-for="(stat, index) in form.about_stats" :key="index" class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <div class="flex-1">
                                            <label class="block text-xs text-gray-500 mb-1">Label</label>
                                            <input type="text" v-model="stat.label" class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-xs text-gray-500 mb-1">Value</label>
                                            <input type="text" v-model="stat.value" class="block w-full text-sm font-bold border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <div class="pt-5">
                                            <button type="button" @click="removeStat(index)" class="text-red-400 hover:text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="addStat" class="mt-3 text-sm text-[#0A2540] hover:text-indigo-600 font-bold flex items-center bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg inline-block">
                                    <svg class="w-4 h-4 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Metric
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: POLICIES -->
            <div v-show="activeTab === 'policies'">
                <div class="space-y-6 mb-6">
                    <div v-for="policy in ['faq', 'shipping', 'returns']" :key="policy" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center border-b pb-2 mb-4">
                                <h3 class="text-lg font-medium text-gray-900 uppercase">{{ policy }} Policy</h3>
                                <button type="button" @click="generatePolicy(policy)" :disabled="isGenerating" class="text-xs font-bold text-white bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 px-3 py-1 rounded-full shadow-md transition transform hover:scale-105 disabled:opacity-50">
                                    {{ isGenerating ? 'Writing...' : '✨ AI Suggest' }}
                                </button>
                            </div>
                            <textarea v-model="form['policy_' + policy]" rows="10" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm font-mono text-sm"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end sticky bottom-4 z-10">
                <button type="submit" :disabled="processing" class="bg-[#0A2540] hover:bg-gray-800 text-white font-bold py-3 px-8 rounded-full shadow-xl hover:shadow-2xl transition transform hover:-translate-y-1 flex items-center">
                    <span v-if="processing" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    initialContent: { type: Object, default: () => ({}) },
    initialContact: { type: Object, default: () => ({ social_links: [] }) },
    initialSplitBanner: { type: Object, default: () => ({}) },
    initialAboutUs: { type: Object, default: () => ({ stats: [] }) },
    initialPolicies: { type: Object, default: () => ({}) },
    showNewArrivals: { type: Boolean, default: false },
    showBestSellers: { type: Boolean, default: false },
    routes: { type: Object, required: true },
    config: { type: Object, default: () => ({}) },
});

const activeTab = ref('home');
const processing = ref(false);
const isGenerating = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Previews
const previewHero = ref(null);
const previewSplitLeft = ref(null);
const previewSplitRight = ref(null);
const previewAboutHero = ref(null);

// Form Data - Flattened structure matching Controller validation
const form = reactive({
    hero_title: props.initialContent.hero_title || '',
    hero_subtitle: props.initialContent.hero_subtitle || '',
    cta_text: props.initialContent.cta_text || '',
    hero_image: null,
    
    split_title: props.initialSplitBanner.center_text?.title || '',
    split_image_left: null,
    split_image_right: null,
    
    show_new_arrivals: props.showNewArrivals,
    show_best_sellers: props.showBestSellers,
    
    contact_email: props.initialContact.email || '',
    social_links: props.initialContact.social_links || [],
    
    about_title: props.initialAboutUs.title || '',
    about_content: props.initialAboutUs.content || '',
    about_mission_title: props.initialAboutUs.mission_title || '',
    about_mission_text: props.initialAboutUs.mission_text || '',
    about_hero_image: null,
    about_stats: props.initialAboutUs.stats || [],
    
    policy_faq: props.initialPolicies.faq || '',
    policy_shipping: props.initialPolicies.shipping || '',
    policy_returns: props.initialPolicies.returns || ''
});

// Initialize image previews if URLs exist
const initPreviews = () => {
    if (props.initialContent.banner_image) previewHero.value = getMediaUrl(props.initialContent.banner_image);
    if (props.initialSplitBanner.image_left) previewSplitLeft.value = getMediaUrl(props.initialSplitBanner.image_left);
    if (props.initialSplitBanner.image_right) previewSplitRight.value = getMediaUrl(props.initialSplitBanner.image_right);
    if (props.initialAboutUs.hero_image) previewAboutHero.value = getMediaUrl(props.initialAboutUs.hero_image);
};

const getMediaUrl = (path) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    // Construct route: tenant.media needs tenant slug
    // We can assume slug is available or passed. 
    // Wait, the prop pass route('tenant.media') logic in blade was helpful.
    // Ideally we should pass full URLs from backend props.
    // For now assuming the backend passes FULL URLs or we handle it. 
    // Actually, let's use a prop for mediaBaseUrl if needed, or rely on blade passing resolved URLs.
    // The blade currently does: route('tenant.media', ['path' => ...])
    // So let's try to assume the backend passes resolved string.
    // If not, we might need a helper method.
    return props.routes.media_base + '?path=' + path; // Simplified approach
};

const handleFileChange = (event, fieldName, previewRefName) => {
    const file = event.target.files[0];
    if (file) {
        form[fieldName] = file;
        
        const reader = new FileReader();
        reader.onload = (e) => {
            if (previewRefName === 'previewHero') previewHero.value = e.target.result;
            if (previewRefName === 'previewSplitLeft') previewSplitLeft.value = e.target.result;
            if (previewRefName === 'previewSplitRight') previewSplitRight.value = e.target.result;
            if (previewRefName === 'previewAboutHero') previewAboutHero.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const addLink = () => {
    form.social_links.push({ platform: 'facebook', url: '' });
};
const removeLink = (index) => {
    form.social_links.splice(index, 1);
};

const addStat = () => {
    form.about_stats.push({ label: '', value: '' });
};
const removeStat = (index) => {
    form.about_stats.splice(index, 1);
};

const submitForm = async () => {
    processing.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    const formData = new FormData();
    
    // Append simple fields
    for (const key in form) {
        if (key === 'social_links' || key === 'about_stats') continue;
        if (form[key] !== null && form[key] !== undefined) {
             // Boolean fix
             if (typeof form[key] === 'boolean') {
                 formData.append(key, form[key] ? '1' : '0');
             } else {
                 formData.append(key, form[key]);
             }
        }
    }

    // Append Arrays
    form.social_links.forEach((link, index) => {
        formData.append(`social_links[${index}][platform]`, link.platform);
        formData.append(`social_links[${index}][url]`, link.url);
    });

    form.about_stats.forEach((stat, index) => {
        formData.append(`about_stats[${index}][label]`, stat.label);
        formData.append(`about_stats[${index}][value]`, stat.value);
    });

    try {
        await axios.post(props.routes.update, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        successMessage.value = 'Store content updated successfully!';
        window.scrollTo(0, 0);
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data && error.response.data.errors) {
            errorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
        } else {
            errorMessage.value = 'Failed to update content.';
        }
        window.scrollTo(0, 0);
    } finally {
        processing.value = false;
    }
};

// AI Generators (Simulated & API)
const generateAIContent = async (field) => {
    isGenerating.value = true;
    await new Promise(resolve => setTimeout(resolve, 800)); // Sim delay

    const industry = props.config.industry || 'fashion';
    const storeName = props.config.store_name || 'Our Store';

    let templates = {
        fashion: {
            mission: 'To empower individuals to express their unique identity through timeless style.',
            story: `We started ${storeName} with a simple belief: fashion should be accessible, sustainable, and personal.`,
            mission_title: 'Our Mission'
        },
        electronics: {
            mission: 'Bringing the future to your doorstep with cutting-edge technology.',
            story: `Technology moves fast. At ${storeName}, we curate the best gadgets to help you stay ahead of the curve.`,
            mission_title: 'Our Mission'
        },
        grocery: {
            mission: 'Farm-fresh quality for healthy, happy families.',
            story: `We believe good food brings people together. That’s why we source only the freshest ingredients from trusted local farmers and deliver them straight to your table.`,
             mission_title: 'Our Mission'
        }
    };

    const selectedTemplate = templates[industry.toLowerCase()] || templates['fashion'];

    if (field === 'mission') {
        form.about_mission_text = selectedTemplate.mission;
        if (!form.about_mission_title) form.about_mission_title = selectedTemplate.mission_title;
    } else if (field === 'content') {
        form.about_content = selectedTemplate.story;
    }
    isGenerating.value = false;
};

const generatePolicy = async (type) => {
    isGenerating.value = true;
    try {
        const response = await axios.post(props.routes.generate_policy, { type });
        if (response.data.content) {
            form['policy_' + type] = response.data.content;
        }
    } catch (error) {
        alert('Failed to generate policy.');
    } finally {
        isGenerating.value = false;
    }
};

onMounted(() => {
    initPreviews();
});
</script>
