<div class="flex flex-col h-full bg-slate-900 text-white border-r border-slate-800">
    <!-- Logo area -->
    <div class="flex items-center justify-center h-16 shrink-0 px-4 border-b border-slate-800 bg-slate-950">
        <a href="{{ route('admin.dashboard', ['tenant' => $tenant->slug]) }}" class="flex items-center space-x-3">
            <x-logo size="sm" class="flex-shrink-0" />
            <div class="flex flex-col">
                <span class="font-bold text-lg tracking-tight text-white">{{ $tenant->name }}</span>
                <span class="text-xs text-slate-500">{{ $tenant->id }}</span>
            </div>
        </a>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-3 pb-20">
        <nav class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                Dashboard
            </a>

            <!-- Inventory Section -->
            @if($tenant->hasFeature('inventory'))
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Inventory & Stock</h3>
            </div>
            
            <a href="{{ route('admin.products.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.products.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                Products
            </a>

            <a href="{{ route('admin.categories.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                Categories
            </a>

            <a href="{{ route('admin.brands.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.brands.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.brands.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42" />
                </svg>
                Brands
            </a>

            @if($tenant->hasFeature('inventory_advanced'))
            <a href="{{ route('admin.purchase-orders.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.purchase-orders.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                 <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.purchase-orders.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                 </svg>
                Purchases
            </a>

            <a href="{{ route('admin.stock-transfers.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.stock-transfers.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.stock-transfers.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
                Stock Transfers
            </a>

            <a href="{{ route('admin.warehouses.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.warehouses.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.warehouses.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                </svg>
                Warehouses
            </a>

            <a href="{{ route('admin.suppliers.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.suppliers.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.suppliers.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
                Suppliers
            </a>

            @if($tenant->hasFeature('reports_inventory'))
             <a href="{{ route('admin.reports.inventory', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.reports.inventory') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.reports.inventory') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Stock Report
            </a>
            @endif
            @endif
            @endif

            <!-- Renters -->
            <a href="{{ route('admin.renters.index', $tenant->slug) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.renters.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.renters.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Renters
            </a>

            <!-- Sales Section -->
            @if($tenant->hasFeature('pos_retail') || $tenant->hasFeature('online_store'))
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Sales & POS</h3>
            </div>
            
            @if($tenant->hasFeature('online_store'))
            <a href="{{ route('admin.orders.index', ['tenant' => $tenant->slug, 'source' => 'storefront']) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->query('source') == 'storefront' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->query('source') == 'storefront' ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                Online Orders
            </a>
            @endif

            <a href="{{ route('admin.orders.index', ['tenant' => $tenant->slug, 'source' => 'admin']) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->query('source') == 'admin' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->query('source') == 'admin' ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Manual Orders
            </a>

            @if($tenant->hasFeature('inventory_advanced'))
            @endif

            @if($tenant->hasFeature('pos_retail'))
            <a href="{{ route('admin.pos.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.pos.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.pos.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                POS Terminal
            </a>
            @endif

            @if($tenant->hasFeature('crm'))
            <a href="{{ route('admin.customers.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.customers.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.customers.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Customers
            </a>
            @endif

            @endif

             <!-- Finance Section -->
            @if($tenant->hasFeature('accounting_core'))
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Accounting</h3>
            </div>

            <a href="{{ route('admin.payments.index', ['tenant' => $tenant->slug]) }}"
                class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                 <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.payments.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                 </svg>
                 Payments Due
            </a>

            <a href="{{ route('admin.accounts.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.accounts.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.accounts.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                Accounts
            </a>

            <a href="{{ route('admin.incomes.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.incomes.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.incomes.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
                Income
            </a>

            <a href="{{ route('admin.expenses.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.expenses.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                 <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.expenses.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" />
                </svg>
                Expenses
            </a>

            <a href="{{ route('admin.tax-codes.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.tax-codes.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                 <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.tax-codes.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                </svg>
                Tax Codes
            </a>

            @if($tenant->hasFeature('accounting_advanced'))
            <a href="{{ route('admin.accounting.trial-balance', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.accounting.trial-balance') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.accounting.trial-balance') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0l2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 01-2.031.352 5.988 5.988 0 01-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971z" />
                </svg>
                Trial Balance
            </a>
            @endif

            <a href="{{ route('admin.accounting.profit-loss', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.accounting.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.accounting.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                </svg>
                Profit & Loss
            </a>
            @endif

            <!-- Reports & Analytics Section -->
            @if($tenant->hasFeature('reports_basic'))
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Reports & Analytics</h3>
            </div>

            <a href="{{ route('admin.reports.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                Reports Dashboard
            </a>
            @endif

             <!-- Marketing Section -->
             @if($tenant->hasFeature('marketing'))
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Marketing</h3>
            </div>

            <a href="{{ route('admin.coupons.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.coupons.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.coupons.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                </svg>
                Coupons
            </a>
            
            <a href="{{ route('admin.enquiries.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.enquiries.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.enquiries.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                </svg>
                Product Enquiries
                @php
                    $pendingEnquiries = \App\Models\ProductEnquiry::pending()->count();
                @endphp
                @if($pendingEnquiries > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingEnquiries }}</span>
                @endif
            </a>

            <a href="{{ route('admin.abandoned-carts.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.abandoned-carts.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.abandoned-carts.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                Abandoned Carts
            </a>
            @endif
            <!-- Team Section -->
            @if($tenant->hasFeature('team_management'))
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Team & Access</h3>
            </div>

            <a href="{{ route('admin.users.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                Staff Members
            </a>

            <a href="{{ route('admin.roles.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.roles.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.roles.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                Roles & Permissions
            </a>
            @endif


            <a href="{{ route('admin.announcements.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.announcements.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.announcements.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                </svg>
                Announcements
            </a>

            <!-- Config Section -->
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Account & Settings</h3>
            </div>

            <a href="{{ route('tenant.subscription.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('tenant.subscription.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('tenant.subscription.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                My Subscription
            </a>
            


            <!-- Settings Dropdown -->
            <div x-data="{ open: {{ request()->is('admin/settings*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                        class="w-full group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->is('admin/settings*') ? 'bg-indigo-600/10 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->is('admin/settings*') ? 'text-indigo-400' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="flex-1 text-left">Configuration</span>
                    <svg class="ml-2 h-4 w-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                
                <div x-show="open" x-cloak class="space-y-1 pl-11 mt-1">
                    <a href="{{ route('admin.settings.index', ['tenant' => $tenant->slug]) }}" 
                       class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.settings.index') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        General Settings
                    </a>
                    
                    @if($tenant->hasFeature('custom_domain'))
                    <a href="{{ route('admin.settings.domain', ['tenant' => $tenant->slug]) }}" 
                       class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.settings.domain') ? 'text-white bg-slate-800' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        Custom Domain
                    </a>
                    @endif
                </div>
            </div>

            <!-- Support Section -->
            <div class="pt-6 pb-2">
                <h3 class="px-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Help & Support</h3>
            </div>

            <a href="{{ route('admin.support.index', ['tenant' => $tenant->slug]) }}"
               class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.support.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('admin.support.*') ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Support Tickets
            </a>
        </nav>
    </div>

    <!-- Bottom Actions -->
    <div class="p-4 border-t border-slate-800 bg-slate-950 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} {{ $tenant->name }}
    </div>
</div>
