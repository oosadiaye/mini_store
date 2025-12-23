{{-- Text/Content Block Section Editor --}}
<div class="space-y-6">
    
    {{-- Content Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Content</h4>
        
        {{-- Rich Text Editor --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Content</label>
            <textarea x-model="editingSection.content"
                      @input="updatePreview()"
                      rows="8"
                      placeholder="Enter your content here..."
                      class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 font-mono"></textarea>
            <p class="text-xs text-gray-500 mt-1">
                <i class="fas fa-info-circle mr-1"></i> HTML is supported
            </p>
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
            {{-- Font Size --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Font Size <span class="text-gray-400" x-text="(editingSection.settings.font_size_desktop || 16) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.font_size_desktop"
                       min="12" max="32" step="1"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            
            {{-- Text Alignment --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Text Alignment</label>
                <div class="flex gap-1">
                    <button type="button"
                            @click="editingSection.settings.text_align_desktop = 'left'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_desktop === 'left', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_desktop !== 'left'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.text_align_desktop = 'center'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_desktop === 'center', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_desktop !== 'center'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-center"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.text_align_desktop = 'right'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_desktop === 'right', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_desktop !== 'right'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-right"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.text_align_desktop = 'justify'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_desktop === 'justify', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_desktop !== 'justify'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-justify"></i>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Mobile Typography --}}
        <div x-show="deviceMode === 'mobile'" class="space-y-4">
            {{-- Font Size --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">
                    Font Size <span class="text-gray-400" x-text="(editingSection.settings.font_size_mobile || 14) + 'px'"></span>
                </label>
                <input type="range" 
                       x-model.number="editingSection.settings.font_size_mobile"
                       min="12" max="24" step="1"
                       @input="updatePreview()"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>
            
            {{-- Text Alignment --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-2">Text Alignment</label>
                <div class="flex gap-1">
                    <button type="button"
                            @click="editingSection.settings.text_align_mobile = 'left'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_mobile === 'left', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_mobile !== 'left'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.text_align_mobile = 'center'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_mobile === 'center', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_mobile !== 'center'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-center"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.text_align_mobile = 'right'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_mobile === 'right', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_mobile !== 'right'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-right"></i>
                    </button>
                    <button type="button"
                            @click="editingSection.settings.text_align_mobile = 'justify'; updatePreview()"
                            :class="{'bg-indigo-600 text-white': editingSection.settings.text_align_mobile === 'justify', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.text_align_mobile !== 'justify'}"
                            class="flex-1 py-2 rounded text-sm font-medium transition">
                        <i class="fas fa-align-justify"></i>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Line Height --}}
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Line Height <span class="text-gray-400" x-text="(editingSection.settings.line_height || 1.6)"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.line_height"
                   min="1.0" max="3.0" step="0.1"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Text Color --}}
        <div class="mt-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Text Color</label>
            <div class="flex gap-2">
                <input type="color" 
                       x-model="editingSection.settings.text_color"
                       @input="updatePreview()"
                       class="h-10 w-12 rounded border border-gray-300 cursor-pointer">
                <input type="text" 
                       x-model="editingSection.settings.text_color"
                       @input="updatePreview()"
                       placeholder="#333333"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Dimensions Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Dimensions</h4>
        
        {{-- Max Width --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Max Width <span class="text-gray-400" x-text="(editingSection.settings.max_width || 800) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.max_width"
                   min="400" max="1400" step="50"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Spacing Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Spacing</h4>
        
        {{-- Padding Top --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Padding Top <span class="text-gray-400" x-text="(editingSection.settings.padding_top || 60) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.padding_top"
                   min="0" max="200" step="10"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Padding Bottom --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Padding Bottom <span class="text-gray-400" x-text="(editingSection.settings.padding_bottom || 60) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.padding_bottom"
                   min="0" max="200" step="10"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
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
                       placeholder="transparent"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>
    </div>
    
</div>
