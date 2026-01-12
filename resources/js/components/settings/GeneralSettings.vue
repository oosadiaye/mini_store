<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Settings</h2>
                <p class="text-gray-600">Manage your store configuration.</p>
            </div>
            <button @click="saveSettings" :disabled="processing" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition shadow-md disabled:opacity-50 flex items-center gap-2">
                <span v-if="processing" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                {{ processing ? 'Saving...' : 'Save Changes' }}
            </button>
        </div>

        <!-- Notifications -->
        <div v-if="successMessage" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 relative">
            {{ successMessage }}
            <button @click="successMessage = ''" class="absolute top-2 right-2 text-green-500 hover:text-green-700">&times;</button>
        </div>
        <div v-if="errorMessage" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 relative">
             <ul v-if="typeof errorMessage === 'object'" class="list-disc pl-5">
                <li v-for="(err, key) in errorMessage" :key="key">{{ err }}</li>
             </ul>
             <span v-else>{{ errorMessage }}</span>
             <button @click="errorMessage = ''" class="absolute top-2 right-2 text-red-500 hover:text-red-700">&times;</button>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="-mb-px flex space-x-8">
                <button v-for="tab in tabs" :key="tab.id" 
                    @click="activeTab = tab.id"
                    :class="activeTab === tab.id ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    {{ tab.name }}
                </button>
            </nav>
        </div>

        <!-- Content -->
        <div class="mt-6">
             <!-- General Tab -->
             <div v-show="activeTab === 'general'" class="space-y-6">
                <!-- Store Identity -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Store Identity</h3>
                        <p class="text-sm text-gray-500">Basic information about your store.</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Store Name</label>
                            <input v-model="form.store_name" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Currency Code</label>
                                <input v-model="form.currency_code" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all uppercase">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Symbol</label>
                                <input v-model="form.currency_symbol" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
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
                             <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Company Address</label>
                            <textarea v-model="form.company_address" rows="3" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all"></textarea>
                        </div>
                        <div>
                             <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Company Email</label>
                            <input v-model="form.company_email" type="email" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        </div>
                         <div>
                             <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Company Phone</label>
                            <input v-model="form.company_phone" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
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
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">PO Prefix</label>
                            <input v-model="form.po_prefix" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Invoice Prefix</label>
                            <input v-model="form.invoice_prefix" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
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
                             <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Flat Rate Shipping Cost</label>
                            <input v-model="form.shipping_cost" type="number" step="0.01" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        </div>
                        <div>
                             <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Free Shipping Threshold</label>
                            <input v-model="form.free_shipping_threshold" type="number" step="0.01" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                            <p class="text-xs text-gray-500 mt-1">Orders above this amount will have free shipping. Set to 0 to disable.</p>
                        </div>
                    </div>
                 </div>

                 <!-- White Label -->
                  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                     <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">White Label & Features</h3>
                    </div>
                     <div class="p-6 space-y-4">
                         <div class="flex items-start">
                             <div class="flex items-center h-5">
                                <input v-model="form.hide_powered_by" id="hide_powered_by" type="checkbox" class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-offset-0">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="hide_powered_by" class="font-bold text-gray-700">Remove "Powered by" Branding</label>
                                <p class="text-gray-500">Hide the platform branding from the footer.</p>
                            </div>
                         </div>
                         <div class="flex items-start border-t border-gray-100 pt-4">
                            <div class="flex items-center h-5">
                                <input v-model="form.guest_checkout" id="guest_checkout" type="checkbox" class="focus:ring-indigo-500 h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-offset-0">
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
             <div v-show="activeTab === 'branding'" class="space-y-6">
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <!-- Logo -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Store Logo</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                <img v-if="logoPreview" :src="logoPreview" alt="Store Logo" class="max-w-full max-h-full object-contain">
                                <span v-else class="text-gray-400 text-xs">No Logo</span>
                            </div>
                            <input type="file" @change="handleFileUpload($event, 'logo')" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                        </div>
                    </div>
                    <!-- Favicon -->
                     <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 border border-gray-200 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                <img v-if="faviconPreview" :src="faviconPreview" alt="Favicon" class="w-8 h-8 object-contain">
                                <span v-else class="text-gray-400 text-xs">None</span>
                            </div>
                            <input type="file" @change="handleFileUpload($event, 'favicon')" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:bg-indigo-50 file:text-indigo-700">
                        </div>
                    </div>
                 </div>
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                     <input v-model="form.primary_color" type="color" class="h-10 w-32 rounded-md border-gray-300 p-1">
                 </div>
             </div>

             <!-- Payments Tab -->
             <div v-show="activeTab === 'payments'" class="space-y-6">
                 <!-- Gateways -->
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                     <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Payment Gateways</h3>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div v-for="gw in gateways" :key="gw" class="border rounded-lg p-4 bg-gray-50">
                             <div class="flex items-center justify-between mb-4">
                                 <span class="font-bold text-gray-700 uppercase">{{ gw }}</span>
                                 <label class="relative inline-flex items-center cursor-pointer">
                                     <input type="checkbox" v-model="form['gateway_' + gw + '_active']" class="sr-only peer">
                                     <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                                 </label>
                             </div>
                             <div class="space-y-3">
                                <input v-model="form['gateway_' + gw + '_public_key']" type="text" placeholder="Public Key" class="block w-full text-xs rounded-md border-gray-300">
                                <input v-model="form['gateway_' + gw + '_secret_key']" type="password" placeholder="Secret Key" class="block w-full text-xs rounded-md border-gray-300">
                                <input v-if="['opay', 'moniepoint'].includes(gw)" v-model="form['gateway_' + gw + '_merchant_id']" type="text" placeholder="Merchant ID" class="block w-full text-xs rounded-md border-gray-300">
                             </div>
                         </div>
                     </div>
                 </div>

                 <!-- Payment Types List -->
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-semibold text-gray-800">Payment Types</h3>
                        <button type="button" @click="showPaymentModal = true" class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-200 text-sm font-medium">+ Add Payment Type</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                     <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                     <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                     <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">GL Account</th>
                                     <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gateway</th>
                                     <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Storefront</th>
                                     <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                            </thead>
                             <tbody class="divide-y divide-gray-200">
                                <tr v-for="pt in localPaymentTypes" :key="pt.id">
                                    <td class="px-4 py-2 text-sm">{{ pt.name }}</td>
                                    <td class="px-4 py-2 text-sm uppercase">{{ pt.type }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ pt.account ? (pt.account.account_code + ' - ' + pt.account.account_name) : '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ pt.require_gateway ? (pt.gateway_provider || 'Linked') : '-' }}</td>
                                    <td class="px-4 py-2 text-center text-sm">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" :checked="pt.is_active_on_storefront" @change="toggleStorefront(pt.id)" class="sr-only peer">
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none ring-0 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </td>
                                     <td class="px-4 py-2 text-right text-sm">
                                         <button @click="deletePaymentType(pt.id)" class="text-red-600 hover:text-red-900 border-none bg-transparent cursor-pointer">Delete</button>
                                     </td>
                                </tr>
                                <tr v-if="localPaymentTypes.length === 0">
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">No payment types registered.</td>
                                </tr>
                             </tbody>
                        </table>
                    </div>
                 </div>
             </div>

             <!-- Integrations Tab -->
             <div v-show="activeTab === 'integrations'" class="space-y-6">
                 <!-- SMTP -->
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">SMTP Mail Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">Mail Host</label>
                            <input v-model="form.mail_host" type="text" class="block w-full rounded-md border-gray-300">
                        </div>
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">Mail Port</label>
                            <input v-model="form.mail_port" type="number" class="block w-full rounded-md border-gray-300">
                        </div>
                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input v-model="form.mail_username" type="text" class="block w-full rounded-md border-gray-300">
                        </div>
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input v-model="form.mail_password" type="password" class="block w-full rounded-md border-gray-300">
                        </div>
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                            <select v-model="form.mail_encryption" class="block w-full rounded-md border-gray-300">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                            <input v-model="form.mail_from_address" type="email" class="block w-full rounded-md border-gray-300">
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                         <h4 class="text-md font-medium text-gray-900 mb-2">Test Configuration</h4>
                         <div class="flex items-end gap-4">
                             <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Send Test Email To</label>
                                <input v-model="testEmail" type="email" class="block w-full rounded-md border-gray-300">
                            </div>
                            <button @click="sendTestEmail" type="button" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-md transition self-end">
                                Send Test Email
                            </button>
                         </div>
                         <p class="text-xs text-gray-500 mt-2">Note: This tests your <strong>SAVED</strong> settings. Please save any changes before testing.</p>
                    </div>
                 </div>
             </div>

             <!-- Storefront Tab -->
             <div v-show="activeTab === 'storefront'" class="space-y-6">
                 <storefront-settings 
                    :initial-status="initialStorefrontStatus"
                    :has-feature="hasFeature"
                    :tenant-slug="tenantSlug"
                 ></storefront-settings>
                 
                 <!-- Cards for Editor and Wizard -->
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Visual Editor -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-indigo-100 rounded-lg text-indigo-600 mr-4">
                                <i class="fas fa-edit text-2xl"></i>
                            </div>
                             <div>
                                <h3 class="text-lg font-bold text-gray-900">Visual Editor</h3>
                                <p class="text-sm text-gray-500">Customize your storefront's look and feel.</p>
                            </div>
                        </div>
                        <div class="flex-grow">
                             <p class="text-gray-600 text-sm mb-4">Use our visual editor to change colors, fonts, layouts, and content directly on your storefront.</p>
                        </div>
                         <div class="mt-4 pt-4 border-t border-gray-100">
                             <a :href="routes.storefrontHome" target="_blank" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition">Launch Visual Editor ↗</a>
                         </div>
                    </div>
                    <!-- Wizard -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col">
                        <div class="flex items-center mb-4">
                            <div class="p-3 bg-green-100 rounded-lg text-green-600 mr-4">
                                <i class="fas fa-magic text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Setup Wizard</h3>
                                <p class="text-sm text-gray-500">Restart the store configuration wizard.</p>
                            </div>
                        </div>
                        <div class="flex-grow">
                             <p class="text-gray-600 text-sm mb-4">Re-run the step-by-step wizard to reset your industry, catalog, and basic branding settings.</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                             <a :href="routes.wizardIndex" class="block w-full text-center bg-white border-2 border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-2 px-4 rounded-lg transition">Restart Wizard</a>
                        </div>
                    </div>
                 </div>
             </div>

             <!-- SEO & Geo Tab -->
             <div v-show="activeTab === 'seo'" class="space-y-6">
                <!-- SEO Settings -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Search Engine Optimization</h3>
                            <p class="text-sm text-gray-500">Improve how your store appears in search results.</p>
                        </div>
                        <div class="flex gap-2">
                            <a :href="routes.storefrontSitemap" target="_blank" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition">Sitemap.xml ↗</a>
                            <a :href="routes.storefrontRobots" target="_blank" class="text-xs font-bold text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition">Robots.txt ↗</a>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Meta Title</label>
                                <button @click="suggestSeo('meta_title')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded transition">
                                    <i class="fas fa-magic"></i> AI Suggest
                                </button>
                            </div>
                            <input v-model="form.meta_title" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all" placeholder="Up to 60 characters recommended">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Meta Description</label>
                                <button @click="suggestSeo('meta_description')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded transition">
                                    <i class="fas fa-magic"></i> AI Suggest
                                </button>
                            </div>
                            <textarea v-model="form.meta_description" rows="3" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all" placeholder="Up to 160 characters recommended"></textarea>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Keywords</label>
                                <button @click="suggestSeo('meta_keywords')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-2 py-1 rounded transition">
                                    <i class="fas fa-magic"></i> AI Suggest
                                </button>
                            </div>
                            <input v-model="form.meta_keywords" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all" placeholder="Comma separated keywords">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Social Sharing Image (OG Image)</label>
                            <div class="flex items-center space-x-6 bg-gray-50/50 p-4 rounded-xl border-2 border-dashed border-gray-200">
                                <div class="flex-shrink-0 w-32 h-20 border border-gray-200 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden shadow-sm">
                                    <img v-if="ogImagePreview" :src="ogImagePreview" alt="OG Image" class="max-w-full max-h-full object-cover">
                                    <i v-else class="fas fa-share-alt text-gray-300 text-2xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <input type="file" @change="handleFileUpload($event, 'og_image')" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="text-[10px] text-gray-400 mt-2 uppercase font-bold tracking-widest">Recommended: 1200x630px JPG or PNG</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Geo-Targeting -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-bold text-gray-900">Geo-Targeting & Presence</h3>
                        <p class="text-sm text-gray-500">Provide geographical context for localized search.</p>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Target Country</label>
                            <input v-model="form.store_country" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Region / State</label>
                            <input v-model="form.store_region" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all" placeholder="e.g. Lagos, California">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Timezone</label>
                            <input v-model="form.store_timezone" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide mb-2">Google Maps URL</label>
                            <input v-model="form.google_maps_url" type="text" class="block w-full rounded-xl border-2 border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 py-3 text-base font-medium transition-all" placeholder="Embed or Link URL">
                        </div>
                    </div>
                </div>
             </div>

             <!-- Tax Tab -->
             <div v-show="activeTab === 'tax'" class="space-y-6">
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                     <div class="flex justify-between items-center mb-6">
                         <div>
                            <h3 class="text-lg font-semibold text-gray-800">Tax Code Management</h3>
                            <p class="text-sm text-gray-500 mt-1">Manage tax codes with auto-generated GL accounts</p>
                         </div>
                         <a :href="routes.taxCodesCreate" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">+ Add Tax Code</a>
                     </div>
                     <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
                         <p class="text-sm text-blue-800">
                            <strong>Note:</strong> Tax codes are now managed separately with automatic GL account generation.<br>
                            • Sales Tax → 2100 series (Sales Tax Payable - Liability)<br>
                            • Purchase Tax → 1300 series (Input Tax Receivable - Asset)
                         </p>
                     </div>
                      <div class="text-center py-8">
                         <i class="fas fa-money-bill-wave mx-auto h-12 w-12 text-gray-400 text-5xl"></i>
                         <h3 class="mt-2 text-sm font-medium text-gray-900">Tax Code Management</h3>
                         <p class="mt-1 text-sm text-gray-500">Click the button below to manage your tax codes</p>
                         <div class="mt-6">
                             <a :href="routes.taxCodesIndex" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                 Manage Tax Codes
                             </a>
                         </div>
                     </div>
                 </div>
             </div>
        </div>

        <!-- Payment Type Modal -->
        <common-modal :is-open="showPaymentModal" @close="showPaymentModal = false">
             <!-- Modal Header -->
             <div class="px-6 py-4 border-b border-gray-200">
                 <h3 class="text-lg font-medium text-gray-900">Add Payment Type</h3>
             </div>

             <!-- Modal Body -->
             <div class="px-6 py-4 space-y-6">
                 <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                    <input v-model="ptForm.name" type="text" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5" placeholder="e.g. Standard Delivery">
                </div>
                 <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Type</label>
                    <div class="relative">
                        <select v-model="ptForm.type" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 appearance-none">
                            <option value="cash">Cash / Physical</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>
                 <div v-if="ptForm.type === 'bank'" class="grid grid-cols-1 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                     <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Bank Name</label>
                        <input v-model="ptForm.bank_name" type="text" placeholder="e.g. GTBank" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                     </div>
                     <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Account Number</label>
                        <input v-model="ptForm.account_number" type="text" placeholder="0123456789" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                     </div>
                     <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Account Name</label>
                        <input v-model="ptForm.account_name" type="text" placeholder="Account Name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2">
                     </div>
                </div>
                 <div>
                     <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" @click="ptForm.require_gateway = !ptForm.require_gateway">
                        <div>
                             <span class="block text-sm font-medium text-gray-900">Link to Payment Gateway</span>
                             <span class="block text-xs text-gray-500">Enable online processing via a provider</span>
                        </div>
                        <div class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" :class="ptForm.require_gateway ? 'bg-indigo-600' : 'bg-gray-200'">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="ptForm.require_gateway ? 'translate-x-5' : 'translate-x-0'"></span>
                        </div>
                    </div>
                    <div v-if="ptForm.require_gateway" class="mt-3">
                        <select v-model="ptForm.gateway_provider" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5">
                            <option value="">Select Provider...</option>
                            <option value="opay">Opay</option>
                            <option value="moniepoint">Moniepoint</option>
                            <option value="paystack">Paystack</option>
                            <option value="flutterwave">Flutterwave</option>
                        </select>
                    </div>
                </div>
                <!-- Storefront Toggle -->
                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors" @click="ptForm.is_active_on_storefront = !ptForm.is_active_on_storefront">
                     <div>
                         <span class="block text-sm font-medium text-gray-900">Available on Storefront?</span>
                         <span class="block text-xs text-gray-500">Show this payment option to customers</span>
                    </div>
                    <div class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" :class="ptForm.is_active_on_storefront ? 'bg-indigo-600' : 'bg-gray-200'">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="ptForm.is_active_on_storefront ? 'translate-x-5' : 'translate-x-0'"></span>
                    </div>
                </div>
             </div>
             
             <!-- Modal Footer -->
             <div class="px-6 py-4 bg-gray-50 flex justify-end rounded-b-lg">
                <button @click="showPaymentModal = false" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300 mr-2">Cancel</button>
                <button @click="createPaymentType" :disabled="ptProcessing" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 disabled:opacity-50">
                    {{ ptProcessing ? 'Creating...' : 'Create' }}
                </button>
            </div>
        </common-modal>
    </div>
</template>


<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import StorefrontSettings from '../storefront/StorefrontSettings.vue';
import CommonModal from '../common/CommonModal.vue';
import axios from 'axios';



const props = defineProps({
    initialSettings: { type: Object, required: true, default: () => ({}) },
    tenantName: { type: String, default: '' },
    paymentTypes: { type: Array, default: () => [] },
    initialStorefrontStatus: { type: Boolean, default: false },
    hasFeature: { type: Boolean, default: false },
    tenantSlug: { type: String, default: '' },
    defaultTab: { type: String, default: 'general' },
    routes: { type: Object, required: true },
    csrfToken: String,
    currentUserEmail: String
});

// State
const activeTab = ref(props.defaultTab);
const processing = ref(false);
const successMessage = ref('');
const errorMessage = ref('');
const showPaymentModal = ref(false);
const ptProcessing = ref(false);
const localPaymentTypes = ref([...props.paymentTypes]);
const testEmail = ref(props.currentUserEmail || '');

const gateways = ['opay', 'moniepoint', 'paystack', 'flutterwave'];
const tabs = [
    { id: 'general', name: 'General' },
    { id: 'branding', name: 'Branding' },
    { id: 'payments', name: 'Payments' },
    { id: 'integrations', name: 'Integrations' },
    { id: 'storefront', name: 'Storefront & CMS' },
    { id: 'seo', name: 'SEO & Geo' },
    { id: 'tax', name: 'Tax' }
];

// Form Data - Flattened for easier v-model binding
const form = reactive({
    store_name: props.tenantName,
    currency_code: props.initialSettings.currency_code || 'NGN',
    currency_symbol: props.initialSettings.currency_symbol || '₦',
    company_address: props.initialSettings.company_address || '',
    company_email: props.initialSettings.company_email || '',
    company_phone: props.initialSettings.company_phone || '',
    po_prefix: props.initialSettings.po_prefix || 'PO-',
    invoice_prefix: props.initialSettings.invoice_prefix || 'INV-',
    shipping_cost: props.initialSettings.shipping_cost || '0',
    free_shipping_threshold: props.initialSettings.free_shipping_threshold || '0',
    hide_powered_by: !!props.initialSettings.hide_powered_by,
    guest_checkout: props.initialSettings.guest_checkout !== undefined ? !!props.initialSettings.guest_checkout : true,
    primary_color: props.initialSettings.primary_color || '#4f46e5',
    mail_host: props.initialSettings.mail_host || '',
    mail_port: props.initialSettings.mail_port || '587',
    mail_username: props.initialSettings.mail_username || '',
    mail_password: props.initialSettings.mail_password || '',
    mail_encryption: props.initialSettings.mail_encryption || 'tls',
    mail_from_address: props.initialSettings.mail_from_address || '',
    // Gateways
    gateway_opay_active: !!props.initialSettings.gateway_opay_active,
    gateway_opay_public_key: props.initialSettings.gateway_opay_public_key || '',
    gateway_opay_secret_key: props.initialSettings.gateway_opay_secret_key || '',
    gateway_opay_merchant_id: props.initialSettings.gateway_opay_merchant_id || '',
    gateway_moniepoint_active: !!props.initialSettings.gateway_moniepoint_active,
    gateway_moniepoint_public_key: props.initialSettings.gateway_moniepoint_public_key || '',
    gateway_moniepoint_secret_key: props.initialSettings.gateway_moniepoint_secret_key || '',
    gateway_moniepoint_merchant_id: props.initialSettings.gateway_moniepoint_merchant_id || '',
    gateway_paystack_active: !!props.initialSettings.gateway_paystack_active,
    gateway_paystack_public_key: props.initialSettings.gateway_paystack_public_key || '',
    gateway_paystack_secret_key: props.initialSettings.gateway_paystack_secret_key || '',
    gateway_flutterwave_active: !!props.initialSettings.gateway_flutterwave_active,
    gateway_flutterwave_public_key: props.initialSettings.gateway_flutterwave_public_key || '',
    gateway_flutterwave_secret_key: props.initialSettings.gateway_flutterwave_secret_key || '',
    // SEO
    meta_title: props.initialSettings.meta_title || '',
    meta_description: props.initialSettings.meta_description || '',
    meta_keywords: props.initialSettings.meta_keywords || '',
    // Geo
    store_country: props.initialSettings.store_country || 'Nigeria',
    store_region: props.initialSettings.store_region || '',
    store_timezone: props.initialSettings.store_timezone || 'Africa/Lagos',
    google_maps_url: props.initialSettings.google_maps_url || '',
});

// Files
const logoFile = ref(null);
const faviconFile = ref(null);
const logoPreview = ref(props.initialSettings.logo ? props.routes.media + '?path=' + props.initialSettings.logo : null);
const faviconPreview = ref(props.initialSettings.favicon ? props.routes.media + '?path=' + props.initialSettings.favicon : null);
const ogImagePreview = ref(props.initialSettings.og_image ? props.routes.media + '?path=' + props.initialSettings.og_image : null);
const ogImageFile = ref(null);

// Payment Type Form
const ptForm = reactive({
    name: '',
    type: 'cash',
    bank_name: '',
    account_number: '',
    account_name: '',
    require_gateway: false,
    gateway_provider: '',
    is_active_on_storefront: false
});

// Methods
const handleFileUpload = (event, type) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            if (type === 'logo') {
                logoPreview.value = e.target.result;
                logoFile.value = file;
            } else if (type === 'favicon') {
                faviconPreview.value = e.target.result;
                faviconFile.value = file;
            } else if (type === 'og_image') {
                ogImagePreview.value = e.target.result;
                ogImageFile.value = file;
            }
        };
        reader.readAsDataURL(file);
    }
};

