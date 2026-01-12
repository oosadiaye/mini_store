<x-app-layout :title="'WooCommerce Integration'">
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">WooCommerce Integration</h2>
            <div class="flex space-x-2">
                @if($settings['woocommerce_webhook_secret'] ?? false)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Webhooks Active
                    </span>
                @else
                    <form action="{{ route('admin.woocommerce.webhooks', ['tenant' => tenant()->slug]) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 underline">Register Webhooks</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stats Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-sm text-gray-500 uppercase font-bold tracking-wider mb-2">Sync Status</div>
                    <div class="flex items-baseline space-x-2">
                         <h3 class="text-3xl font-bold">{{ $syncedOrdersCount }}</h3>
                         <span class="text-gray-500">Orders Linked</span>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">
                        Last Sync: {{ $lastSync }}
                    </div>
                    @if($hasCredentials)
                        <div class="mt-4">
                            <form action="{{ route('admin.woocommerce.sync', ['tenant' => tenant()->slug]) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Sync Now
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Form -->
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Connection Settings</h3>
                    
                    <form action="{{ route('admin.woocommerce.settings', ['tenant' => tenant()->slug]) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- Enabled Toggle -->
                            <div class="sm:col-span-6 flex items-center">
                                <input type="checkbox" name="woocommerce_enabled" id="woocommerce_enabled" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ ($settings['woocommerce_enabled'] ?? false) ? 'checked' : '' }}>
                                <label for="woocommerce_enabled" class="ml-2 block text-sm font-medium text-gray-700">Enable WooCommerce Integration</label>
                            </div>

                            <div class="sm:col-span-6">
                                <x-input-label for="woocommerce_url" :value="'Store URL'" />
                                <x-text-input id="woocommerce_url" class="block mt-1 w-full" type="url" name="woocommerce_url" :value="$settings['woocommerce_url'] ?? ''" placeholder="https://yourstore.com" required />
                                <p class="mt-1 text-xs text-gray-500">The base URL of your WooCommerce store (e.g. https://example.com)</p>
                            </div>

                            <div class="sm:col-span-6">
                                <x-input-label for="woocommerce_consumer_key" :value="'Consumer Key (CK)'" />
                                <x-text-input id="woocommerce_consumer_key" class="block mt-1 w-full" type="text" name="woocommerce_consumer_key" :value="$settings['woocommerce_consumer_key'] ?? ''" required />
                            </div>

                            <div class="sm:col-span-6">
                                <x-input-label for="woocommerce_consumer_secret" :value="'Consumer Secret (CS)'" />
                                <x-text-input id="woocommerce_consumer_secret" class="block mt-1 w-full" type="password" name="woocommerce_consumer_secret" :value="$settings['woocommerce_consumer_secret'] ?? ''" required />
                            </div>
                            
                            <!-- Sync Direction -->
                            <div class="sm:col-span-3">
                                <x-input-label for="woocommerce_sync_direction" :value="'Sync Direction'" />
                                <select id="woocommerce_sync_direction" name="woocommerce_sync_direction" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="import" {{ ($settings['woocommerce_sync_direction'] ?? '') == 'import' ? 'selected' : '' }}>Import Only (WC -> MiniStore)</option>
                                    <option value="export" {{ ($settings['woocommerce_sync_direction'] ?? '') == 'export' ? 'selected' : '' }}>Export Only (MiniStore -> WC)</option>
                                    <option value="both" {{ ($settings['woocommerce_sync_direction'] ?? '') == 'both' ? 'selected' : '' }}>Two-Way Sync</option>
                                </select>
                            </div>
                            
                            <!-- Sync Interval -->
                            <div class="sm:col-span-3">
                                <x-input-label for="woocommerce_sync_interval" :value="'Auto Sync Interval (Minutes)'" />
                                <select id="woocommerce_sync_interval" name="woocommerce_sync_interval" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="5" {{ ($settings['woocommerce_sync_interval'] ?? '') == 5 ? 'selected' : '' }}>Every 5 Minutes</option>
                                    <option value="10" {{ ($settings['woocommerce_sync_interval'] ?? '') == 10 ? 'selected' : '' }}>Every 10 Minutes</option>
                                    <option value="30" {{ ($settings['woocommerce_sync_interval'] ?? '') == 30 ? 'selected' : '' }}>Every 30 Minutes</option>
                                    <option value="60" {{ ($settings['woocommerce_sync_interval'] ?? '') == 60 ? 'selected' : '' }}>Every Hour</option>
                                    <option value="1440" {{ ($settings['woocommerce_sync_interval'] ?? '') == 1440 ? 'selected' : '' }}>Daily</option>
                                </select>
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Save & Test Connection') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                 <h3 class="text-lg font-medium leading-6 text-gray-900">Synced Orders</h3>
                 <a href="{{ route('admin.woocommerce.orders', ['tenant' => tenant()->slug]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">View All &rarr;</a>
            </div>
            <!-- We can include a small table partial here or just link to the main orders page -->
        </div>
    </div>
</x-app-layout>
