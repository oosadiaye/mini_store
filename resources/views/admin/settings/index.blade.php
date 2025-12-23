@extends('admin.layout')

@section('content')
<div x-data="{ 
    activeTab: '{{ request('tab', 'general') }}',
    showPaymentModal: false 
}">
    
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Settings</h2>
            <p class="text-gray-600">Manage your store configuration.</p>
        </div>
        <button type="button" onclick="document.getElementById('settings-submit-btn').click()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition shadow-md">
            Save Changes
        </button>
    </div>
    
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="show = false">
            <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
        </span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="mb-6 border-b border-gray-200 overflow-x-auto">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                General
            </button>
            <button @click="activeTab = 'branding'" :class="activeTab === 'branding' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Branding
            </button>
            <button @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Payments
            </button>
            <button @click="activeTab = 'integrations'" :class="activeTab === 'integrations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Integrations
            </button>
            <button @click="activeTab = 'tax'" :class="activeTab === 'tax' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Tax
            </button>
            <button @click="activeTab = 'pwa'" :class="activeTab === 'pwa' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                PWA
            </button>
        </nav>
    </div>

    <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-8">
        @csrf
        <button type="submit" id="settings-submit-btn" class="hidden">Submit</button>
        
        <!-- General Tab -->
        <div x-show="activeTab === 'general'" class="space-y-6">
            <!-- Store Identity -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Store Identity</h3>
                    <p class="text-sm text-gray-500">Basic information about your store.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="store_name" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Store Name</label>
                        <input type="text" name="store_name" id="store_name" value="{{ old('store_name', $tenant->name) }}" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                             <label for="currency_code" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Currency Code</label>
                             <input type="text" name="currency_code" id="currency_code" value="{{ old('currency_code', $settings['currency_code'] ?? 'NGN') }}" placeholder="e.g. NGN" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all uppercase">
                        </div>
                         <div>
                             <label for="currency_symbol" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Symbol</label>
                             <input type="text" name="currency_symbol" id="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '₦') }}" placeholder="e.g. ₦" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Contact Information</h3>
                    <p class="text-sm text-gray-500">Displayed on Invoices and Purchase Orders.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="company_address" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Company Address</label>
                        <textarea name="company_address" id="company_address" rows="3" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="company_email" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Company Email</label>
                        <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                    </div>
                    <div>
                        <label for="company_phone" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Company Phone</label>
                        <input type="text" name="company_phone" id="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                    </div>
                </div>
            </div>

            <!-- Document Defaults -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Document Defaults</h3>
                    <p class="text-sm text-gray-500">Set default prefixes for your documents.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="po_prefix" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">PO Prefix</label>
                        <input type="text" name="po_prefix" id="po_prefix" value="{{ old('po_prefix', $settings['po_prefix'] ?? 'PO-') }}" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                    </div>
                    <div>
                        <label for="invoice_prefix" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" id="invoice_prefix" value="{{ old('invoice_prefix', $settings['invoice_prefix'] ?? 'INV-') }}" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                    </div>
                </div>
            </div>

            <!-- Shipping Configuration -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Shipping Configuration</h3>
                    <p class="text-sm text-gray-500">Configure default shipping rates.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shipping_cost" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Flat Rate Shipping Cost</label>
                        <input type="number" name="shipping_cost" id="shipping_cost" step="0.01" value="{{ old('shipping_cost', $settings['shipping_cost'] ?? '0') }}" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                    </div>
                    <div>
                        <label for="free_shipping_threshold" class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Free Shipping Threshold</label>
                        <input type="number" name="free_shipping_threshold" id="free_shipping_threshold" step="0.01" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '0') }}" class="block w-full rounded-xl border-3 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        <p class="text-xs text-gray-500 mt-1">Orders above this amount will have free shipping. Set to 0 to disable.</p>
                    </div>
                </div>
            </div>

             <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">White Label & Features</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="hide_powered_by" name="hide_powered_by" type="checkbox" value="1" {{ ($settings['hide_powered_by'] ?? false) ? 'checked' : '' }} class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-offset-0">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="hide_powered_by" class="font-bold text-gray-700">Remove "Powered by" Branding</label>
                            <p class="text-gray-500">Hide the platform branding from the footer.</p>
                        </div>
                    </div>

                    <div class="flex items-start border-t border-gray-100 pt-4">
                        <div class="flex items-center h-5">
                            <input id="guest_checkout" name="guest_checkout" type="checkbox" value="1" {{ ($settings['guest_checkout'] ?? true) ? 'checked' : '' }} class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-offset-0">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="guest_checkout" class="font-bold text-gray-700">Enable Guest Checkout</label>
                            <p class="text-gray-500">Allow customers to purchase without creating an account.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branding Tab -->
        <div x-show="activeTab === 'branding'" class="space-y-6" style="display: none;">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <!-- Logo -->
                <div class="mb-6" x-data="{ logoPreview: '{{ isset($settings['logo']) ? route('tenant.media', ['path' => $settings['logo']]) . '?v=' . time() : '' }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store Logo</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                            <template x-if="logoPreview">
                                <img :src="logoPreview" alt="Store Logo" class="max-w-full max-h-full object-contain">
                            </template>
                            <template x-if="!logoPreview">
                                <span class="text-gray-400 text-xs">No Logo</span>
                            </template>
                        </div>
                        <input type="file" name="logo" @change="const file = $event.target.files[0]; const reader = new FileReader(); reader.onload = (e) => { logoPreview = e.target.result; }; reader.readAsDataURL(file);" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                    </div>
                </div>

                <!-- Favicon -->
                <div class="mb-6" x-data="{ faviconPreview: '{{ isset($settings['favicon']) ? route('tenant.media', ['path' => $settings['favicon']]) . '?v=' . time() : '' }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 w-12 h-12 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                            <template x-if="faviconPreview">
                                <img :src="faviconPreview" alt="Favicon" class="w-8 h-8 object-contain">
                            </template>
                            <template x-if="!faviconPreview">
                                <span class="text-gray-400 text-xs">None</span>
                            </template>
                        </div>
                        <input type="file" name="favicon" @change="const file = $event.target.files[0]; const reader = new FileReader(); reader.onload = (e) => { faviconPreview = e.target.result; }; reader.readAsDataURL(file);" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                    </div>
                </div>

                <!-- Hero Banner -->
                <div x-data="{ bannerPreview: '{{ isset($settings['hero_banner']) ? route('tenant.media', ['path' => $settings['hero_banner']]) . '?v=' . time() : '' }}' }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Hero Banner 
                        <span class="text-gray-500 font-normal ml-2">(Recommended: 1200x600px)</span>
                    </label>
                    <div class="space-y-4">
                        <div class="w-full aspect-[2/1] border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden relative">
                            <template x-if="bannerPreview">
                                <img :src="bannerPreview" alt="Hero Banner" class="absolute inset-0 w-full h-full object-cover">
                            </template>
                            <template x-if="!bannerPreview">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="mt-1 text-sm text-gray-500">No banner uploaded</p>
                                </div>
                            </template>
                        </div>
                        <input type="file" name="hero_banner" @change="const file = $event.target.files[0]; const reader = new FileReader(); reader.onload = (e) => { bannerPreview = e.target.result; }; reader.readAsDataURL(file);" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                    </div>
                </div>

                <!-- Hero Banner Text Content -->
                <div class="mt-6 space-y-4 border-t pt-6">
                    <h3 class="text-sm font-semibold text-gray-900">Hero Banner Text</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Badge Text</label>
                        <input type="text" name="hero_badge" value="{{ old('hero_badge', $settings['hero_badge'] ?? 'New Arrival') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g., New Arrival, Hot Deal">
                        <p class="mt-1 text-xs text-gray-500">Small badge text above the main heading</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Main Heading</label>
                        <input type="text" name="hero_heading" value="{{ old('hero_heading', $settings['hero_heading'] ?? 'Next Gen Gaming Rigs') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g., Premium Electronics">
                        <p class="mt-1 text-xs text-gray-500">Main heading text (use | for line break, e.g., "Next Gen|Gaming Rigs")</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="hero_description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g., Experience ray tracing and ultra-performance with our latest custom builds.">{{ old('hero_description', $settings['hero_description'] ?? 'Experience ray tracing and ultra-performance with our latest custom builds.') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Short description below the heading</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                        <input type="text" name="hero_button_text" value="{{ old('hero_button_text', $settings['hero_button_text'] ?? 'Shop Now') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g., Shop Now, View Products">
                        <p class="mt-1 text-xs text-gray-500">Call-to-action button text</p>
                    </div>
                </div>
            </div>
            
            <!-- Side Banners -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Side Banners (Electro Retail Theme)</h3>
                    <p class="text-xs text-gray-500 mt-1">These appear on the right side of the hero section</p>
                </div>
                
                <!-- Side Banner 1 -->
                <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                    <h4 class="text-sm font-medium text-gray-700">Side Banner 1 (Top)</h4>
                    
                    <div x-data="{ sideBanner1Preview: '{{ isset($settings['side_banner_1']) ? route('tenant.media', ['path' => $settings['side_banner_1']]) . '?v=' . time() : '' }}' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                        <div class="w-full h-40 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden relative mb-2">
                            <template x-if="sideBanner1Preview">
                                <img :src="sideBanner1Preview" alt="Side Banner 1" class="absolute inset-0 w-full h-full object-cover">
                            </template>
                            <template x-if="!sideBanner1Preview">
                                <span class="text-gray-400 text-xs">No image</span>
                            </template>
                        </div>
                        <input type="file" name="side_banner_1" @change="const file = $event.target.files[0]; const reader = new FileReader(); reader.onload = (e) => { sideBanner1Preview = e.target.result; }; reader.readAsDataURL(file);" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="side_banner_1_title" value="{{ old('side_banner_1_title', $settings['side_banner_1_title'] ?? 'Headphones') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Text</label>
                        <input type="text" name="side_banner_1_link_text" value="{{ old('side_banner_1_link_text', $settings['side_banner_1_link_text'] ?? 'View Deals') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
                
                <!-- Side Banner 2 -->
                <div class="border border-gray-200 rounded-lg p-4 space-y-4">
                    <h4 class="text-sm font-medium text-gray-700">Side Banner 2 (Bottom)</h4>
                    
                    <div x-data="{ sideBanner2Preview: '{{ isset($settings['side_banner_2']) ? route('tenant.media', ['path' => $settings['side_banner_2']]) . '?v=' . time() : '' }}' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                        <div class="w-full h-40 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden relative mb-2">
                            <template x-if="sideBanner2Preview">
                                <img :src="sideBanner2Preview" alt="Side Banner 2" class="absolute inset-0 w-full h-full object-cover">
                            </template>
                            <template x-if="!sideBanner2Preview">
                                <span class="text-gray-400 text-xs">No image</span>
                            </template>
                        </div>
                        <input type="file" name="side_banner_2" @change="const file = $event.target.files[0]; const reader = new FileReader(); reader.onload = (e) => { sideBanner2Preview = e.target.result; }; reader.readAsDataURL(file);" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="side_banner_2_title" value="{{ old('side_banner_2_title', $settings['side_banner_2_title'] ?? 'Cameras') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link Text</label>
                        <input type="text" name="side_banner_2_link_text" value="{{ old('side_banner_2_link_text', $settings['side_banner_2_link_text'] ?? 'View Deals') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                 <input type="color" name="primary_color" value="{{ old('primary_color', $settings['primary_color'] ?? '#4f46e5') }}" class="h-10 w-32 rounded-md border-gray-300 p-1">
            </div>
        </div>

        <!-- Payments Tab -->
        <div x-show="activeTab === 'payments'" class="space-y-6" style="display: none;">
            
            <!-- Gateways Configuration -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Payment Gateways</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach(['opay', 'moniepoint', 'paystack', 'flutterwave'] as $gw)
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between mb-4">
                                <span class="font-bold text-gray-700 uppercase">{{ $gw }}</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="gateway_{{ $gw }}_active" value="1" {{ ($settings["gateway_{$gw}_active"] ?? false) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <input type="text" name="gateway_{{ $gw }}_public_key" value="{{ $settings["gateway_{$gw}_public_key"] ?? '' }}" placeholder="Public Key" class="block w-full text-xs rounded-md border-gray-300">
                                <input type="password" name="gateway_{{ $gw }}_secret_key" value="{{ $settings["gateway_{$gw}_secret_key"] ?? '' }}" placeholder="Secret Key" class="block w-full text-xs rounded-md border-gray-300">
                                @if(in_array($gw, ['opay', 'moniepoint']))
                                    <input type="text" name="gateway_{{ $gw }}_merchant_id" value="{{ $settings["gateway_{$gw}_merchant_id"] ?? '' }}" placeholder="Merchant ID" class="block w-full text-xs rounded-md border-gray-300">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Types Section (Separate from form submission, technically, but visually here) -->
            <!-- We need to be careful not to nest forms. The modal will be outside. -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-semibold text-gray-800">Payment Types</h3>
                    <button type="button" @click="showPaymentModal = true" class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-200 text-sm font-medium">
                        + Add Payment Type
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GL Account</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gateway</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($paymentTypes as $pt)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $pt->name }}</td>
                                <td class="px-4 py-2 text-sm uppercase">{{ $pt->type }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $pt->account->account_code }} - {{ $pt->account->account_name }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $pt->require_gateway ? ($pt->gateway_provider ?? 'Linked') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-right text-sm">
                                    <!-- Use a separate form for toggle/delete to avoid conflict with main form -->
                                    <button type="button" form="delete-pt-{{ $pt->id }}" class="text-red-600 hover:text-red-900 border-none bg-transparent cursor-pointer">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Integrations Tab -->
        <div x-show="activeTab === 'integrations'" class="space-y-6" style="display: none;">
             <!-- SMTP -->
             <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">SMTP Mail Settings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mail Host</label>
                        <input type="text" name="mail_host" value="{{ old('mail_host', $settings['mail_host'] ?? '') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mail Port</label>
                        <input type="number" name="mail_port" value="{{ old('mail_port', $settings['mail_port'] ?? '587') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="mail_username" value="{{ old('mail_username', $settings['mail_username'] ?? '') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="mail_password" value="{{ old('mail_password', $settings['mail_password'] ?? '') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                        <select name="mail_encryption" class="block w-full rounded-md border-gray-300">
                            <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ ($settings['mail_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                        <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                </div>
            </div>
             <!-- Tracking Pixels -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Tracking Pixels</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel_id" value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">TikTok Pixel ID</label>
                        <input type="text" name="tiktok_pixel_id" value="{{ old('tiktok_pixel_id', $settings['tiktok_pixel_id'] ?? '') }}" class="block w-full rounded-md border-gray-300">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Tab -->
        <div x-show="activeTab === 'tax'" class="space-y-6" style="display: none;">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Tax Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Global Tax Rate (%)</label>
                        <input type="number" name="tax_rate" step="0.01" value="{{ old('tax_rate', $settings['tax_rate'] ?? '7.5') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="text-xs text-gray-500 mt-1">Default tax rate applied when enabled.</p>
                    </div>
                </div>
                
                <div class="mt-6 space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="enable_pos_tax" name="enable_pos_tax" type="checkbox" value="1" {{ ($settings['enable_pos_tax'] ?? false) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="enable_pos_tax" class="font-medium text-gray-700">Enable Tax on POS/Sales</label>
                            <p class="text-gray-500">Automatically calculate and post Sales Tax Payable (2100) for POS transactions.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="enable_purchase_tax" name="enable_purchase_tax" type="checkbox" value="1" {{ ($settings['enable_purchase_tax'] ?? false) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="enable_purchase_tax" class="font-medium text-gray-700">Enable Tax on Purchases</label>
                            <p class="text-gray-500">Calculate Input Tax on Purchase Orders and post to Input Tax Receivable (1300).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PWA Tab -->
        <div x-show="activeTab === 'pwa'" class="space-y-6" style="display: none;">
             <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">App Name</label>
                        <input type="text" name="pwa_name" value="{{ old('pwa_name', $settings['pwa_name'] ?? $tenant->name) }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Short Name</label>
                        <input type="text" name="pwa_short_name" value="{{ old('pwa_short_name', $settings['pwa_short_name'] ?? Str::slug($tenant->name)) }}" class="block w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Theme Color</label>
                        <input type="color" name="pwa_theme_color" value="{{ old('pwa_theme_color', $settings['pwa_theme_color'] ?? '#4f46e5') }}" class="h-10 w-full rounded-md border-gray-300 p-1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Background Color</label>
                        <input type="color" name="pwa_background_color" value="{{ old('pwa_background_color', $settings['pwa_background_color'] ?? '#ffffff') }}" class="h-10 w-full rounded-md border-gray-300 p-1">
                    </div>
                    <div class="md:col-span-2" x-data="{ pwaIconPreview: '{{ isset($settings['pwa_icon']) ? Storage::url($settings['pwa_icon']) . '?v=' . time() : '' }}' }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">App Icon (512x512)</label>
                        <template x-if="pwaIconPreview">
                             <img :src="pwaIconPreview" class="h-12 w-12 rounded mb-2">
                        </template>
                        <input type="file" name="pwa_icon" @change="const file = $event.target.files[0]; const reader = new FileReader(); reader.onload = (e) => { pwaIconPreview = e.target.result; }; reader.readAsDataURL(file);" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                    </div>
                </div>
            </div>
        </div>

    </form>

    <!-- Payment Type Modal -->
    <div x-show="showPaymentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showPaymentModal = false"></div>

            <div class="relative bg-white rounded-lg max-w-lg w-full p-6 shadow-xl space-y-4">
                <h3 class="text-lg font-medium text-gray-900">Add Payment Type</h3>
                
                <form action="{{ route('admin.payment-types.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                        </div>

                        <div x-data="{ type: 'cash' }">
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="type" x-model="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                            </select>

                            <div x-show="type === 'bank'" class="mt-4 space-y-3">
                                <input type="text" name="bank_name" placeholder="Bank Name" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <input type="text" name="account_number" placeholder="Account Number" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                <input type="text" name="account_name" placeholder="Account Name" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                            </div>
                        </div>

                        <div x-data="{ requireGateway: false }">
                             <div class="flex items-center mt-4">
                                <input type="checkbox" name="require_gateway" value="1" x-model="requireGateway" id="req_gw" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <label for="req_gw" class="ml-2 block text-sm text-gray-900">Link to Payment Gateway</label>
                            </div>
                            
                            <div x-show="requireGateway" class="mt-2">
                                <select name="gateway_provider" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                    <option value="">Select Provider...</option>
                                    <option value="opay">Opay</option>
                                    <option value="moniepoint">Moniepoint</option>
                                    <option value="paystack">Paystack</option>
                                    <option value="flutterwave">Flutterwave</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end space-x-3">
                        <button type="button" @click="showPaymentModal = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300">Cancel</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Forms for Deletion outside the main form -->
    @foreach($paymentTypes as $pt)
        <form id="delete-pt-{{ $pt->id }}" action="{{ route('admin.payment-types.destroy', $pt->id) }}" method="POST" onsubmit="return confirm('Delete this payment type?')">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</div>
@endsection
