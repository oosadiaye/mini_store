@extends('admin.layout')

@section('content')
@section('content')
<div class="fixed inset-0 flex overflow-hidden bg-gray-100 z-[60]" x-data="pageBuilder()" x-init="init()">
    
    <!-- LEFT SIDEBAR: Controls -->
    <div class="w-96 flex flex-col bg-white border-r border-gray-200 shadow-2xl z-20 transition-all duration-300">
        
        <!-- Sidebar Header -->
        <div class="h-16 border-b flex items-center justify-between px-4 bg-gray-50">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-red-500 transition" title="Exit Page Builder">
                    <i class="fas fa-times-circle text-xl"></i>
                </a>
                <h2 class="font-bold text-gray-800">Page Builder</h2>
            </div>
            <!-- Page Selector -->
            <select id="page-selector" 
                    class="bg-white border-none text-sm font-medium text-gray-600 focus:ring-0 cursor-pointer hover:text-indigo-600 transition w-32"
                    onchange="window.location.href='{{ route('admin.page-builder.index') }}?page=' + this.value">
                <option value="home" {{ $pageName === 'home' ? 'selected' : '' }}>Home</option>
                <option value="about" {{ $pageName === 'about' ? 'selected' : '' }}>About</option>
                <option value="contact" {{ $pageName === 'contact' ? 'selected' : '' }}>Contact</option>
                <option value="shop" {{ $pageName === 'shop' ? 'selected' : '' }}>Shop</option>
            </select>
        </div>

        <!-- MAIN SIDEBAR CONTENT -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden relative">
            
            <!-- VIEW 1: NAVIGATOR & WIDGETS (Default) -->
            <div x-show="!editingSection" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform -translate-x-full" x-transition:enter-end="transform translate-x-0">
                
                <!-- Tabs -->
                <div class="flex border-b sticky top-0 bg-white z-10">
                    <button @click="sidebarTab = 'add'" :class="{'text-indigo-600 border-b-2 border-indigo-600': sidebarTab === 'add'}" class="flex-1 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                    <button @click="sidebarTab = 'layers'" :class="{'text-indigo-600 border-b-2 border-indigo-600': sidebarTab === 'layers'}" class="flex-1 py-3 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-list mr-1"></i> Layers
                    </button>
                </div>

                <!-- TAB: ADD SECTIONS -->
                <div x-show="sidebarTab === 'add'" class="p-4 space-y-3">
                    <p class="text-xs text-gray-400 font-bold uppercase mb-2">Drag to 'Layers' Tab</p>
                    <div id="available-sections" class="grid grid-cols-2 gap-2">
                        @foreach($availableSections as $section)
                        @php
                            $sectionData = [
                                'id' => uniqid($section['type'] . '-'),
                                'type' => $section['type'],
                                'enabled' => true,
                                'order' => 999,
                                'settings' => []
                            ];
                        @endphp
                        <div class="section-template cursor-grab active:cursor-grabbing p-3 rounded border border-gray-200 bg-white hover:border-indigo-500 hover:shadow-md transition text-center group" 
                             data-section="{{ json_encode($sectionData) }}"
                             @click="addSection('{{ $section['type'] }}')">
                            <div class="text-2xl mb-1 group-hover:scale-110 transition-transform">{{ $section['icon'] }}</div>
                            <div class="text-xs font-medium text-gray-700 truncate">{{ $section['name'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- TAB: LAYERS (Current Layout) -->
                <div x-show="sidebarTab === 'layers'" class="p-4">
                    <div x-show="sections.length === 0" class="text-center py-10 text-gray-400">
                        <p>Page is empty.</p>
                        <button @click="sidebarTab = 'add'" class="text-indigo-600 text-sm mt-2 hover:underline">Add Content</button>
                    </div>

                    <div id="current-layout" class="space-y-2 min-h-[300px] pb-20 border-2 border-transparent" :class="{'border-dashed border-indigo-200': sections.length === 0}">
                        <template x-for="(section, index) in sections" :key="section.id">
                            <div class="section-card bg-white border border-gray-200 rounded p-3 cursor-pointer hover:border-indigo-400 transition group flex justify-between items-center relative"
                                 :class="{'border-indigo-600 ring-1 ring-indigo-600': editingIndex === index}"
                                 @click="configureSection(index)">
                                
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-grip-vertical text-gray-300 cursor-move handle"></i>
                                    <!-- Order Number Input -->
                                    <input type="number" 
                                           :value="index" 
                                           class="w-8 h-6 text-xs border-gray-200 rounded p-0 text-center focus:ring-1 focus:ring-indigo-500"
                                           @change.stop="moveSection(index, parseInt($event.target.value))"
                                           @click.stop>
                                    
                                    <span class="text-lg" x-text="getSectionIcon(section.type)"></span>
                                    <span class="text-sm font-medium text-gray-700" x-text="getSectionName(section.type)"></span>
                                </div>

                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Visibility Toggle -->
                                    <button @click.stop="section.enabled = !section.enabled" class="p-1.5 text-gray-400 hover:text-gray-600 rounded">
                                        <i class="fas" :class="section.enabled ? 'fa-eye' : 'fa-eye-slash'"></i>
                                    </button>
                                    <!-- Delete -->
                                    <button @click.stop="removeSection(index)" class="p-1.5 text-gray-400 hover:text-red-500 rounded">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- VIEW 2: EDIT SETTINGS -->
            <div x-show="editingSection" class="absolute inset-0 bg-white" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="transform translate-x-full" x-transition:enter-end="transform translate-x-0">
                
                <!-- Edit Header -->
                <div class="h-12 border-b flex items-center px-4 gap-3 bg-gray-50">
                    <button @click="closeSettings()" class="text-gray-500 hover:text-gray-800 transition">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <span class="font-bold text-sm text-gray-800">Edit <span x-text="getSectionName(editingSection?.type)"></span></span>
                </div>

                <!-- Settings Content -->
                <div class="p-5 space-y-6 overflow-y-auto h-[calc(100vh-130px)]">
                    <!-- Dynamic Form Fields from previous modal -->
                    <template x-if="editingSection">
                        <div class="space-y-6">
                            
                            <!-- CONTENT SECTION -->
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Content</h4>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                                    <input type="text" x-model="editingSection.title" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Subtitle/Text</label>
                                    <textarea x-model="editingSection.content" rows="3" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0"></textarea>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <!-- STYLE SECTION -->
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Layout & Grid</h4>
                                
                                <!-- Container Width -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Container Width</label>
                                    <select x-model="editingSection.settings.container_width" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0">
                                        <option value="container">Standard Container (Centered)</option>
                                        <option value="full">Full Width</option>
                                    </select>
                                </div>
                                
                                <!-- Grid Columns (Responsive) -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1"><i class="fas fa-desktop"></i> Desktop Cols</label>
                                        <select x-model="editingSection.settings.grid_cols_desktop" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0">
                                            <option value="">Auto</option>
                                            <option value="1">1 Column</option>
                                            <option value="2">2 Columns</option>
                                            <option value="3">3 Columns</option>
                                            <option value="4">4 Columns</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1"><i class="fas fa-mobile-alt"></i> Mobile Cols</label>
                                        <select x-model="editingSection.settings.grid_cols_mobile" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0">
                                            <option value="1">1 Column</option>
                                            <option value="2">2 Columns</option>
                                        </select>
                                    </div>
                                </div>
                                <hr class="border-gray-100">

                                <!-- Spacing & Sizing -->
                                <div x-data="{ spacingMode: 'desktop', spacingType: 'padding' }">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Spacing</h4>
                                        <div class="flex gap-1">
                                            <div class="flex bg-gray-100 p-0.5 rounded">
                                                <button @click="spacingType='padding'" :class="{'bg-white shadow text-indigo-600': spacingType==='padding'}" class="px-2 py-0.5 text-xs rounded transition">Padding</button>
                                                <button @click="spacingType='margin'" :class="{'bg-white shadow text-indigo-600': spacingType==='margin'}" class="px-2 py-0.5 text-xs rounded transition">Margin</button>
                                            </div>
                                            <div class="flex bg-gray-100 p-0.5 rounded">
                                                <button @click="spacingMode='desktop'" :class="{'bg-white shadow text-indigo-600': spacingMode==='desktop'}" class="px-2 py-0.5 text-xs rounded transition"><i class="fas fa-desktop"></i></button>
                                                <button @click="spacingMode='mobile'" :class="{'bg-white shadow text-indigo-600': spacingMode==='mobile'}" class="px-2 py-0.5 text-xs rounded transition"><i class="fas fa-mobile-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Desktop Padding -->
                                    <div x-show="spacingMode === 'desktop' && spacingType === 'padding'" class="space-y-2">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Top</label>
                                                <input type="range" x-model="editingSection.settings.padding_top" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_top||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Bottom</label>
                                                <input type="range" x-model="editingSection.settings.padding_bottom" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_bottom||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Left</label>
                                                <input type="range" x-model="editingSection.settings.padding_left" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_left||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Right</label>
                                                <input type="range" x-model="editingSection.settings.padding_right" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_right||0)+'px'"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile Padding -->
                                    <div x-show="spacingMode === 'mobile' && spacingType === 'padding'" class="space-y-2">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Top</label>
                                                <input type="range" x-model="editingSection.settings.padding_top_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_top_mobile||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Bottom</label>
                                                <input type="range" x-model="editingSection.settings.padding_bottom_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_bottom_mobile||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Left</label>
                                                <input type="range" x-model="editingSection.settings.padding_left_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_left_mobile||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Right</label>
                                                <input type="range" x-model="editingSection.settings.padding_right_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.padding_right_mobile||0)+'px'"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Desktop Margin -->
                                    <div x-show="spacingMode === 'desktop' && spacingType === 'margin'" class="space-y-2">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Top</label>
                                                <input type="range" x-model="editingSection.settings.margin_top" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_top||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Bottom</label>
                                                <input type="range" x-model="editingSection.settings.margin_bottom" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_bottom||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Left</label>
                                                <input type="range" x-model="editingSection.settings.margin_left" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_left||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Right</label>
                                                <input type="range" x-model="editingSection.settings.margin_right" min="0" max="250" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_right||0)+'px'"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile Margin -->
                                    <div x-show="spacingMode === 'mobile' && spacingType === 'margin'" class="space-y-2">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Top</label>
                                                <input type="range" x-model="editingSection.settings.margin_top_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_top_mobile||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Bottom</label>
                                                <input type="range" x-model="editingSection.settings.margin_bottom_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_bottom_mobile||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Left</label>
                                                <input type="range" x-model="editingSection.settings.margin_left_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_left_mobile||0)+'px'"></div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Right</label>
                                                <input type="range" x-model="editingSection.settings.margin_right_mobile" min="0" max="150" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                                <div class="text-right text-xs text-gray-500" x-text="(editingSection.settings.margin_right_mobile||0)+'px'"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="border-gray-100">
                                
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Background & Layout</h4>
                                
                                <!-- Background Color -->
                            <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Background Color</label>
                                    <div class="flex gap-2">
                                        <input type="color" x-model="editingSection.settings.background_color" class="h-8 w-10 border rounded cursor-pointer">
                                        <input type="text" x-model="editingSection.settings.background_color" class="flex-1 text-sm border-gray-300 rounded">
                                    </div>
                                </div>

                                <!-- Min Height -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Min Height: <span x-text="(editingSection.settings.min_height||450)+'px'"></span></label>
                                    <input type="range" x-model="editingSection.settings.min_height" min="200" max="1000" step="10" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                </div>

                                <!-- Background Image Upload -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Background Image</label>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="editingSection.settings.background_image" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0" placeholder="https://...">
                                        <label class="cursor-pointer bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded px-2 flex items-center justify-center">
                                            <i class="fas fa-upload text-gray-500"></i>
                                            <input type="file" @change="uploadImage($event, 'background_image')" class="hidden" accept="image/*">
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Foreground Image Upload -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Foreground Image (Optional)</label>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="editingSection.settings.foreground_image" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0" placeholder="https://...">
                                        <label class="cursor-pointer bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded px-2 flex items-center justify-center">
                                            <i class="fas fa-upload text-gray-500"></i>
                                            <input type="file" @change="uploadImage($event, 'foreground_image')" class="hidden" accept="image/*">
                                        </label>
                                    </div>
                                </div>

                                <!-- Overlay -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Overlay Color</label>
                                    <div class="flex gap-2 mb-2">
                                        <input type="color" x-model="editingSection.settings.overlay_color" class="h-8 w-10 border rounded cursor-pointer">
                                        <input type="text" x-model="editingSection.settings.overlay_color" class="flex-1 text-sm border-gray-300 rounded">
                                    </div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Opacity: <span x-text="(editingSection.settings.overlay_opacity||50)+'%'"></span></label>
                                    <input type="range" x-model="editingSection.settings.overlay_opacity" min="0" max="100" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                </div>
                            </div>
                            
                            <hr class="border-gray-100">
                            
                            <!-- TYPOGRAPHY SECTION -->
                            <div class="space-y-4">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Typography & Text</h4>
                                
                                <!-- Text Alignment (Responsive) -->
                                <div x-data="{ alignMode: 'desktop' }">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-xs font-medium text-gray-700">Text Alignment</label>
                                        <div class="flex bg-gray-100 p-0.5 rounded">
                                            <button @click="alignMode='desktop'" :class="{'bg-white shadow text-indigo-600': alignMode==='desktop'}" class="px-2 py-0.5 text-xs rounded transition"><i class="fas fa-desktop"></i></button>
                                            <button @click="alignMode='mobile'" :class="{'bg-white shadow text-indigo-600': alignMode==='mobile'}" class="px-2 py-0.5 text-xs rounded transition"><i class="fas fa-mobile-alt"></i></button>
                                        </div>
                                    </div>
                                    
                                    <!-- Desktop Alignment -->
                                    <div x-show="alignMode === 'desktop'" class="flex gap-1">
                                        <button @click="editingSection.settings.text_align = 'left'" :class="{'bg-indigo-600 text-white': editingSection.settings.text_align === 'left', 'bg-gray-100 text-gray-600': editingSection.settings.text_align !== 'left'}" class="flex-1 py-2 rounded text-xs font-medium transition"><i class="fas fa-align-left"></i></button>
                                        <button @click="editingSection.settings.text_align = 'center'" :class="{'bg-indigo-600 text-white': editingSection.settings.text_align === 'center', 'bg-gray-100 text-gray-600': editingSection.settings.text_align !== 'center'}" class="flex-1 py-2 rounded text-xs font-medium transition"><i class="fas fa-align-center"></i></button>
                                        <button @click="editingSection.settings.text_align = 'right'" :class="{'bg-indigo-600 text-white': editingSection.settings.text_align === 'right', 'bg-gray-100 text-gray-600': editingSection.settings.text_align !== 'right'}" class="flex-1 py-2 rounded text-xs font-medium transition"><i class="fas fa-align-right"></i></button>
                                    </div>
                                    
                                    <!-- Mobile Alignment -->
                                    <div x-show="alignMode === 'mobile'" class="flex gap-1">
                                        <button @click="editingSection.settings.text_align_mobile = 'left'" :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_mobile === 'left', 'bg-gray-100 text-gray-600': editingSection.settings.text_align_mobile !== 'left'}" class="flex-1 py-2 rounded text-xs font-medium transition"><i class="fas fa-align-left"></i></button>
                                        <button @click="editingSection.settings.text_align_mobile = 'center'" :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_mobile === 'center', 'bg-gray-100 text-gray-600': editingSection.settings.text_align_mobile !== 'center'}" class="flex-1 py-2 rounded text-xs font-medium transition"><i class="fas fa-align-center"></i></button>
                                        <button @click="editingSection.settings.text_align_mobile = 'right'" :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_mobile === 'right', 'bg-gray-100 text-gray-600': editingSection.settings.text_align_mobile !== 'right'}" class="flex-1 py-2 rounded text-xs font-medium transition"><i class="fas fa-align-right"></i></button>
                                    </div>
                                </div>
                                
                                <!-- Font Sizes (Responsive) -->
                                <div x-data="{ fontMode: 'desktop' }">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-xs font-medium text-gray-700">Font Size</label>
                                        <div class="flex bg-gray-100 p-0.5 rounded">
                                            <button @click="fontMode='desktop'" :class="{'bg-white shadow text-indigo-600': fontMode==='desktop'}" class="px-2 py-0.5 text-xs rounded transition"><i class="fas fa-desktop"></i></button>
                                            <button @click="fontMode='mobile'" :class="{'bg-white shadow text-indigo-600': fontMode==='mobile'}" class="px-2 py-0.5 text-xs rounded transition"><i class="fas fa-mobile-alt"></i></button>
                                        </div>
                                    </div>
                                    
                                    <!-- Desktop Font Sizes -->
                                    <div x-show="fontMode === 'desktop'" class="space-y-2">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Title: <span x-text="(editingSection.settings.title_font_size||'Default')"></span></label>
                                            <input type="range" x-model="editingSection.settings.title_font_size" min="12" max="80" step="2" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Content: <span x-text="(editingSection.settings.content_font_size||'Default')"></span></label>
                                            <input type="range" x-model="editingSection.settings.content_font_size" min="10" max="32" step="1" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                        </div>
                                    </div>
                                    
                                    <!-- Mobile Font Sizes -->
                                    <div x-show="fontMode === 'mobile'" class="space-y-2">
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Title: <span x-text="(editingSection.settings.title_font_size_mobile||'Default')"></span></label>
                                            <input type="range" x-model="editingSection.settings.title_font_size_mobile" min="12" max="60" step="2" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-1">Content: <span x-text="(editingSection.settings.content_font_size_mobile||'Default')"></span></label>
                                            <input type="range" x-model="editingSection.settings.content_font_size_mobile" min="10" max="24" step="1" class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Font Weight -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Font Weight</label>
                                    <select x-model="editingSection.settings.font_weight" class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-0">
                                        <option value="">Default</option>
                                        <option value="300">Light (300)</option>
                                        <option value="400">Normal (400)</option>
                                        <option value="500">Medium (500)</option>
                                        <option value="600">Semi-Bold (600)</option>
                                        <option value="700">Bold (700)</option>
                                        <option value="800">Extra Bold (800)</option>
                                    </select>
                                </div>
                                
                                <!-- Text Colors -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Title Color</label>
                                        <div class="flex gap-2">
                                            <input type="color" x-model="editingSection.settings.title_color" class="h-8 w-10 border rounded cursor-pointer">
                                            <input type="text" x-model="editingSection.settings.title_color" class="flex-1 text-xs border-gray-300 rounded">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Content Color</label>
                                        <div class="flex gap-2">
                                            <input type="color" x-model="editingSection.settings.content_color" class="h-8 w-10 border rounded cursor-pointer">
                                            <input type="text" x-model="editingSection.settings.content_color" class="flex-1 text-xs border-gray-300 rounded">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Visibility Controls -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-2">Visibility</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="editingSection.settings.hide_on_desktop" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs text-gray-700"><i class="fas fa-desktop mr-1"></i> Hide on Desktop</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="editingSection.settings.hide_on_mobile" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-xs text-gray-700"><i class="fas fa-mobile-alt mr-1"></i> Hide on Mobile</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                         </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t bg-white flex justify-between items-center gap-2">
            <button @click="resetLayout()" class="text-gray-400 hover:text-red-500 p-2" title="Reset">
                <i class="fas fa-trash-restore"></i>
            </button>
            <div class="flex gap-2">
                <button @click="promptSaveTemplate()" class="bg-purple-100 text-purple-700 hover:bg-purple-200 px-4 py-2 rounded text-sm font-medium transition">
                    Template
                </button>
                <button @click="saveLayout()" class="bg-indigo-600 text-white hover:bg-indigo-700 px-6 py-2 rounded text-sm font-medium shadow-lg transform hover:-translate-y-0.5 transition">
                    Update
                </button>
            </div>
        </div>
    </div>

    <!-- RIGHT CANVAS: Preview -->
    <div class="flex-1 flex flex-col h-full bg-gray-200 relative transition-all duration-300">
        
        <!-- Toolbar -->
        <div class="h-14 bg-white border-b flex items-center justify-center shadow-sm z-10">
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button @click="device='desktop'" :class="{'bg-white shadow text-indigo-600': device === 'desktop', 'text-gray-500': device !== 'desktop'}" class="p-2 rounded transition w-10 h-8 flex items-center justify-center">
                    <i class="fas fa-desktop"></i>
                </button>
                <button @click="device='tablet'" :class="{'bg-white shadow text-indigo-600': device === 'tablet', 'text-gray-500': device !== 'tablet'}" class="p-2 rounded transition w-10 h-8 flex items-center justify-center">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button @click="device='mobile'" :class="{'bg-white shadow text-indigo-600': device === 'mobile', 'text-gray-500': device !== 'mobile'}" class="p-2 rounded transition w-10 h-8 flex items-center justify-center">
                    <i class="fas fa-mobile-alt"></i>
                </button>
            </div>
        </div>

        <!-- Iframe Container -->
        <div class="flex-1 overflow-auto flex justify-center p-8 relative" id="canvas-area">
             <div :class="{
                    'w-full h-full': device === 'desktop',
                    'w-[768px] h-[900px] my-10 shadow-2xl': device === 'tablet',
                    'w-[375px] h-[700px] my-10 shadow-2xl': device === 'mobile'
                 }" 
                 class="bg-white transition-all duration-300 relative">
                
                <iframe id="preview-frame" 
                        src="{{ route('storefront.home') }}?editor_mode=true&page={{ $pageName }}" 
                        class="w-full h-full border-none"
                        scrolling="yes">
                </iframe>
                
                <!-- Overlay for click interception (optional, if we use postMessage) -->
             </div>
        </div>
    </div>