const saveSettings = async () => {
    processing.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    const formData = new FormData();
    // Append simple fields
    for (const key in form) {
        if (typeof form[key] === 'boolean') {
            formData.append(key, form[key] ? '1' : '0');
        } else {
            formData.append(key, form[key] || '');
        }
    }
    // Append files
    if (logoFile.value) formData.append('logo', logoFile.value);
    if (faviconFile.value) formData.append('favicon', faviconFile.value);
    if (ogImageFile.value) formData.append('og_image', ogImageFile.value);

    // Initial files references (if we want to keep them when not updating? backend usually handles "if hasFile")
    // Backend logic: if ($request->hasFile('logo')) update. So we are good.

    try {
        await axios.post(props.routes.update, formData, {
            headers: { 'Content-Type': 'multipart/form-data' } // Axios handles this but explicit is fine
        });
        
        // Reload to reflect changes (header logo, name, etc.)
        window.location.reload();
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data && error.response.data.errors) {
            errorMessage.value = error.response.data.errors;
        } else {
            errorMessage.value = 'Failed to update settings. Please try again.';
        }
        processing.value = false;
    }
};

const createPaymentType = async () => {
    ptProcessing.value = true;
    try {
        const response = await axios.post(props.routes.paymentTypesStore, ptForm, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.data.success) {
            localPaymentTypes.value.push(response.data.payment_type);
            showPaymentModal.value = false;
            // Reset form
            ptForm.name = '';
            ptForm.type = 'cash';
            ptForm.bank_name = '';
            ptForm.account_number = '';
            ptForm.account_name = '';
            ptForm.require_gateway = false;
            ptForm.gateway_provider = '';
            successMessage.value = response.data.message;
        }
    } catch (e) {
        console.error(e);
        alert('Failed to create payment type.');
    } finally {
        ptProcessing.value = false;
    }
};

