{{-- Advanced Control Components for Page Builder --}}

{{-- Range Slider with Live Value Display --}}
@php
    function renderRangeSlider($name, $label, $min, $max, $step, $unit, $default, $device = null) {
        $deviceSuffix = $device ? "_{$device}" : '';
        $fullName = $name . $deviceSuffix;
        return <<<HTML
<div class="mb-4">
    <label class="block text-xs font-medium text-gray-700 mb-2">
        {$label}
        <span class="text-gray-400 ml-1" x-text="(editingSection.settings.{$fullName} || {$default}) + '{$unit}'"></span>
    </label>
    <input type="range" 
           x-model.number="editingSection.settings.{$fullName}"
           min="{$min}" 
           max="{$max}" 
           step="{$step}"
           @input="updatePreview()"
           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
</div>
HTML;
    }
@endphp

{{-- Responsive Toggle Component --}}
<div x-data="{ deviceMode: 'desktop' }" class="mb-6">
    <div class="flex justify-between items-center mb-3">
        <h4 class="text-sm font-semibold text-gray-800">{{ $sectionTitle ?? 'Settings' }}</h4>
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
    
    {{-- Desktop Controls --}}
    <div x-show="deviceMode === 'desktop'" x-transition>
        {{ $desktopSlot ?? '' }}
    </div>
    
    {{-- Mobile Controls --}}
    <div x-show="deviceMode === 'mobile'" x-transition>
        {{ $mobileSlot ?? '' }}
    </div>
</div>