</div>

<!-- SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
function pageBuilder() {
    return {
        sections: @json($layout->sections ?? []),
        availableSections: @json($availableSections),
        sidebarTab: 'layers', // 'add' or 'layers'
        editingIndex: null,
        editingSection: null, // Holds the live reference or clone
        device: 'desktop',

        init() {
            // Sortable: Current Layout
            const layoutEl = document.getElementById('current-layout');
            Sortable.create(layoutEl, {
                animation: 150,
                handle: '.section-card', // can drag whole card or handle
                onEnd: (evt) => {
                    const item = this.sections.splice(evt.oldIndex, 1)[0];
                    this.sections.splice(evt.newIndex, 0, item);
                    this.updateOrder();
                }
            });

            // Sortable: Available (Drag from Sidebar)
            // Note: Dragging from sidebar to sidebar list is easy.
            // Dragging to iframe is hard. We stick to list-to-list.
            const availableEl = document.getElementById('available-sections');
            Sortable.create(availableEl, {
                group: {
                    name: 'sections',
                    pull: 'clone',
                    put: false
                },
                sort: false,
                onEnd: (evt) => {
                     if (evt.to.id === 'current-layout') {
                        const sectionJson = evt.item.dataset.section;
                        if (sectionJson) {
                            const sectionData = JSON.parse(sectionJson);
                            const newSection = JSON.parse(JSON.stringify(sectionData));
                            newSection.id = sectionData.type + '-' + Date.now();
                            this.sections.splice(evt.newIndex, 0, newSection);
                            this.updateOrder();
                            evt.item.remove(); // Remove clone
                        }
                     }
                }
            });

            // Listen for messages from Iframe
            window.addEventListener('message', (event) => {
                if (event.data.type === 'sectionSelected') {
                    if (typeof event.data.index === 'number') {
                        this.configureSection(event.data.index);
                    }
                }
            });
            
            // Auto-refresh iframe on change (Debounced)?
            // Better: 'Update' button sends data.
            // Watch for changes (with debounce)
            this.$watch('sections', (value) => {
                if(this.previewTimeout) clearTimeout(this.previewTimeout);
                this.previewTimeout = setTimeout(() => {
                    this.updatePreview();
                }, 500); // 500ms debounce
            });
        },

        previewTimeout: null,

        async updatePreview() {
            // Send sections to backend to render HTML
             try {
                const response = await fetch('{{ route("admin.page-builder.render") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ sections: this.sections })
                });
                const data = await response.json();
                if(data.html) {
                    const iframe = document.getElementById('preview-frame');
                    if(iframe && iframe.contentWindow) {
                        iframe.contentWindow.postMessage({
                            type: 'updateContent',
                            html: data.html
                        }, '*');
                    }
                }
            } catch(e) { console.error('Preview error', e); }
        },

        updateOrder() {
            this.sections.forEach((s, i) => s.order = i + 1);
        },

        configureSection(index) {
            this.editingIndex = index;
            // Direct reference allows live updates in the list, but not necessarily iframe until save
            this.editingSection = this.sections[index]; 
        },

        closeSettings() {
            this.editingIndex = null;
            this.editingSection = null;
        },

        removeSection(index) {
            if(confirm('Remove this section?')) {
                this.sections.splice(index, 1);
                this.closeSettings();
                this.updateOrder();
                this.updatePreview();
            }
        },

        addSection(type) {
            // Create new section object
            const newSection = {
                id: type + '-' + Date.now(),
                type: type,
                enabled: true,
                order: 999,
                settings: {}
            };
            this.sections.push(newSection);
            this.sidebarTab = 'layers';
            this.updateOrder();
            // Scroll to bottom of layers
            setTimeout(() => {
                const el = document.getElementById('current-layout');
                el.scrollTop = el.scrollHeight;
            }, 50);
        },

        moveSection(fromIndex, toIndex) {
            // Validate toIndex
            if (isNaN(toIndex)) return;
            // Clamp
            toIndex = Math.max(0, Math.min(toIndex, this.sections.length - 1));
            
            if (fromIndex === toIndex) return;

            // Move
            const item = this.sections.splice(fromIndex, 1)[0];
            this.sections.splice(toIndex, 0, item);
            this.updateOrder();
        },

        async uploadImage(event, field) {
            const file = event.target.files[0];
            if (!file) return;
            const formData = new FormData();
            formData.append('image', file);
            
            try {
                const res = await fetch('{{ route("admin.page-builder.upload") }}', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: formData
                });
                const data = await res.json();
                if(data.success) {
                    // If we bound editingSection to the live array item
                    this.editingSection.settings[field] = data.url;
                }
            } catch(e) { alert('Upload failed'); }
        },

        async saveLayout() {
            // Save logic
             try {
                const response = await fetch('{{ route("admin.page-builder.update") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ page_name: '{{ $pageName }}', sections: this.sections })
                });
                if(response.ok) {
                    // Reload iframe to show changes
                    document.getElementById('preview-frame').contentWindow.location.reload();
                    alert('Saved!');
                }
            } catch(e) {
                alert('Error saving');
            }
        },

        async promptSaveTemplate() {
             const name = prompt("Template Name:");
             if(!name) return;
             // ... existing logic ...
        },

        async resetLayout() {
            if(!confirm('Reset?')) return;
             // ... existing logic ...
             // Reload page after
        },
        
        getSectionIcon(type) {
             const icons = {
                 'hero': '🎨', 'text': '📝', 'products': '🛍️', 
                 // ... others
             };
             return icons[type] || '📦';
        },
        getSectionName(type) {
            return type.replace('_', ' ').toUpperCase();
        }
    }
}
</script>
@endsection
