<template>
    <div>
        <!-- Trigger Button (Visible on Desktop/Mobile) -->
        <button @click="open" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-500 px-3 py-1.5 rounded-lg text-sm border border-slate-200 transition-colors mr-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <span class="hidden md:inline">Search...</span>
            <span class="hidden md:inline-flex items-center justify-center bg-white border border-slate-300 rounded px-1.5 text-xs font-mono ml-2">⌘K</span>
        </button>

        <!-- Command Palette Modal -->
        <div v-if="isVisible" class="fixed inset-0 z-[60] overflow-y-auto p-4 sm:p-6 md:p-20" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-25 transition-opacity" @click="close" aria-hidden="true"></div>

            <!-- Modal Panel -->
            <div class="mx-auto max-w-xl transform divide-y divide-gray-100 overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
                <div class="relative">
                    <svg class="pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input 
                        ref="searchInput"
                        type="text" 
                        class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-800 placeholder-gray-400 focus:ring-0 sm:text-sm" 
                        placeholder="Search for pages, features, or settings..."
                        v-model="query"
                        @keydown.down.prevent="onArrowDown"
                        @keydown.up.prevent="onArrowUp"
                        @keydown.enter.prevent="onEnter"
                        @keydown.esc="close"
                    >
                </div>

                <!-- Results List -->
                <ul v-if="filteredResults.length > 0" class="max-h-80 scroll-py-2 overflow-y-auto py-2 text-sm text-gray-800" id="options" role="listbox">
                    <li 
                        v-for="(result, index) in filteredResults" 
                        :key="index"
                        class="cursor-default select-none px-4 py-2"
                        :class="{'bg-indigo-600 text-white': index === selectedIndex}"
                        @mouseenter="selectedIndex = index"
                        @click="navigate(result.item.url)"
                    >
                        <div class="flex items-center">
                            <!-- Icon logic could be added here if 'icon' prop exists in actions -->
                            <span :class="{'text-indigo-200': index === selectedIndex, 'text-gray-500': index !== selectedIndex}" class="mr-3">
                                <i :class="result.item.icon || 'fas fa-link'"></i>
                            </span>
                            <span :class="{'font-semibold': index === selectedIndex}">{{ result.item.title }}</span>
                            <span v-if="result.item.category" :class="{'text-indigo-200': index === selectedIndex, 'text-gray-400': index !== selectedIndex}" class="ml-auto text-xs">{{ result.item.category }}</span>
                        </div>
                    </li>
                </ul>

                <!-- Empty State -->
                <div v-if="query && filteredResults.length === 0" class="py-14 px-6 text-center text-sm sm:px-14">
                    <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-4 font-semibold text-gray-900">No results found</p>
                    <p class="mt-2 text-gray-500">No components found for this search term. Please try again.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Fuse from 'fuse.js';