{{-- Color Picker with Opacity --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-gray-700 mb-2">{{ $colorLabel ?? 'Color' }}</label>
    <div class="flex gap-2">
        <input type="color" 
               x-model="editingSection.settings.{{ $colorField }}"
               @input="updatePreview()"
               class="h-10 w-12 rounded border border-gray-300 cursor-pointer">
        <input type="text" 
               x-model="editingSection.settings.{{ $colorField }}"
               @input="updatePreview()"
               placeholder="#000000"
               class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
    </div>
    @if($withOpacity ?? false)
        <div class="mt-2">
            <label class="block text-xs text-gray-600 mb-1">
                Opacity: <span x-text="(editingSection.settings.{{ $opacityField }} || 100) + '%'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.{{ $opacityField }}"
                   min="0" 
                   max="100" 
                   step="5"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
    @endif
</div>

{{-- Text Alignment Buttons --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-gray-700 mb-2">{{ $alignLabel ?? 'Text Alignment' }}</label>
    <div class="flex gap-1">
        <button type="button"
                @click="editingSection.settings.{{ $alignField }} = 'left'; updatePreview()"
                :class="{'bg-indigo-600 text-white': editingSection.settings.{{ $alignField }} === 'left', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.{{ $alignField }} !== 'left'}"
                class="flex-1 py-2 rounded text-sm font-medium transition">
            <i class="fas fa-align-left"></i>
        </button>
        <button type="button"
                @click="editingSection.settings.{{ $alignField }} = 'center'; updatePreview()"
                :class="{'bg-indigo-600 text-white': editingSection.settings.{{ $alignField }} === 'center', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.{{ $alignField }} !== 'center'}"
                class="flex-1 py-2 rounded text-sm font-medium transition">
            <i class="fas fa-align-center"></i>
        </button>
        <button type="button"
                @click="editingSection.settings.{{ $alignField }} = 'right'; updatePreview()"
                :class="{'bg-indigo-600 text-white': editingSection.settings.{{ $alignField }} === 'right', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.{{ $alignField }} !== 'right'}"
                class="flex-1 py-2 rounded text-sm font-medium transition">
            <i class="fas fa-align-right"></i>
        </button>
        <button type="button"
                @click="editingSection.settings.{{ $alignField }} = 'justify'; updatePreview()"
                :class="{'bg-indigo-600 text-white': editingSection.settings.{{ $alignField }} === 'justify', 'bg-gray-100 text-gray-600 hover:bg-gray-200': editingSection.settings.{{ $alignField }} !== 'justify'}"
                class="flex-1 py-2 rounded text-sm font-medium transition">
            <i class="fas fa-align-justify"></i>
        </button>
    </div>
</div>

{{-- Image Upload with Preview --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-gray-700 mb-2">{{ $imageLabel ?? 'Image' }}</label>
    
    {{-- Preview --}}
    <div x-show="editingSection.settings.{{ $imageField }}" class="mb-2">
        <img :src="editingSection.settings.{{ $imageField }}" 
             alt="Preview" 
             class="w-full h-32 object-cover rounded border border-gray-200">
    </div>
    
    {{-- Upload Controls --}}
    <div class="flex gap-2">
        <input type="text" 
               x-model="editingSection.settings.{{ $imageField }}"
               @input="updatePreview()"
               placeholder="https://... or upload"
               class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        <label class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium transition flex items-center">
            <i class="fas fa-upload mr-2"></i> Upload
            <input type="file" 
                   @change="uploadImage($event, '{{ $imageField }}')" 
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

{{-- Spacing Controls (4 Sides) --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-gray-700 mb-3">{{ $spacingLabel ?? 'Padding' }}</label>
    <div class="grid grid-cols-2 gap-3">
        {{-- Top --}}
        <div>
            <label class="block text-xs text-gray-600 mb-1">
                Top <span x-text="(editingSection.settings.{{ $spacingPrefix }}_top{{ $spacingSuffix ?? '' }} || 0) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.{{ $spacingPrefix }}_top{{ $spacingSuffix ?? '' }}"
                   min="{{ $min ?? 0 }}" 
                   max="{{ $max ?? 300 }}" 
                   step="{{ $step ?? 10 }}"
                   @input="updatePreview()"
                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Bottom --}}
        <div>
            <label class="block text-xs text-gray-600 mb-1">
                Bottom <span x-text="(editingSection.settings.{{ $spacingPrefix }}_bottom{{ $spacingSuffix ?? '' }} || 0) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.{{ $spacingPrefix }}_bottom{{ $spacingSuffix ?? '' }}"
                   min="{{ $min ?? 0 }}" 
                   max="{{ $max ?? 300 }}" 
                   step="{{ $step ?? 10 }}"
                   @input="updatePreview()"
                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Left --}}
        <div>
            <label class="block text-xs text-gray-600 mb-1">
                Left <span x-text="(editingSection.settings.{{ $spacingPrefix }}_left{{ $spacingSuffix ?? '' }} || 0) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.{{ $spacingPrefix }}_left{{ $spacingSuffix ?? '' }}"
                   min="{{ $min ?? 0 }}" 
                   max="{{ $max ?? 300 }}" 
                   step="{{ $step ?? 10 }}"
                   @input="updatePreview()"
                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Right --}}
        <div>
            <label class="block text-xs text-gray-600 mb-1">
                Right <span x-text="(editingSection.settings.{{ $spacingPrefix }}_right{{ $spacingSuffix ?? '' }} || 0) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.{{ $spacingPrefix }}_right{{ $spacingSuffix ?? '' }}"
                   min="{{ $min ?? 0 }}" 
                   max="{{ $max ?? 300 }}" 
                   step="{{ $step ?? 10 }}"
                   @input="updatePreview()"
                   class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
    </div>
</div>

{{-- Select Dropdown --}}
<div class="mb-4">
    <label class="block text-xs font-medium text-gray-700 mb-2">{{ $selectLabel }}</label>
    <select x-model="editingSection.settings.{{ $selectField }}"
            @change="updatePreview()"
            class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        {{ $selectOptions }}
    </select>
</div>

{{-- Checkbox Toggle --}}
<div class="mb-4">
    <label class="flex items-center cursor-pointer">
        <input type="checkbox" 
               x-model="editingSection.settings.{{ $checkboxField }}"
               @change="updatePreview()"
               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
        <span class="text-sm text-gray-700">{{ $checkboxLabel }}</span>
    </label>
</div>

{{-- Section Divider --}}
<hr class="my-6 border-gray-200">

{{-- Collapsible Section --}}
<div x-data="{ open: {{ $defaultOpen ?? 'true' }} }" class="mb-4">
    <button type="button"
            @click="open = !open"
            class="w-full flex items-center justify-between text-left py-2 px-3 bg-gray-50 hover:bg-gray-100 rounded transition">
        <span class="text-sm font-semibold text-gray-800">{{ $sectionTitle }}</span>
        <i class="fas transition-transform" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
    </button>
    <div x-show="open" x-collapse class="mt-3">
        {{ $sectionContent }}
    </div>
</div>
