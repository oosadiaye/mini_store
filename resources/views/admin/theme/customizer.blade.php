@extends('admin.layout')

@push('styles')
<style>
    .tab-active {
        @apply border-indigo-500 text-indigo-600;
    }
    .tab-inactive {
        @apply border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300;
    }
</style>
@endpush

@section('content')
<div x-data="advancedPageBuilder()" x-init="init()" class="pb-20">
    
    {{-- Header --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Theme Customizer & Page Builder</h2>
            <p class="text-gray-600">Customize your store's appearance and page layouts</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('storefront.home') }}" target="_blank" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition border border-gray-300 flex items-center">
                <i class="fas fa-external-link-alt mr-2"></i> Preview
            </a>
            <button @click="saveLayout()" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition shadow-md">
                <i class="fas fa-save mr-2"></i> Save Changes
            </button>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
        <p><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
    </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="currentTab = 'style'" 
                    :class="currentTab === 'style' ? 'tab-active' : 'tab-inactive'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-palette mr-2"></i> Style
            </button>
            <button @click="currentTab = 'home'; loadPage('home')" 
                    :class="currentTab === 'home' ? 'tab-active' : 'tab-inactive'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-home mr-2"></i> Home Page
            </button>
            <button @click="currentTab = 'shop'; loadPage('shop')" 
                    :class="currentTab === 'shop' ? 'tab-active' : 'tab-inactive'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-shopping-bag mr-2"></i> Shop Page
            </button>
            <button @click="currentTab = 'about'; loadPage('about')" 
                    :class="currentTab === 'about' ? 'tab-active' : 'tab-inactive'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-info-circle mr-2"></i> About Page
            </button>
            <button @click="currentTab = 'contact'; loadPage('contact')" 
                    :class="currentTab === 'contact' ? 'tab-active' : 'tab-inactive'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                <i class="fas fa-envelope mr-2"></i> Contact Page
            </button>
        </nav>
    </div>

    {{-- Style Tab Content --}}
    <div x-show="currentTab === 'style'" x-transition>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-swatchbook mr-2 text-indigo-600"></i> Global Theme Settings
            </h3>
            <p class="text-gray-600 mb-6">Configure colors, fonts, and global styling that applies across all pages.</p>
            
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-palette text-6xl mb-4 opacity-20"></i>
                <p>Global style settings will be integrated here.</p>
                <p class="text-sm mt-2">This includes colors, fonts, button styles, etc.</p>
            </div>
        </div>
    </div>

    {{-- Page Builder Tabs Content --}}
    <div x-show="['home', 'shop', 'about', 'contact'].includes(currentTab)" x-transition>
        <div class="grid grid-cols-12 gap-6">
            
            {{-- Left Sidebar - Controls --}}
            <div class="col-span-3 space-y-4">
                
                {{-- Sidebar Tabs --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex border-b border-gray-200">
                        <button @click="sidebarTab = 'add'" 
                                :class="sidebarTab === 'add' ? 'bg-indigo-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50'"
                                class="flex-1 py-3 px-4 text-sm font-medium transition">
                            <i class="fas fa-plus-circle mr-2"></i> Add
                        </button>
                        <button @click="sidebarTab = 'layers'" 
                                :class="sidebarTab === 'layers' ? 'bg-indigo-50 text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:bg-gray-50'"
                                class="flex-1 py-3 px-4 text-sm font-medium transition">
                            <i class="fas fa-layer-group mr-2"></i> Layers
                        </button>
                    </div>
                    
                    {{-- Add Section Panel --}}
                    <div x-show="sidebarTab === 'add'" class="p-4 max-h-[600px] overflow-y-auto">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Available Sections</h4>
                        <div class="space-y-2">
                            <template x-for="section in availableSections" :key="section.type">
                                <button @click="addSection(section.type)" 
                                        class="w-full text-left p-3 rounded-lg border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50 transition group">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3" x-text="section.icon"></span>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-800 group-hover:text-indigo-600" x-text="section.name"></div>
                                            <div class="text-xs text-gray-500" x-text="section.description"></div>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                    
                    {{-- Layers Panel --}}
                    <div x-show="sidebarTab === 'layers'" class="p-4 max-h-[600px] overflow-y-auto">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Current Layout</h4>
                        <div id="current-layout" class="space-y-2">
                            <template x-for="(section, index) in sections" :key="section.id">
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-3 hover:border-indigo-500 transition cursor-move handle">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <i class="fas fa-grip-vertical text-gray-400 mr-2"></i>
                                            <span class="text-sm font-medium text-gray-800" x-text="getSectionName(section.type)"></span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button @click="section.enabled = !section.enabled; updatePreview()" 
                                                    :class="section.enabled ? 'text-green-600' : 'text-gray-400'"
                                                    class="hover:text-green-700">
                                                <i :class="section.enabled ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
                                            </button>
                                            <button @click="configureSection(index)" 
                                                    class="text-indigo-600 hover:text-indigo-700">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <button @click="removeSection(index)" 
                                                    class="text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="sections.length === 0" class="text-center py-8 text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-2 opacity-20"></i>
                                <p class="text-sm">No sections yet. Add one from the "Add" tab.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Section Settings Panel --}}
                <div x-show="editingSection" class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                        <h4 class="font-semibold text-gray-800">
                            <i class="fas fa-cog mr-2 text-indigo-600"></i> Section Settings
                        </h4>
                        <button @click="closeSettings()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4 max-h-[500px] overflow-y-auto">
                        {{-- Dynamic Section Editor --}}
                        <template x-if="editingSection && editingSection.type === 'hero'">
                            <div>
                                @include('admin.page-builder.sections.hero')
                            </div>
                        </template>
                        <template x-if="editingSection && (editingSection.type === 'products' || editingSection.type === 'featured_products')">
                            <div>
                                @include('admin.page-builder.sections.products')
                            </div>
                        </template>
                        <template x-if="editingSection && (editingSection.type === 'text' || editingSection.type === 'content_block')">
                            <div>
                                @include('admin.page-builder.sections.text')
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            
            {{-- Right Side - Preview --}}
            <div class="col-span-9">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Device Toggle --}}
                    <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                        <h4 class="font-semibold text-gray-800">
                            <i class="fas fa-eye mr-2 text-indigo-600"></i> Live Preview
                        </h4>
                        <div class="flex bg-white border border-gray-300 rounded-lg overflow-hidden">
                            <button @click="device = 'desktop'; updatePreview()" 
                                    :class="device === 'desktop' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                    class="px-4 py-2 text-sm font-medium transition">
                                <i class="fas fa-desktop mr-2"></i> Desktop
                            </button>
                            <button @click="device = 'tablet'; updatePreview()" 
                                    :class="device === 'tablet' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                    class="px-4 py-2 text-sm font-medium transition border-l border-gray-300">
                                <i class="fas fa-tablet-alt mr-2"></i> Tablet
                            </button>
                            <button @click="device = 'mobile'; updatePreview()" 
                                    :class="device === 'mobile' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'"
                                    class="px-4 py-2 text-sm font-medium transition border-l border-gray-300">
                                <i class="fas fa-mobile-alt mr-2"></i> Mobile
                            </button>
                        </div>
                    </div>
                    
                    {{-- Preview Iframe --}}
                    <div class="bg-gray-100 p-4" style="min-height: 800px;">
                        <div class="mx-auto transition-all duration-300"
                             :style="device === 'desktop' ? 'max-width: 100%' : (device === 'tablet' ? 'max-width: 768px' : 'max-width: 375px')">
                            <iframe id="preview-frame" 
                                    src="{{ route('storefront.home') }}"
                                    class="w-full bg-white shadow-lg rounded-lg"
                                    style="height: 800px; border: none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Data --}}
<script>
    window.initialSections = @json($layout->sections ?? []);
    window.availableSections = @json($availableSections ?? []);
    window.pageName = '{{ $pageName ?? 'home' }}';
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="/js/page-builder.js"></script>
<script>
    // Extend Alpine component with tab management
    document.addEventListener('alpine:init', () => {
        Alpine.data('advancedPageBuilder', () => ({
            ...window.advancedPageBuilder(),
            currentTab: 'home',
            sidebarTab: 'add',
            
            loadPage(pageName) {
                window.location.href = `/admin/theme-customizer?page=${pageName}`;
            },
            
            // Override save method for theme customizer
            async saveLayout() {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    const response = await fetch('/admin/theme-customizer/save', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            page_name: window.pageName || 'home',
                            sections: this.sections
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        alert('✅ Saved successfully!');
                        // Reload iframe to show saved changes
                        document.getElementById('preview-frame')?.contentWindow?.location.reload();
                    } else {
                        alert('❌ Save failed. Please try again.');
                    }
                } catch (error) {
                    console.error('Save error:', error);
                    alert('❌ Save failed. Please try again.');
                }
            }
        }));
    });
</script>
@endpush
