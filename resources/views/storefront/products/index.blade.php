<x-storefront.layout :config="$config">
    <div class="bg-gray-50 min-h-screen py-8"
     x-data="{
         ...productListing({       
             initialViewMode: '{{ $viewMode }}',
             initialSort: 'newest',
             layoutMode: '{{ $layoutMode }}'
         }),
         ...cartActions()
     }"
     x-init="fetchProducts()"
    >
    <div class="container mx-auto px-4">
        
        <!-- Header: Title + Controls -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <h1 class="text-3xl font-bold font-heading text-gray-900 mb-4 md:mb-0">Shop All</h1>
            
            <div class="flex items-center space-x-4">
                <!-- View Switcher -->
                <div class="flex bg-white rounded border border-gray-200 p-1">
                    <button @click="viewMode = 'grid'" 
                            :class="{'bg-gray-100 text-gray-900': viewMode === 'grid', 'text-gray-500': viewMode !== 'grid'}"
                            class="p-2 rounded hover:text-gray-900 transition-colors"
                            aria-label="Grid View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button @click="viewMode = 'table'" 
                            :class="{'bg-gray-100 text-gray-900': viewMode === 'table', 'text-gray-500': viewMode !== 'table'}"
                            class="p-2 rounded hover:text-gray-900 transition-colors"
                            aria-label="Table View">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>

                <!-- Sort Dropdown -->
                <select x-model="filters.sort" @change="fetchProducts()" class="border-gray-200 rounded text-sm focus:ring-brand-500 focus:border-brand-500">
                    <option value="newest">Newest Arrivals</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="name_asc">Name: A-Z</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filter -->
            <aside class="w-full lg:w-1/4">
                <x-storefront.filter-sidebar />
            </aside>

            <!-- Main Content -->
            <main class="w-full lg:w-3/4">
                <!-- Loading State -->
                <div x-show="loading" class="flex justify-center py-20">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-brand-600"></div>
                </div>

                <!-- Product Listing Layout -->
                <div x-show="!loading" style="display: none;">
                   <x-storefront.product-listing-layout />
                   
                   <!-- Pagination -->
                   <!-- Infinite Scroll Loading Trigger -->
                   <div x-intersect.threshold.20="handleScroll" class="py-8 flex justify-center" x-show="meta.current_page < meta.last_page">
                       <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-600"></div>
                   </div>
                        <nav class="flex space-x-1">
                            <button @click="changePage(meta.current_page - 1)" 
                                    :disabled="meta.current_page === 1"
                                    class="px-4 py-2 border rounded-md" 
                                    :class="meta.current_page === 1 ? 'text-gray-300 border-gray-200' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'">
                                Previous
                            </button>
                            
                            <span class="px-4 py-2 text-gray-700">Page <span x-text="meta.current_page"></span> of <span x-text="meta.last_page"></span></span>

                            <button @click="changePage(meta.current_page + 1)" 
                                    :disabled="meta.current_page === meta.last_page"
                                    class="px-4 py-2 border rounded-md"
                                    :class="meta.current_page === meta.last_page ? 'text-gray-300 border-gray-200' : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'">
                                Next
                            </button>
                    <!-- Pagination buttons removed for infinite scroll -->
                </div>
            </main>
        </div>
    </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productListing', (config) => ({
                viewMode: config.initialViewMode,
                loading: true,
                products: [],
                meta: {},
                categories: [],
                priceRange: { min: 0, max: 1000 },
                filters: {
                    category_slug: new URLSearchParams(window.location.search).get('category') || '',
                    min_price: null,
                    max_price: null,
                    sort: config.initialSort,
                    page: 1
                },

                async fetchProducts(append = false) {
                    if (this.loading && !append) return; // Prevent double load on init, but allow if appending (handled carefully)
                    
                    if (!append) {
                        this.loading = true;
                        this.products = [];
                        this.filters.page = 1;
                    } else {
                        // Don't set main loading true for background append, maybe a separate state if needed, 
                        // but here we just fetching next page.
                    }

                    // Construct Query Params
                    const params = new URLSearchParams();
                    if (this.filters.category_slug) params.append('category_slug', this.filters.category_slug);
                    if (this.filters.min_price) params.append('min_price', this.filters.min_price);
                    if (this.filters.max_price) params.append('max_price', this.filters.max_price);
                    params.append('sort', this.filters.sort);
                    params.append('page', this.filters.page);

                    // Update Browser URL (only on fresh filter change, not every scroll page ideally, but ok for now)
                    if (!append) {
                        const url = new URL(window.location);
                        url.search = params.toString();
                        window.history.pushState({}, '', url);
                    }

                    try {
                        const response = await fetch(`{{ route('storefront.api.products') }}?${params.toString()}&_t=${new Date().getTime()}`);
                        const json = await response.json();
                        
                        if (append) {
                            this.products = [...this.products, ...json.data];
                        } else {
                            this.products = json.data;
                            // Only update metadata filters on initial load/reset
                            if (this.categories.length === 0) {
                                this.categories = json.filters.categories;
                                this.priceRange = json.filters.price_range;
                                if (!this.filters.min_price) this.filters.min_price = this.priceRange.min;
                                if (!this.filters.max_price) this.filters.max_price = this.priceRange.max;
                            }
                        }
                        
                        this.meta = json.meta;
                        
                    } catch (error) {
                        console.error('Failed to load products', error);
                    } finally {
                        this.loading = false;
                    }
                },

                handleScroll() {
                     if (this.meta.current_page < this.meta.last_page && !this.loading) {
                        this.filters.page++;
                         // Use a flag or pass true to fetchProducts to append
                        this.fetchProducts(true);
                     }
                },

                toggleCategory(slug) {
                    // Toggle logic: if already selected, clear it. Single select for now as per API design.
                    if (this.filters.category_slug === slug) {
                        this.filters.category_slug = '';
                    } else {
                        this.filters.category_slug = slug;
                    }
                    this.fetchProducts(false);
                },
                
                applyPriceFilter(min, max) {
                    this.filters.min_price = min;
                    this.filters.max_price = max;
                    this.fetchProducts(false);
                },

                changePage(page) {
                    // Deprecated for infinite scroll but kept if we revert
                    this.filters.page = page;
                    this.fetchProducts(false);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }));
        });
    </script>
    @endpush
</x-storefront.layout>