export default {
    props: {
        tenantSlug: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            isVisible: false,
            query: '',
            selectedIndex: 0,
            fuse: null,
            // Pre-defined list of actions/pages
            actions: [
                // Core
                { title: 'Dashboard', url: `/admin/dashboard`, category: 'General', keywords: 'home stats overview', icon: 'fas fa-home' },
                { title: 'Settings', url: `/admin/settings`, category: 'Settings', keywords: 'config setup profile preferences', icon: 'fas fa-cog' },
                { title: 'Notifications', url: `/admin/notifications`, category: 'General', keywords: 'alerts messages', icon: 'fas fa-bell' },
                
                // Sales
                { title: 'POS System', url: `/admin/pos`, category: 'Sales', keywords: 'point of sale retail checkout sell', icon: 'fas fa-cash-register' },
                { title: 'Orders', url: `/admin/orders`, category: 'Sales', keywords: 'sales transactions history', icon: 'fas fa-shopping-cart' },
                { title: 'Create Order', url: `/admin/orders/create`, category: 'Sales', keywords: 'new sale manual order', icon: 'fas fa-plus' },
                { title: 'Customers', url: `/admin/customers`, category: 'Sales', keywords: 'clients people buyers', icon: 'fas fa-users' },
                
                // Inventory
                { title: 'Products', url: `/admin/products`, category: 'Inventory', keywords: 'items stock catalogue merchandising', icon: 'fas fa-box' },
                { title: 'Add Product', url: `/admin/products/create`, category: 'Inventory', keywords: 'new item create stock', icon: 'fas fa-plus-circle' },
                { title: 'Categories', url: `/admin/categories`, category: 'Inventory', keywords: 'groups collections taxonomy', icon: 'fas fa-tags' },
                { title: 'Warehouses', url: `/admin/warehouses`, category: 'Inventory', keywords: 'locations storage depots', icon: 'fas fa-warehouse' },
                { title: 'Stock Transfers', url: `/admin/stock-transfers`, category: 'Inventory', keywords: 'move stock shipment relocation', icon: 'fas fa-truck' },
                { title: 'Purchase Orders', url: `/admin/purchase-orders`, category: 'Inventory', keywords: 'procurement buy supply supplier', icon: 'fas fa-file-invoice' },
                
                // Finance
                { title: 'Incomes', url: `/admin/incomes`, category: 'Finance', keywords: 'revenue money in earnings', icon: 'fas fa-arrow-up' },
                { title: 'Expenses', url: `/admin/expenses`, category: 'Finance', keywords: 'costs bills money out spending', icon: 'fas fa-arrow-down' },
                { title: 'Reports: Sales', url: `/admin/reports/sales`, category: 'Reports', keywords: 'analytics charts performance', icon: 'fas fa-chart-line' },
                { title: 'Reports: Inventory', url: `/admin/reports/inventory`, category: 'Reports', keywords: 'stock level valuation value', icon: 'fas fa-chart-bar' },
                
                // Site
                { title: 'Pages', url: `/admin/pages`, category: 'Website', keywords: 'cms content site', icon: 'fas fa-file-alt' },
                { title: 'Banners', url: `/admin/banners`, category: 'Website', keywords: 'sliders promo ads', icon: 'fas fa-image' },
                { title: 'Coupons', url: `/admin/coupons`, category: 'Marketing', keywords: 'discounts promo codes', icon: 'fas fa-ticket-alt' },
            ]
        };
    },
    computed: {
        filteredResults() {
            if (!this.query) {
                return [];
            }
            return this.fuse.search(this.query).slice(0, 10); // Limit to top 10
        }
    },
    mounted() {
        // Fix up URLs with correct tenant slug
        this.actions.forEach(action => {
            if (action.url.startsWith('/admin')) {
                action.url = `/${this.tenantSlug}${action.url}`;
            }
        });

        const options = {
            keys: ['title', 'keywords', 'category'],
            threshold: 0.4, // Fuzzy match sensitivity
            includeScore: true
        };
        
        this.fuse = new Fuse(this.actions, options);

        window.addEventListener('keydown', this.onKeydown);
    },
    beforeUnmount() {
        window.removeEventListener('keydown', this.onKeydown);
    },
    methods: {
        open() {
            this.isVisible = true;
            this.query = '';
            this.selectedIndex = 0;
            this.$nextTick(() => {
                if(this.$refs.searchInput) this.$refs.searchInput.focus();
            });
        },
        close() {
            this.isVisible = false;
            this.query = '';
        },
        onKeydown(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                this.isVisible ? this.close() : this.open();
            }
        },
        onArrowDown() {
            if (this.selectedIndex < this.filteredResults.length - 1) {
                this.selectedIndex++;
            }
        },
        onArrowUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },
        onEnter() {
            if (this.filteredResults.length > 0) {
                this.navigate(this.filteredResults[this.selectedIndex].item.url);
            }
        },
        navigate(url) {
            window.location.href = url;
            this.close();
        }
    }
}
</script>
