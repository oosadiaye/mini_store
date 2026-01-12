<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <!-- Categories -->
    <div class="mb-8">
        <h3 class="font-heading font-semibold text-gray-900 mb-4">Categories</h3>
        <div class="space-y-2">
            <template x-for="category in categories" :key="category.slug">
                <label class="flex items-center space-x-3 cursor-pointer group">
                    <input type="checkbox" 
                           :checked="filters.category_slug === category.slug"
                           @change="toggleCategory(category.slug)"
                           class="form-checkbox h-5 w-5 text-brand-600 rounded border-2 border-gray-300 focus:ring-brand-500 transition duration-150 ease-in-out">
                    <span class="text-gray-700 group-hover:text-brand-600 transition-colors" x-text="category.name"></span>
                    <span class="text-xs text-gray-400 ml-auto" x-text="`(${category.count})`"></span>
                </label>
            </template>
        </div>
    </div>

    <!-- Featured Collection -->
    <div class="mb-8 pb-8 border-b border-gray-100">
        <h3 class="font-heading font-semibold text-gray-900 mb-4">Collections</h3>
        <label class="flex items-center space-x-3 cursor-pointer group">
            <input type="checkbox" 
                   :checked="filters.is_featured"
                   @change="filters.is_featured = !filters.is_featured; filters.page = 1; fetchProducts()"
                   class="form-checkbox h-5 w-5 text-brand-600 rounded border-2 border-gray-300 focus:ring-brand-500 transition duration-150 ease-in-out">
            <span class="text-gray-700 group-hover:text-brand-600 transition-colors">Featured Products</span>
        </label>
    </div>

    <!-- Price Range -->
    <div>
        <h3 class="font-heading font-semibold text-gray-900 mb-4">Price Range</h3>
        <div class="px-2">
            <!-- Simple Range Inputs for Min/Max -->
            <div class="flex items-center space-x-4 mb-4">
                <input type="number" x-model="filters.min_price" 
                       @change="filters.page = 1; fetchProducts()"
                       class="w-full px-3 py-2 border-2 border-gray-300 rounded text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500" placeholder="Min">
                <span class="text-gray-400">-</span>
                <input type="number" x-model="filters.max_price" 
                       @change="filters.page = 1; fetchProducts()"
                       class="w-full px-3 py-2 border-2 border-gray-300 rounded text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500" placeholder="Max">
            </div>
            
            <button @click="filters.page=1; fetchProducts()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 rounded transition-colors text-sm">
                Apply Filter
            </button>
        </div>
    </div>
</div>
