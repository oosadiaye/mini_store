<template>
  <div class="flex h-full bg-gray-100 overflow-hidden">
    <!-- LEFT SIDEBAR: Controls -->
    <div class="w-80 bg-white border-r flex flex-col shadow-lg z-10">
      <!-- Header -->
      <div class="h-14 border-b flex items-center px-4 justify-between bg-gray-50">
        <h2 class="font-bold text-gray-700">Theme Editor</h2>
        <button 
          @click="saveLayout" 
          :disabled="saving"
          class="px-3 py-1 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 disabled:opacity-50 transition-colors"
        >
          <span v-if="saving">Saving...</span>
          <span v-else>Save</span>
        </button>
      </div>

      <!-- Mode Switcher -->
      <div class="flex border-b">
        <button 
          @click="activeTab = 'sections'"
          class="flex-1 py-3 text-sm font-medium transition-colors"
          :class="activeTab === 'sections' ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50' : 'text-gray-500 hover:bg-gray-50'"
        >
          Add Sections
        </button>
        <button 
          @click="activeTab = 'edit'"
          class="flex-1 py-3 text-sm font-medium transition-colors"
          :class="activeTab === 'edit' ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50' : 'text-gray-500 hover:bg-gray-50'"
        >
          Edit Properties
        </button>
      </div>

      <!-- TAB: ADD SECTIONS -->
      <div v-show="activeTab === 'sections'" class="flex-1 overflow-y-auto p-4">
        <p class="text-xs text-gray-400 uppercase font-bold mb-3 tracking-wider">Available Blocks</p>
        <VueDraggable
          v-model="availableBlocks"
          :group="{ name: 'sections', pull: 'clone', put: false }"
          :sort="false"
          :clone="cloneSection"
          class="space-y-3"
        >
          <div 
            v-for="block in availableBlocks" 
            :key="block.type"
            class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm cursor-move hover:border-indigo-300 hover:ring-1 hover:ring-indigo-300 transition-all group"
          >
            <div class="flex items-center">
              <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500 mr-3 group-hover:bg-indigo-100 group-hover:text-indigo-600">
                <i :class="block.icon"></i>
              </div>
              <div>
                <span class="block font-medium text-sm text-gray-700">{{ block.label }}</span>
                <span class="block text-xs text-gray-400">{{ block.description }}</span>
              </div>
            </div>
          </div>
        </VueDraggable>
      </div>

      <!-- TAB: EDIT PROPERTIES -->
      <div v-show="activeTab === 'edit'" class="flex-1 overflow-y-auto p-4">
        <div v-if="selectedSection">
          <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800 text-sm">{{ getBlockLabel(selectedSection.type) }} Settings</h3>
             <button @click="deleteSection(selectedSection.id)" class="text-red-500 hover:text-red-700 text-xs hover:underline">
              Delete
            </button>
          </div>

          <!-- Dynamic Form Fields -->
          <div class="space-y-4">
            <!-- HERO_BANNER PROPS -->
            <div v-if="selectedSection.type === 'hero_banner'">
              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
                <input v-model="selectedSection.props.title" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Subtitle</label>
                <input v-model="selectedSection.props.subtitle" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Background Color</label>
                <div class="flex items-center gap-2">
                   <input v-model="selectedSection.props.bg_color" type="color" class="h-8 w-12 border cursor-pointer rounded">
                   <input v-model="selectedSection.props.bg_color" type="text" class="flex-1 text-sm border-gray-300 rounded uppercase">
                </div>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Text Color</label>
                <div class="flex items-center gap-2">
                   <input v-model="selectedSection.props.text_color" type="color" class="h-8 w-12 border cursor-pointer rounded">
                   <input v-model="selectedSection.props.text_color" type="text" class="flex-1 text-sm border-gray-300 rounded uppercase">
                </div>
              </div>
            </div>

            <!-- PRODUCT_GRID PROPS -->
            <div v-if="selectedSection.type === 'product_grid'">
              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Title</label>
                <input v-model="selectedSection.props.title" type="text" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Product Limit</label>
                <input v-model="selectedSection.props.limit" type="number" min="2" max="12" class="w-full text-sm border-gray-300 rounded focus:ring-indigo-500 focus:border-indigo-500">
              </div>
            </div>
            
            <div class="mt-6 pt-6 border-t">
                 <p class="text-xs text-gray-400 text-center">Changes are applied immediately to the preview.</p>
            </div>
          </div>
        </div>
        
        <div v-else class="h-full flex flex-col items-center justify-center text-gray-400 text-center">
            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            <p class="text-sm">Select a section in the preview to edit properties.</p>
        </div>
      </div>
    </div>

    <!-- RIGHT CANVAS: Preview -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-100 h-full overflow-hidden">
        <!-- Toolbar -->
        <div class="h-10 bg-white border-b flex items-center justify-center text-xs text-gray-500 shadow-sm z-10">
            Preview Mode • Desktop
        </div>
        
        <!-- Scrollable Area -->
        <div class="flex-1 overflow-y-auto p-8 flex justify-center custom-scrollbar">
            <!-- The "Phone/Desktop" Frame -->
            <div class="w-full max-w-5xl bg-white shadow-2xl min-h-[800px] flex flex-col transition-all duration-300">
                 <!-- Draggable Container -->
                 <VueDraggable
                    v-model="sections"
                    group="sections"
                    handle=".drag-handle"
                    class="flex-1 min-h-[200px]"
                    item-key="id"
                 >
                    <template #item="{ element }">
                        <div 
                           class="relative group border-2 transition-all duration-200"
                           :class="selectedSection?.id === element.id ? 'border-indigo-500 z-10' : 'border-transparent hover:border-indigo-300'"
                           @click.stop="selectSection(element)"
                        >
                           <!-- Hover/Active Actions Overlay -->
                           <div class="absolute top-0 right-0 p-2 opacity-0 group-hover:opacity-100 transition-opacity z-20 flex space-x-1">
                                <button class="drag-handle p-1.5 bg-indigo-600 text-white rounded cursor-move shadow-sm hover:bg-indigo-700" title="Drag to reorder">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                </button>
                                <button @click.stop="deleteSection(element.id)" class="p-1.5 bg-red-600 text-white rounded shadow-sm hover:bg-red-700" title="Remove section">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                           </div>
                           
                           <!-- Dynamic Component Rendering -->
                           <component 
                             :is="getComponent(element.type)" 
                             :props="element.props" 
                           />
                        </div>
                    </template>
                 </VueDraggable>
                 
                 <div v-if="sections.length === 0" class="flex-1 flex flex-col items-center justify-center text-gray-400 p-12 border-2 border-dashed border-gray-200 m-8 rounded-lg bg-gray-50">
                     <p>Drag sections here from the sidebar</p>
                 </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineAsyncComponent, onMounted } from 'vue';