const deletePaymentType = async (id) => {
    if(!confirm('Are you sure you want to delete this payment type?')) return;
    try {
        // Construct the URL by replacing ID placeholder if needed, or if route passed is generic
        // Assuming route passed is base or we construct it.
        // To be safe, let's pass generated route map or base route.
        // Let's assume we pass the full delete url in the loop? No.
        // Easier: pass base route and append ID
        const url = props.routes.paymentTypesDestroy.replace(':id', id);
        
        const response = await axios.delete(url, {
             headers: { 'Accept': 'application/json' }
        });

        if (response.data.success) {
            localPaymentTypes.value = localPaymentTypes.value.filter(pt => pt.id !== id);
            successMessage.value = response.data.message;
        }
    } catch (e) {
         console.error(e);
         alert('Failed to delete payment type.');
    }
};

const sendTestEmail = async () => {
    if (!testEmail.value) {
        alert('Please enter an email address.');
        return;
    }
    // We send to backend. Note: backend uses SAVED settings.
    // So we should warn user or save first. The UI warns.
    try {
        const response = await axios.post(props.routes.testEmail, { test_email: testEmail.value });
        if (response.data.success) {
             alert(response.data.message);
        } else {
             alert(response.data.message || 'Email sent (check logs if not received).');
        }
       
    } catch (e) {
        console.error(e);
        const msg = e.response?.data?.message || 'Failed to send test email. Check logs or ensure settings are SAVED.';
        alert(msg);
    }
};

