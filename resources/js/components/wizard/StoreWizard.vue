<template>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Segmented Modern Progress Bar -->
            <div class="mb-12">
                <div class="flex items-center space-x-4">
                    <div v-for="i in 4" :key="i" class="flex-1">
                        <div class="stepper-segment bg-gray-200">
                            <div class="h-full rounded-full transition-all duration-700"
                                    :class="i <= step ? 'bg-[#0A2540]' : ''"
                                    :style="{ width: i < step ? '100%' : (i === step ? '100%' : '0%') }">
                            </div>
                        </div>
                        <div class="mt-3 flex items-center">
                            <span class="text-[10px] font-bold tracking-widest uppercase transition-colors"
                                    :class="i <= step ? 'text-[#0A2540]' : 'text-gray-400'">
                                {{ ['Identity', 'Niche', 'Catalog', 'Layout'][i-1] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden wizard-card border border-gray-100 p-6 text-gray-900 rounded-2xl shadow-lg">
                <!-- STEP 1: IDENTITY -->
                <div v-if="step === 1" class="animate-fade-in-right">
                    <h3 class="text-xl font-bold text-[#0A2540] mb-6">Brand Identity</h3>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Store Name</label>
                            <input v-model="form.store_name" type="text" class="block w-full input-premium py-3 px-4 rounded-xl shadow-sm border-2 border-gray-200 focus:border-[#0A2540] focus:ring-[#0A2540]/10" placeholder="e.g. Acme Corp Marketplace" required autofocus>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Primary Brand Color</label>
                                <div class="flex items-center gap-3 mt-1">
                                    <div class="relative">
                                        <input type="color" v-model="form.brand_color" class="h-12 w-12 border-0 rounded-xl cursor-pointer p-0 overflow-hidden shadow-sm">
                                    </div>
                                    <input type="text" v-model="form.brand_color" class="block flex-1 input-premium py-3 px-4 rounded-xl shadow-sm font-mono text-sm border-2 border-gray-200">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Upload Logo</label>
                                <div class="mt-1 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-100 border-dashed rounded-xl hover:border-[#0A2540] transition-colors group cursor-pointer relative">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-8 w-8 text-gray-400 group-hover:text-[#0A2540] transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-xs text-gray-600">
                                            <span class="relative cursor-pointer bg-white rounded-md font-bold text-[#0A2540] hover:text-navy-soft focus-within:outline-none">Click to upload</span>
                                        </div>
                                         <p v-if="logoFileName" class="text-xs text-green-600 font-bold mt-2">{{ logoFileName }}</p>
                                    </div>
                                    <input type="file" @change="handleFileUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: INDUSTRY -->
                <div v-if="step === 2" class="animate-fade-in-right">
                    <h3 class="text-xl font-bold text-[#0A2540] mb-2">Select Your Industry</h3>
                    <p class="text-sm text-gray-500 mb-8">This helps us auto-configure your store's look and feel.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 gap-6">
                        <div v-for="ind in industries" :key="ind.id" 
                                @click="selectIndustry(ind.id)"
                                :class="{'border-[#0A2540] ring-2 ring-[#0A2540]/10 bg-gray-50': form.industry === ind.id, 'border-gray-100 bg-white': form.industry !== ind.id}"
                                class="group p-6 border rounded-2xl cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative overflow-hidden h-full flex flex-col justify-between">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-5xl group-hover:scale-110 transition-transform duration-500">{{ ind.icon }}</div>
                                <div v-show="form.industry === ind.id" class="text-white bg-[#0A2540] rounded-full p-1 shadow-md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                            
                            <div>
                                <div class="font-bold text-[#0A2540] text-lg">{{ ind.label }}</div>
                                <div class="text-sm text-gray-500 mt-2 leading-relaxed">{{ ind.desc }}</div>
                            </div>

                             <!-- Subtle Background Pattern/Accent -->
                            <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-[#0A2540]/5 rounded-full blur-2xl group-hover:bg-[#0A2540]/10 transition-colors"></div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: CATALOG -->
                <div v-if="step === 3" class="animate-fade-in-right">
                    <catalog-curator v-model="catalogData"></catalog-curator>
                </div>

                <!-- STEP 4: LAYOUT -->
                <div v-if="step === 4" class="animate-fade-in-right">
                    <h3 class="text-xl font-bold text-[#0A2540] mb-2">Choose Layout Style</h3>
                    <p class="text-sm text-gray-500 mb-8">Select the interface that best represents your brand's experience.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                         <div v-for="(preset, key) in layoutPresets" :key="key" 
                                @click="selectLayout(key)" 
                                :class="{'border-[#0A2540] ring-4 ring-[#0A2540]/5 bg-gray-50': form.layout_preference === key, 'border-gray-100 bg-white': form.layout_preference !== key}"
                                class="group border-2 rounded-2xl overflow-hidden cursor-pointer transition-all duration-300 hover:shadow-xl hover:-translate-y-1 relative flex flex-col h-full">
                            
                            <!-- Dynamic Wireframe -->
                            <div class="bg-gray-50 h-40 border-b border-gray-100 relative group-hover:bg-gray-100 transition-colors">
                                <div v-html="preset.wireframe" class="w-full h-full p-6 text-gray-200 group-hover:text-gray-400 transition-colors"></div>
                                <!-- Active Checkmark -->
                                <div v-show="form.layout_preference === key" class="absolute top-3 right-3 text-white bg-[#0A2540] rounded-full p-1.5 shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </div>
                            
                            <div class="p-5 flex-1 flex flex-col">
                                <h4 class="font-bold text-[#0A2540] text-lg">{{ preset.label }}</h4>
                                <p class="text-xs text-gray-500 mt-2 mb-4 leading-relaxed flex-1">{{ preset.description }}</p>
                                
                                <!-- Tags -->
                                <div class="flex gap-2 flex-wrap">
                                    <span v-for="tag in preset.tags" :key="tag" class="text-[9px] uppercase font-black text-[#0A2540] bg-[#0A2540]/5 px-2 py-1 rounded-md tracking-widest">{{ tag }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Schema Preview -->
                    <div v-if="generatedSchema" class="mt-12 rounded-2xl overflow-hidden shadow-2xl border border-gray-800">
                        <div class="bg-[#0A2540] px-4 py-2 flex items-center justify-between">
                            <div class="flex space-x-2">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">generated_theme_schema.json</div>
                        </div>
                        <div class="bg-[#1a1a2e] p-6 text-xs font-mono leading-relaxed">
                            <pre class="text-blue-300 overflow-x-auto">{{ JSON.stringify(generatedSchema, null, 2) }}</pre>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="mt-10 flex justify-between items-center border-t border-gray-100 pt-8">
                    <button v-show="step > 1" @click="step--" class="text-gray-400 hover:text-[#0A2540] font-bold text-sm uppercase tracking-widest transition-colors flex items-center group">
                        <span class="mr-2 group-hover:-translate-x-1 transition-transform">&larr;</span> Back
                    </button>
                    <div v-show="step === 1"></div> <!-- Spacer -->

                    <button v-show="step < 4" @click="saveAndNext" :disabled="processing"
                            class="bg-[#0A2540] hover:bg-navy-soft text-white font-bold py-4 px-10 rounded-xl transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center group text-sm uppercase tracking-widest disabled:opacity-50">
                        <span v-if="processing" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                        Next Step <span class="ml-3 group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </button>

                    <button v-show="step === 4" @click="finishWizard" :disabled="processing"
                            class="bg-[#0A2540] hover:bg-navy-soft text-white font-bold py-4 px-10 rounded-xl transition shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex items-center animate-pulse text-sm uppercase tracking-widest disabled:opacity-50">
                         <span v-if="processing" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>
                        Create My Store
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import CatalogCurator from './CatalogCurator.vue';
import axios from 'axios';

const props = defineProps({
    storeConfig: { type: Object, required: true },
    categories: { type: Array, required: true },
    routes: { type: Object, required: true },
    csrfToken: String
});

const step = ref(1);
const processing = ref(false);
const logoFileName = ref('');

const form = reactive({
    store_name: props.storeConfig.store_name || '',
    brand_color: props.storeConfig.brand_color || '#3b82f6',
    industry: props.storeConfig.industry || '',
    selected_categories: props.storeConfig.selected_categories || [],
    layout_preference: props.storeConfig.layout_preference || 'minimal',
    logo: null
});

// Deep copy of categories for local manipulation
const catalogData = ref(JSON.parse(JSON.stringify(props.categories)));

const industries = [
    { id: 'fashion', label: 'Fashion', icon: '👗', desc: 'Stylish fonts & large images' },
    { id: 'electronics', label: 'Electronics', icon: '📱', desc: 'Tech-focused & clean' },
    { id: 'grocery', label: 'Grocery', icon: '🍎', desc: 'Data-dense & practical' },
    { id: 'hardware', label: 'Hardware', icon: '🛠️', desc: 'Bold & industrial' }
];

const industryPresets = {
    fashion: {
        label: 'Fashion & Apparel',
        icon: '🛍️',
        fonts: { heading: 'Playfair Display', body: 'Lato' },
        radius: '0px',
        vibe: 'minimalist',
        colors: { primary: ['#000000', '#F3F4F6'] }, // Adjusted structure slightly for simpler access
        // ... (rest of presets not strictly needed for logic but good for completeness if we used them)
    },
    electronics: {
        label: 'Electronics & Gadgets',
        icon: '🔌',
        fonts: { heading: 'Roboto', body: 'Inter' },
        radius: '4px',
        vibe: 'dark_mode',
        colors: { primary: ['#0EA5E9', '#1E293B'] }
    },
    grocery: {
        label: 'Grocery & Essentials',
        icon: '🥦',
        fonts: { heading: 'Poppins', body: 'Open Sans' },
        radius: '8px',
        vibe: 'fresh',
        colors: { primary: ['#10B981', '#ECFDF5'] }
    },
    hardware: {
        label: 'Hardware & Tools',
        icon: '🛠️',
        fonts: { heading: 'Oswald', body: 'Roboto Condensed' },
        radius: '2px',
        vibe: 'bold',
        colors: { primary: ['#F59E0B', '#FFFBEB'] }
    }
};

const layoutPresets = {
    high_volume: {
        label: 'High Volume Mart',
        description: 'Amazon Style. Fast search, dense categories. Best for large inventories.',
        tags: ['Search', 'Density', 'Hero'],
        structure: ['HeroBanner', 'SearchFocus', 'CategoryGrid', 'FeaturedProductCarousel', 'Footer'],
        wireframe: `
            <div class="h-full flex flex-col items-center justify-center p-2">
                <div class="w-full h-1/4 bg-gray-800 rounded-sm mb-2 flex items-center justify-center text-[8px] text-white/50">HERO</div>
                <div class="w-2/3 h-3 bg-white rounded shadow-sm mb-2 border border-gray-100"></div>
                <div class="grid grid-cols-4 gap-1 w-full flex-1">
                    <div class="bg-blue-50 h-full rounded border border-blue-100"></div><div class="bg-blue-50 h-full rounded border border-blue-100"></div>
                    <div class="bg-blue-50 h-full rounded border border-blue-100"></div><div class="bg-blue-50 h-full rounded border border-blue-100"></div>
                </div>
            </div>`
    },
    brand_showcase: {
        label: 'Brand Showcase',
        description: 'Nike Style. Emotional connection, big visuals, brand story',
        tags: ['Hero', 'Story'],
        structure: ['HeroBanner', 'TextBlock', 'FeaturedProductCarousel', 'Newsletter', 'Footer'],
        wireframe: `
            <div class="h-full flex flex-col">
                <div class="w-full h-3/5 bg-gray-800 flex items-center justify-center text-white/20 text-xs">HERO</div>
                <div class="w-full h-2/5 bg-white flex items-center justify-center text-[8px] text-gray-300 p-2 text-center">Brand Story Block</div>
            </div>`
    },
    quick_order: {
        label: 'Quick Order',
        description: 'Wholesale/B2B. List view for efficiency. No fluff.',
        tags: ['Tabular', 'Fast', 'Hero'],
        structure: ['HeroBanner', 'QuickOrderTable', 'Footer'],
        wireframe: `
            <div class="h-full p-2 bg-gray-50 flex flex-col">
                <div class="w-full h-1/4 bg-gray-800 rounded-sm mb-2 flex items-center justify-center text-[8px] text-white/50">HERO</div>
                <div class="w-full h-3 bg-gray-300 rounded mb-2"></div>
                <div class="space-y-1 flex-1">
                    <div class="w-full h-1.5 bg-white rounded border border-gray-200"></div>
                    <div class="w-full h-1.5 bg-white rounded border border-gray-200"></div>
                    <div class="w-full h-1.5 bg-white rounded border border-gray-200"></div>
                    <div class="w-full h-1.5 bg-white rounded border border-gray-200"></div>
                </div>
            </div>`
    }
};

const generatedSchema = ref(null);

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.logo = file;
        logoFileName.value = file.name;
    }
};

const selectIndustry = (id) => {
    form.industry = id;
    if (industryPresets[id]) {
        // Apply only the color if not set? Or always override?
        // Logic says auto-configure look and feel.
        // Array issue: Alpine logic was `preset.colors[0]` vs my struct `preset.colors.primary[0]`
        // Let's assume simpler structure or use first color.
        const color = industryPresets[id].colors.primary[0];
        form.brand_color = color; 
    }
};

const selectLayout = (key) => {
    form.layout_preference = key;
    generatePageSchema(key);
};

const generatePageSchema = (layoutKey) => {
    if (!layoutKey || !layoutPresets[layoutKey]) return;

    const preset = layoutPresets[layoutKey];
    
    // Flatten visible categories for mocking injected data
    const allCategories = [];
        const collectVisible = (nodes) => {
        nodes.forEach(node => {
            if (node.is_visible_online) {
                allCategories.push({ id: node.id, name: node.name });
            }
            if (node.children && node.children.length) {
                collectVisible(node.children);
            }
        });
    };
    collectVisible(catalogData.value);
    
    const highlightedCats = allCategories.slice(0, 3);

    generatedSchema.value = {
        layout_mode: layoutKey,
        components: preset.structure,
        injected_data: {
            hero_title: `Welcome to ${form.store_name || 'My Store'}`,
            hero_subtitle: `Browse our ${highlightedCats.length} curated collections.`,
            highlight_categories: highlightedCats
        },
        timestamp: new Date().toISOString()
    };
};

const submitStep = async () => {
    const formData = new FormData();
    formData.append('step', step.value);
    formData.append('store_name', form.store_name);
    formData.append('brand_color', form.brand_color);
    if (form.industry) formData.append('industry', form.industry);
    if (form.layout_preference) formData.append('layout_preference', form.layout_preference);
    if (form.logo) formData.append('logo', form.logo);

    if (step.value === 3) {
        // Send flattened structure or just tree?
        // Original logic sent 'catalog_curation' as JSON string of tree
        formData.append('catalog_curation', JSON.stringify(catalogData.value));
    }

    try {
        await axios.post(props.routes.update, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
    } catch (e) {
        console.error(e);
        throw e; // propagate to caller
    }
};

const saveAndNext = async () => {
    if (step.value === 1 && !form.store_name) return alert('Please enter a store name');
    if (step.value === 2 && !form.industry) return alert('Please select an industry');

    processing.value = true;
    try {
        await submitStep();
        step.value++;
    } catch (e) {
        alert('Error saving progress. Please try again.');
    } finally {
        processing.value = false;
    }
};

const finishWizard = async () => {
    if (!form.layout_preference) return alert('Please select a layout');
    
    processing.value = true;
    try {
        // Save final step first
        await submitStep();

        // Prepare finish payload
        const formData = new FormData();
        
        // Logic for navigation_categories: flatten tree
        const allCategories = [];
        const collectVisible = (nodes) => {
            nodes.forEach(node => {
                if (node.is_visible_online) {
                    allCategories.push({
                        id: node.id,
                        name: node.name,
                        slug: node.slug
                    });
                }
                if (node.children && node.children.length) {
                    collectVisible(node.children);
                }
            });
        };
        collectVisible(catalogData.value);
        
        formData.append('navigation_categories', JSON.stringify(allCategories));

        const res = await axios.post(props.routes.finish, formData);
        
        if (res.data.success && res.data.redirect) {
            window.location.href = res.data.redirect;
        }
    } catch (e) {
        console.error(e);
        alert('Something went wrong generating your store. Please try again.');
        processing.value = false;
    }
};

onMounted(() => {
    if (form.layout_preference) {
        generatePageSchema(form.layout_preference);
    }
});
</script>

<style scoped>
.wizard-card {
    border-radius: 16px;
    box-shadow: 0 4px 6px -1px rgba(10, 37, 64, 0.1), 0 2px 4px -1px rgba(10, 37, 64, 0.1);
}
.input-premium {
    transition: all 0.2s ease;
}
.input-premium:focus {
    box-shadow: 0 0 0 4px rgba(10, 37, 64, 0.1);
}
.stepper-segment {
    height: 6px;
    border-radius: 3px;
    position: relative;
    overflow: hidden;
}

/* Transitions */
.animate-fade-in-right {
    animation: fadeInRight 0.3s ease-out;
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
