{{-- Hero Banner Section Editor --}}
<div class="space-y-6">
    
    {{-- Content Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Content</h4>
        
        {{-- Title --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Title</label>
            <input type="text" 
                   x-model="editingSection.title"
                   @input="updatePreview()"
                   placeholder="Welcome to Our Store"
                   class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
        
        {{-- Subtitle --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Subtitle</label>
            <textarea x-model="editingSection.content"
                      @input="updatePreview()"
                      rows="2"
                      placeholder="Discover amazing products"
                      class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
        </div>
        
        {{-- Button Text --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Button Text</label>
                <input type="text" 
                       x-model="editingSection.settings.button_text"
                       @input="updatePreview()"
                       placeholder="Shop Now"
                       class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Button URL</label>
                <input type="text" 
                       x-model="editingSection.settings.button_url"
                       @input="updatePreview()"
                       placeholder="/products"
                       class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Dimensions Section --}}
    <div x-data="{ deviceMode: 'desktop' }">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Dimensions</h4>
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button @click="deviceMode='desktop'" 
                        :class="{'bg-white shadow-sm text-indigo-600': deviceMode==='desktop', 'text-gray-500': deviceMode!=='desktop'}"
                        class="px-3 py-1 rounded text-xs font-medium transition">
                    <i class="fas fa-desktop mr-1"></i> Desktop
                </button>
                <button @click="deviceMode='mobile'" 
                        :class="{'bg-white shadow-sm text-indigo-600': deviceMode==='mobile', 'text-gray-500': deviceMode!=='mobile'}"
                        class="px-3 py-1 rounded text-xs font-medium transition">
                    <i class="fas fa-mobile-alt mr-1"></i> Mobile
                </button>
            </div>
        </div>
        
        {{-- Desktop Height --}}
        <div x-show="deviceMode === 'desktop'" class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Min Height <span class="text-gray-400" x-text="(editingSection.settings.min_height_desktop || 600) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.min_height_desktop"
                   min="200" max="2000" step="10"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Mobile Height --}}
        <div x-show="deviceMode === 'mobile'" class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Min Height <span class="text-gray-400" x-text="(editingSection.settings.min_height_mobile || 400) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.min_height_mobile"
                   min="200" max="1000" step="10"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Typography Section --}}
    <div x-data="{ deviceMode: 'desktop' }">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Typography</h4>
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button @click="deviceMode='desktop'" 
                        :class="{'bg-white shadow-sm text-indigo-600': deviceMode==='desktop', 'text-gray-500': deviceMode!=='desktop'}"
                        class="px-3 py-1 rounded text-xs font-medium transition">
                    <i class="fas fa-desktop mr-1"></i> Desktop
                </button>
                <button @click="deviceMode='mobile'" 
                        :class="{'bg-white shadow-sm text-indigo-600': deviceMode==='mobile', 'text-gray-500': deviceMode!=='mobile'}"
                        class="px-3 py-1 rounded text-xs font-medium transition">
                    <i class="fas fa-mobile-alt mr-1"></i> Mobile
                </button>
            </div>
        </div>
        
        {{-- Desktop Typography --}}
        <div x-show="deviceMode === 'desktop'" class="space-y-4">
            {{-- Title Font Size --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Title Font Size <span class="text-gray-400" x-text="(editingSection.settings.title_font_size_desktop || 64) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.title_font_size_desktop"
                       min="20" max="120" step="2"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            
            {{-- Subtitle Font Size --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Subtitle Font Size <span class="text-gray-400" x-text="(editingSection.settings.subtitle_font_size_desktop || 24) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.subtitle_font_size_desktop"
                       min="12" max="48" step="1"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            
            {{-- Text Alignment --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Text Alignment</label>
                <div class="flex gap-1">
                    <button type="button"
                            @click="editingSection.settings.title_text_align_desktop = 'left'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.title_text_align_desktop === 'left', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.title_text_align_desktop !== 'left'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.title_text_align_desktop = 'center'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.title_text_align_desktop === 'center', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.title_text_align_desktop !== 'center'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-center"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.title_text_align_desktop = 'right'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.title_text_align_desktop === 'right', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.title_text_align_desktop !== 'right'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-right"></i>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Mobile Typography --}}
        <div x-show="deviceMode === 'mobile'" class="space-y-4">
            {{-- Title Font Size --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Title Font Size <span class="text-gray-400" x-text="(editingSection.settings.title_font_size_mobile || 36) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.title_font_size_mobile"
                       min="16" max="80" step="2"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            
            {{-- Subtitle Font Size --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Subtitle Font Size <span class="text-gray-400" x-text="(editingSection.settings.subtitle_font_size_mobile || 18) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.subtitle_font_size_mobile"
                       min="12" max="32" step="1"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            
            {{-- Text Alignment --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Text Alignment</label>
                <div class="flex gap-1">
                    <button type="button"
                            @click="editingSection.settings.title_text_align_mobile = 'left'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.title_text_align_mobile === 'left', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.title_text_align_mobile !== 'left'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.title_text_align_mobile = 'center'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.title_text_align_mobile === 'center', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.title_text_align_mobile !== 'center'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-center"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.title_text_align_mobile = 'right'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.title_text_align_mobile === 'right', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.title_text_align_mobile !== 'right'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-right"></i>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Font Weight (applies to both) --}}
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Font Weight</label>
            <select x-model.number="editingSection.settings.title_font_weight"
                    @change="updatePreview()"
                    class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="300">Light (300)</option>
                <option value="400">Normal (400)</option>
                <option value="500">Medium (500)</option>
                <option value="600">Semi-Bold (600)</option>
                <option value="700">Bold (700)</option>
                <option value="800">Extra Bold (800)</option>
            </select>
        </div>
        
        {{-- Title Color --}}
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Title Color</label>
            <div class="flex gap-2">
                <input type="color" 
                       x-model="editingSection.settings.title_color"
                       @input="updatePreview()"
                       class="h-10 w-12 rounded border border-gray-300 cursor-pointer">
                <input type="text" 
                       x-model="editingSection.settings.title_color"
                       @input="updatePreview()"
                       placeholder="#ffffff"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>
        
        {{-- Subtitle Color --}}
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Subtitle Color</label>
            <div class="flex gap-2">
                <input type="color" 
                       x-model="editingSection.settings.subtitle_color"
                       @input="updatePreview()"
                       class="h-10 w-12 rounded border border-gray-300 cursor-pointer">
                <input type="text" 
                       x-model="editingSection.settings.subtitle_color"
                       @input="updatePreview()"
                       placeholder="#f0f0f0"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Background Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Background</h4>
        
        {{-- Background Color --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Background Color</label>
            <div class="flex gap-2">
                <input type="color" 
                       x-model="editingSection.settings.background_color"
                       @input="updatePreview()"
                       class="h-10 w-12 rounded border border-gray-300 cursor-pointer">
                <input type="text" 
                       x-model="editingSection.settings.background_color"
                       @input="updatePreview()"
                       placeholder="#000000"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>
        
        {{-- Background Image --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Background Image</label>
            
            {{-- Preview --}}
            <div x-show="editingSection.settings.background_image" class="mb-2">
                <img :src="editingSection.settings.background_image" 
                     alt="Background Preview" 
                     class="w-full h-32 object-cover rounded border border-gray-200">
            </div>
            
            {{-- Upload Controls --}}
            <div class="flex gap-2">
                <input type="text" 
                       x-model="editingSection.settings.background_image"
                       @input="updatePreview()"
                       placeholder="https://... or upload"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <label class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium transition flex items-center">
                    <i class="fas fa-upload mr-2"></i> Upload
                    <input type="file" 
                           @change="uploadImage($event, 'background_image')" 
                           class="hidden" 
                           accept="image/*">
                </label>
            </div>
            
            {{-- Loading Indicator --}}
            <div x-show="uploading" class="mt-2 text-xs text-gray-500 flex items-center">
                <svg class="animate-spin h-4 w-4 mr-2 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Uploading...
            </div>
        </div>
        
        {{-- Background Position --}}
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Position X <span class="text-gray-400" x-text="(editingSection.settings.background_position_x || 50) + '%'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.background_position_x"
                       min="0" max="100" step="5"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Position Y <span class="text-gray-400" x-text="(editingSection.settings.background_position_y || 50) + '%'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.background_position_y"
                       min="0" max="100" step="5"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
        </div>
        
        {{-- Overlay --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Overlay Color</label>
            <div class="flex gap-2 mb-2">
                <input type="color" 
                       x-model="editingSection.settings.overlay_color"
                       @input="updatePreview()"
                       class="h-10 w-12 rounded border border-gray-300 cursor-pointer">
                <input type="text" 
                       x-model="editingSection.settings.overlay_color"
                       @input="updatePreview()"
                       placeholder="#000000"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Opacity <span class="text-gray-400" x-text="(editingSection.settings.overlay_opacity || 40) + '%'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.overlay_opacity"
                   min="0" max="100" step="5"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Spacing Section --}}
    <div x-data="{ deviceMode: 'desktop' }">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Spacing (Padding)</h4>
            <div class="flex bg-gray-100 p-1 rounded-lg">
                <button @click="deviceMode='desktop'" 
                        :class="{'bg-white shadow-sm text-indigo-600': deviceMode==='desktop', 'text-gray-500': deviceMode!=='desktop'}"
                        class="px-3 py-1 rounded text-xs font-medium transition">
                    <i class="fas fa-desktop mr-1"></i> Desktop
                </button>
                <button @click="deviceMode='mobile'" 
                        :class="{'bg-white shadow-sm text-indigo-600': deviceMode==='mobile', 'text-gray-500': deviceMode!=='mobile'}"
                        class="px-3 py-1 rounded text-xs font-medium transition">
                    <i class="fas fa-mobile-alt mr-1"></i> Mobile
                </button>
            </div>
        </div>
        
        {{-- Desktop Padding --}}
        <div x-show="deviceMode === 'desktop'" class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Top <span x-text="(editingSection.settings.padding_top_desktop || 120) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_top_desktop"
                       min="0" max="500" step="10"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Bottom <span x-text="(editingSection.settings.padding_bottom_desktop || 120) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_bottom_desktop"
                       min="0" max="500" step="10"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Left <span x-text="(editingSection.settings.padding_left_desktop || 40) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_left_desktop"
                       min="0" max="500" step="10"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Right <span x-text="(editingSection.settings.padding_right_desktop || 40) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_right_desktop"
                       min="0" max="500" step="10"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
        </div>
        
        {{-- Mobile Padding --}}
        <div x-show="deviceMode === 'mobile'" class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Top <span x-text="(editingSection.settings.padding_top_mobile || 60) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_top_mobile"
                       min="0" max="300" step="10"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Bottom <span x-text="(editingSection.settings.padding_bottom_mobile || 60) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_bottom_mobile"
                       min="0" max="300" step="10"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Left <span x-text="(editingSection.settings.padding_left_mobile || 20) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_left_mobile"
                       min="0" max="100" step="5"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">
                    Right <span x-text="(editingSection.settings.padding_right_mobile || 20) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.padding_right_mobile"
                       min="0" max="100" step="5"
                       @input="updatePreview()"
                       class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Visibility Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Visibility</h4>
        <div class="space-y-2">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       x-model="editingSection.settings.hide_on_desktop"
                       @change="updatePreview()"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                <span class="text-sm text-gray-700"><i class="fas fa-desktop mr-1"></i> Hide on Desktop</span>
            </label>
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       x-model="editingSection.settings.hide_on_mobile"
                       @change="updatePreview()"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                <span class="text-sm text-gray-700"><i class="fas fa-mobile-alt mr-1"></i> Hide on Mobile</span>
            </label>
        </div>
    </div>
    
</div>
