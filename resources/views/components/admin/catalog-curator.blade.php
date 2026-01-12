@props(['tree'])

<div x-data='catalogCurator(@json($tree))' class="space-y-4">
    <div class="bg-gray-50/50 p-6 rounded-2xl border border-gray-100">
        <h3 class="text-xl font-bold text-[#0A2540] mb-2">Build Your Online Menu</h3>
        <p class="text-sm text-gray-500 mb-8">Select which categories appear on your storefront. We've auto-hidden internal categories like 'Raw Materials'.</p>

        <!-- Recursive List -->
        <ul class="space-y-4">
            <template x-for="category in categories" :key="category.id">
                <li class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            <!-- Drag Handle (Visual) -->
                            <div class="cursor-move text-gray-300 hover:text-gray-400">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                            </div>
                            
                            <div class="flex flex-col flex-1">
                                <span class="text-base font-bold text-[#0A2540]" x-text="category.name"></span>
                                <div class="mt-2" x-show="category.is_visible_online" x-transition>
                                    <input 
                                        type="text" 
                                        x-model="category.public_display_name"
                                        class="w-full max-w-xs text-sm border-gray-100 bg-gray-50 rounded-lg px-3 py-2 focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] transition-all"
                                        placeholder="Display name for customers..."
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Toggle Switch -->
                        <div class="flex items-center">
                            <button 
                                type="button" 
                                class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="category.is_visible_online ? 'bg-[#0A2540]' : 'bg-gray-200'"
                                @click="toggleCategory(category)"
                            >
                                <span 
                                    aria-hidden="true" 
                                    class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
                                    :class="category.is_visible_online ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <!-- Children -->
                    <ul x-show="category.children && category.children.length > 0 && category.is_visible_online" x-transition class="ml-10 mt-6 space-y-3 border-l-2 border-gray-50 pl-6">
                        <template x-for="child in category.children" :key="child.id">
                            <li class="flex items-center justify-between py-2 group">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-medium text-gray-600" x-text="child.name"></span>
                                    <input 
                                        type="text" 
                                        x-model="child.public_display_name"
                                        x-show="child.is_visible_online"
                                        class="ml-2 text-xs border-gray-100 bg-gray-50 rounded-lg px-3 py-1 focus:ring-2 focus:ring-[#0A2540]/10 focus:border-[#0A2540] transition-all"
                                        placeholder="Display name..."
                                    >
                                </div>
                                <button 
                                    type="button" 
                                    class="relative inline-flex h-6 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                                    :class="child.is_visible_online ? 'bg-[#0A2540]' : 'bg-gray-200'"
                                    @click="toggleCategory(child)"
                                >
                                    <span 
                                        aria-hidden="true" 
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                        :class="child.is_visible_online ? 'translate-x-4' : 'translate-x-0'"
                                    ></span>
                                </button>
                            </li>
                        </template>
                    </ul>
                </li>
            </template>
        </ul>
        
        <!-- Hidden Input to sync data with form -->
        <input type="hidden" name="catalog_curation" :value="JSON.stringify(categories)">
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('catalogCurator', (initialData) => ({
            categories: initialData,

            toggleCategory(cat) {
                cat.is_visible_online = !cat.is_visible_online;
                
                // Cascade Hide: If Parent OFF -> All Children OFF
                if (!cat.is_visible_online && cat.children) {
                    cat.children.forEach(child => {
                        child.is_visible_online = false;
                    });
                }
                
                // Cascade Show? Optional. 
                // Requirement said "unchecking parent automatically unchecks children".
            }
        }))
    })
</script>
