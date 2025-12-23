{{-- Product Grid Section Editor --}}
<div class="space-y-6">
    
    {{-- Content Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Content</h4>
        
        {{-- Section Title --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Section Title</label>
            <input type="text" 
                   x-model="editingSection.title"
                   @input="updatePreview()"
                   placeholder="Featured Products"
                   class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
        
        {{-- Product Limit --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Number of Products <span class="text-gray-400" x-text="(editingSection.settings.limit || 8)"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.limit"
                   min="1" max="50" step="1"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Category Filter --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Filter by Category</label>
            <select x-model="editingSection.settings.category_filter"
                    @change="updatePreview()"
                    class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="">All Categories</option>
                {{-- Categories will be populated dynamically --}}
                @if(isset($categories))
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        
        {{-- Sort By --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Sort By</label>
            <select x-model="editingSection.settings.sort_by"
                    @change="updatePreview()"
                    class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="latest">Latest</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="name">Name</option>
                <option value="popular">Most Popular</option>
            </select>
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Layout Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Grid Layout</h4>
        
        {{-- Desktop Columns --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Columns (Desktop) <span class="text-gray-400" x-text="(editingSection.settings.columns_desktop || 4)"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.columns_desktop"
                   min="1" max="6" step="1"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>1</span>
                <span>2</span>
                <span>3</span>
                <span>4</span>
                <span>5</span>
                <span>6</span>
            </div>
        </div>
        
        {{-- Tablet Columns --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Columns (Tablet) <span class="text-gray-400" x-text="(editingSection.settings.columns_tablet || 3)"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.columns_tablet"
                   min="1" max="4" step="1"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>1</span>
                <span>2</span>
                <span>3</span>
                <span>4</span>
            </div>
        </div>
        
        {{-- Mobile Columns --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Columns (Mobile) <span class="text-gray-400" x-text="(editingSection.settings.columns_mobile || 2)"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.columns_mobile"
                   min="1" max="3" step="1"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            <div class="flex justify-between text-xs text-gray-500 mt-1">
                <span>1</span>
                <span>2</span>
                <span>3</span>
            </div>
        </div>
        
        {{-- Gap Desktop --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Gap (Desktop) <span class="text-gray-400" x-text="(editingSection.settings.gap_desktop || 24) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.gap_desktop"
                   min="0" max="100" step="4"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Gap Mobile --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Gap (Mobile) <span class="text-gray-400" x-text="(editingSection.settings.gap_mobile || 16) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.gap_mobile"
                   min="0" max="50" step="4"
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
                Padding Top <span class="text-gray-400" x-text="(editingSection.settings.padding_top || 80) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.padding_top"
                   min="0" max="300" step="10"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Padding Bottom --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Padding Bottom <span class="text-gray-400" x-text="(editingSection.settings.padding_bottom || 80) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.padding_bottom"
                   min="0" max="300" step="10"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Container Section --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Container</h4>
        
        {{-- Container Width --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Container Width</label>
            <select x-model="editingSection.settings.container_width"
                    @change="updatePreview()"
                    class="w-full text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="container">Contained (1200px)</option>
                <option value="container-lg">Large (1400px)</option>
                <option value="full">Full Width</option>
            </select>
        </div>
    </div>
    
    <hr class="border-gray-200">
    
    {{-- Product Card Styling --}}
    <div>
        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Product Card Styling</h4>
        
        {{-- Card Background --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">Card Background</label>
            <div class="flex gap-2">
                <input type="color" 
                       x-model="editingSection.settings.card_background"
                       @input="updatePreview()"
                       class="h-10 w-12 rounded border border-gray-300 cursor-pointer">
                <input type="text" 
                       x-model="editingSection.settings.card_background"
                       @input="updatePreview()"
                       placeholder="#ffffff"
                       class="flex-1 text-sm border-gray-300 rounded focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>
        
        {{-- Card Border Radius --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700 mb-2">
                Border Radius <span class="text-gray-400" x-text="(editingSection.settings.card_border_radius || 8) + 'px'"></span>
            </label>
            <input type="range" 
                   x-model.number="editingSection.settings.card_border_radius"
                   min="0" max="50" step="2"
                   @input="updatePreview()"
                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
        </div>
        
        {{-- Show Quick View --}}
        <div class="mb-4">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       x-model="editingSection.settings.show_quick_view"
                       @change="updatePreview()"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                <span class="text-sm text-gray-700">Show Quick View Button</span>
            </label>
        </div>
        
        {{-- Show Add to Cart --}}
        <div class="mb-4">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       x-model="editingSection.settings.show_add_to_cart"
                       @change="updatePreview()"
                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                <span class="text-sm text-gray-700">Show Add to Cart Button</span>
            </label>
        </div>
    </div>
    
</div>
