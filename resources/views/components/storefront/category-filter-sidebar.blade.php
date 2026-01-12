<div class="sticky top-24 space-y-8">
    <h3 class="font-heading font-bold text-gray-900 mb-6 text-lg">Filters</h3>
    
    <!-- Price Range -->
    <div class="mb-8 pb-8 border-b border-gray-100">
        <h4 class="font-semibold text-gray-700 mb-4 text-sm">Price Range</h4>
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <input type="number" 
                       placeholder="Min" 
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#0A2540] focus:ring-1 focus:ring-[#0A2540] transition-colors">
                <span class="text-gray-400">—</span>
                <input type="number" 
                       placeholder="Max" 
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-[#0A2540] focus:ring-1 focus:ring-[#0A2540] transition-colors">
            </div>
            <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2.5 rounded-lg transition-colors text-sm">
                Apply
            </button>
        </div>
    </div>

    <!-- Sort By -->
    <div>
        <h4 class="font-semibold text-gray-700 mb-4 text-sm">Sort By</h4>
        <select class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:border-[#0A2540] focus:ring-1 focus:ring-[#0A2540] transition-colors bg-white">
            <option value="newest">Newest First</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="name_asc">Name: A to Z</option>
        </select>
    </div>

    <!-- Clear Filters -->
    <div class="mt-6 pt-6 border-t border-gray-100">
        <button class="w-full text-sm text-gray-500 hover:text-[#0A2540] font-medium transition-colors">
            Clear All Filters
        </button>
    </div>
</div>
