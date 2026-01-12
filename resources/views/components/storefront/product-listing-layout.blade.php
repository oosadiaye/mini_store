<div>
    <template x-if="viewMode === 'grid'">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            <template x-for="product in products" :key="product.id">
                <!-- Inline Card Template or Component Reference -->
                <div class="group relative bg-white border border-gray-100 rounded-lg hover:shadow-lg transition-shadow duration-300 overflow-hidden flex flex-col">
                    <!-- Image -->
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200 xl:aspect-w-7 xl:aspect-h-8 relative">
                         <a :href="product.url" class="block h-full w-full">
                            <img :src="product.image_url" :alt="product.name" class="h-full w-full object-cover object-center group-hover:opacity-75 transition-opacity duration-300">
                         </a>
                         
                         <!-- Discount Badge -->
                         <template x-if="product.discount_percentage > 0">
                            <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded" x-text="`-${product.discount_percentage}%`"></span>
                         </template>
                         
                         <!-- Quick Add Overlay (Optional) -->
                   </div>
                   
                    <div class="p-3 md:p-4 flex-1 flex flex-col">
                        <p class="mt-1 text-[10px] md:text-sm text-gray-500 uppercase tracking-wide" x-text="product.category"></p>
                        <h3 class="text-sm md:text-base font-medium text-gray-900 truncate">
                            <a :href="product.url" class="hover:text-brand-600 hover:underline transition-colors" x-text="product.name"></a>
                        </h3>
                        
                        <div class="mt-auto pt-2 md:pt-4 flex items-center justify-between">
                            <div>
                                <template x-if="product.compare_at_price > product.price">
                                    <p class="text-[10px] md:text-xs text-gray-400 line-through" x-text="`₦${product.compare_at_price.toLocaleString()}`"></p>
                                </template>
                                <p class="text-base md:text-lg font-bold text-gray-900" x-text="`₦${product.active_price.toLocaleString()}`"></p>
                            </div>
                            
                            <!-- Simple Add Button -->
<button @click.stop="addToCart(product.id)" 
                                    :disabled="loading === product.id"
                                    class="bg-[#0A2540] text-white p-2 rounded-full hover:bg-[#1a3a5a] transition-all transform hover:scale-110 shadow-md relative overflow-hidden" 
                                    title="Add to Cart">
                                    
                                    <span x-show="loading !== product.id">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    </span>
                                    <span x-show="loading === product.id" class="absolute inset-0 flex items-center justify-center bg-[#0A2540]">
                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                            </button>
                        </div>
                   </div>
                   
                   
                   <!-- Link Wrapper Removed -->
                   <!-- <a :href="`/products/${product.slug}`" class="absolute inset-0 z-10" aria-hidden="true"></a> -->
                   
                   <!-- Z-Index Fix for buttons if we want interactive elements inside -->
                </div>
            </template>
        </div>
    </template>

    <template x-if="viewMode === 'table'">
        <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Image</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Info</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="product in products" :key="product.id">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a :href="product.url" class="block">
                                    <img :src="product.image_url" class="h-12 w-12 rounded object-cover border border-gray-200">
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <a :href="product.url" class="hover:text-brand-600 hover:underline" x-text="product.name"></a>
                                </div>
                                <div class="text-xs text-gray-500" x-text="product.category"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                      x-show="product.stock_status === 'in_stock'">In Stock</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
                                      x-show="product.stock_status === 'out_of_stock'">Out of Stock</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                <span x-text="`₦${product.active_price.toLocaleString()}`"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button @click.stop="addToCart(product.id)"
                                        :disabled="loading === product.id" 
                                        class="text-brand-600 hover:text-brand-900 border border-brand-600 hover:bg-brand-50 px-3 py-1 rounded text-sm transition-colors flex items-center justify-center gap-2 min-w-[100px]">
                                    <span x-show="loading !== product.id">Add to Cart</span>
                                    <span x-show="loading === product.id">
                                        <svg class="animate-spin h-4 w-4 text-brand-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </template>
    
    <!-- Empty State -->
    <template x-if="products.length === 0">
        <div class="text-center py-20">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
        </div>
    </template>
</div>