const toggleStorefront = async (id) => {
    try {
        const url = props.routes.paymentTypesToggleStorefront.replace(':id', id);
        const response = await axios.post(url);
        
        if (response.data.success) {
            const index = localPaymentTypes.value.findIndex(pt => pt.id === id);
            if (index !== -1) {
                localPaymentTypes.value[index].is_active_on_storefront = response.data.is_active_on_storefront;
            }
        }
    } catch (e) {
        console.error(e);
        alert('Failed to update status.');
    }
};

const suggestSeo = async (type) => {
    try {
        const response = await axios.post(props.routes.seoSuggest, { type });
        if (response.data.suggestion) {
            if (type === 'meta_title') form.meta_title = response.data.suggestion;
            if (type === 'meta_description') form.meta_description = response.data.suggestion;
            if (type === 'meta_keywords') form.meta_keywords = response.data.suggestion;
        }
    } catch (e) {
        console.error(e);
        alert('Failed to get suggestion.');
    }
};

watch(showPaymentModal, (newVal) => {
    if (newVal) {
        // Reset form when modal opens
        ptForm.name = '';
        ptForm.type = 'cash';
        ptForm.bank_name = '';
        ptForm.account_number = '';
        ptForm.account_name = '';
        ptForm.require_gateway = false;
        ptForm.gateway_provider = '';
        ptForm.is_active_on_storefront = false;
    }
});
</script>
