@extends('layouts.superadmin')

@section('header', 'Global Settings')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex" aria-label="Tabs" x-data="{ tab: 'general' }">
            </nav>
    </div>
    
    <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div x-data="{ activeTab: 'mail' }">
            <!-- Tabs Header -->
            <div class="border-b border-gray-200 px-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click.prevent="activeTab = 'mail'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'mail', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'mail'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Email (SMTP)
                    </button>
                    <button @click.prevent="activeTab = 'payment'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'payment', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'payment'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Payment Gateways
                    </button>
                    <button @click.prevent="activeTab = 'branding'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'branding', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'branding'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Branding & Currency
                    </button>
                    <button @click.prevent="activeTab = 'cookie'" :class="{'border-indigo-500 text-indigo-600': activeTab === 'cookie', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'cookie'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                        Cookies & Compliance
                    </button>
                </nav>
            </div>

            <div class="p-6">
                <!-- Mail Tab -->
                <div x-show="activeTab === 'mail'" class="space-y-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">SMTP Settings</h3>
                    <p class="text-sm text-gray-500">Configure the email server used for system notifications.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mail Host</label>
                            <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mail Port</label>
                            <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="mail_password" value="{{ $settings['mail_password'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">Encryption</label>
                            <select name="mail_encryption" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="tls" {{ ($settings['mail_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">From Address</label>
                            <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700">From Name</label>
                            <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Payment Tab -->
                <div x-show="activeTab === 'payment'" class="space-y-6" style="display: none;">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Payment Gateways</h3>
                    <p class="text-sm text-gray-500">Configure API keys for billings.</p>
                    
                    <div class="border-t pt-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">OPay</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Public Key</label>
                                <input type="text" name="gateway_opay_public" value="{{ $settings['gateway_opay_public'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Secret Key</label>
                                <input type="password" name="gateway_opay_secret" value="{{ $settings['gateway_opay_secret'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Paystack</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Public Key</label>
                                <input type="text" name="gateway_paystack_public" value="{{ $settings['gateway_paystack_public'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Secret Key</label>
                                <input type="password" name="gateway_paystack_secret" value="{{ $settings['gateway_paystack_secret'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Flutterwave</h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Public Key</label>
                                <input type="text" name="gateway_flutterwave_public" value="{{ $settings['gateway_flutterwave_public'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Secret Key</label>
                                <input type="password" name="gateway_flutterwave_secret" value="{{ $settings['gateway_flutterwave_secret'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branding Tab -->
                <div x-show="activeTab === 'branding'" class="space-y-6" style="display: none;">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Branding & Currency</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Platform Name</label>
                            <input type="text" name="brand_name" value="{{ $settings['brand_name'] ?? 'Finwize' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Primary Color</label>
                            <input type="color" name="brand_primary_color" value="{{ $settings['brand_primary_color'] ?? '#4f46e5' }}" class="mt-1 h-10 w-full rounded-md border-gray-300 shadow-sm p-1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Default Currency Code</label>
                            <input type="text" name="currency_code" value="{{ $settings['currency_code'] ?? 'USD' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Currency Symbol</label>
                            <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '$' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="col-span-2">
                             <label class="block text-sm font-medium text-gray-700">Platform Logo</label>
                             @if(isset($settings['brand_logo']))
                                <img src="{{ Storage::url($settings['brand_logo']) }}" class="h-16 w-auto my-2 rounded">
                             @endif
                             <input type="file" name="brand_logo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>
                </div>

                <!-- Cookies Tab -->
                <div x-show="activeTab === 'cookie'" class="space-y-6" style="display: none;">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Cookie Consent</h3>
                    <div>
                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input name="cookie_consent_enabled" value="1" type="checkbox" {{ ($settings['cookie_consent_enabled'] ?? '') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">Enable Cookie Consent Banner</label>
                                <p class="text-gray-500">Show a banner to users asking for cookie consent.</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Consent Message</label>
                        <textarea name="cookie_consent_message" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $settings['cookie_consent_message'] ?? 'We use cookies to improve your experience. By continuing to visit this site you agree to our use of cookies.' }}</textarea>
                    </div>
                </div>

            </div>

            <!-- Footer Keys -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="submit" class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