import { VueDraggable } from 'vue-draggable-plus';
import axios from 'axios';

// --- Components ---
const SectionHero = defineAsyncComponent(() => import('./sections/SectionHero.vue'));
const SectionProductList = defineAsyncComponent(() => import('./sections/SectionProductList.vue'));

const props = defineProps({
  initialLayout: {
    type: Array,
    default: () => []
  },
  saveUrl: {
    type: String,
    required: true
  }
});

// --- State ---
const activeTab = ref('sections');
const sections = ref(props.initialLayout); // The source of truth for the layout
const selectedSection = ref(null);
const saving = ref(false);

const availableBlocks = ref([
  { 
    type: 'hero_banner', 
    label: 'Hero Banner', 
    description: 'Large banner with title and background.', 
    icon: 'fa-regular fa-image',
    defaultProps: { title: 'Welcome', subtitle: 'Subtitle Text', bg_color: '#1a1a1a', text_color: '#ffffff' }
  },
  { 
    type: 'product_grid', 
    label: 'Product Grid', 
    description: 'Grid of your latest products.', 
    icon: 'fa-solid fa-grid',
    defaultProps: { title: 'New Arrivals', limit: 4 }
  }
]);

// --- Methods ---

const getComponent = (type) => {
  switch (type) {
    case 'hero_banner': return SectionHero;
    case 'product_grid': return SectionProductList;
    default: return 'div'; // Fallback
  }
};

const getBlockLabel = (type) => {
    const block = availableBlocks.value.find(b => b.type === type);
    return block ? block.label : type;
}

// Logic to clone a block from sidebar to canvas
// Assigns a unique ID to the new instance
const cloneSection = (block) => {
  return {
    id: 'section-' + Date.now() + Math.random().toString(36).substr(2, 9),
    type: block.type,
    props: JSON.parse(JSON.stringify(block.defaultProps)) // Deep clone props
  };
};

const selectSection = (section) => {
  selectedSection.value = section;
  activeTab.value = 'edit';
};

const deleteSection = (id) => {
  sections.value = sections.value.filter(s => s.id !== id);
  if (selectedSection.value && selectedSection.value.id === id) {
    selectedSection.value = null;
    activeTab.value = 'sections';
  }
};

const saveLayout = async () => {
  saving.value = true;
  try {
    await axios.post(props.saveUrl, {
      theme_layout: sections.value
    });
    // Optional: Toast notification here
    alert('Layout saved successfully!');
  } catch (error) {
    console.error('Failed to save layout:', error);
    alert('Failed to save layout.');
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(0,0,0,0.1);
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(0,0,0,0.2);
}
</style>
