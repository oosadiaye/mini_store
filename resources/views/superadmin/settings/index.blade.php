@extends('layouts.superadmin')

@section('header', 'Global Settings')

@section('content')
<div x-data="{ activeTab: 'general' }">
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
        <button @click="activeTab = 'general'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'general', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'general' }" class="py-2 px-4 border-b-2 font-medium text-sm transition">
            General
        </button>
        <button @click="activeTab = 'smtp'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'smtp', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'smtp' }" class="py-2 px-4 border-b-2 font-medium text-sm transition">
            SMTP / Email
        </button>
        <button @click="activeTab = 'templates'" :class="{ 'border-blue-500 text-blue-600': activeTab === 'templates', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'templates' }" class="py-2 px-4 border-b-2 font-medium text-sm transition">
            Templates
        </button>
    </div>

    <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- General Tab -->
        <div x-show="activeTab === 'general'" class="space-y-6 max-w-2xl">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Application Details</h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Application Name (System)</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand Name (Public)</label>
                        <input type="text" name="brand_name" value="{{ $settings['brand_name'] ?? 'ERP' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-slate-500 mt-1">Displayed on Splash Screen and Sidebar.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Currency</label>
                        <input type="text" name="currency" value="{{ $settings['currency'] ?? 'NGN' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                        <input type="text" name="timezone" value="{{ $settings['timezone'] ?? 'UTC' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand Logo</label>
                        <input type="file" name="brand_logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if(isset($settings['brand_logo']))
                            <div class="mt-2">
                                <img src="{{ Storage::url($settings['brand_logo']) }}" alt="Current Logo" class="h-12 w-auto border rounded p-1">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Brand Favicon</label>
                        <input type="file" name="brand_favicon" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                        <p class="text-xs text-slate-500 mt-1">Upload a favicon (ICO, PNG, 32x32 recommended).</p>
                        @if(isset($settings['brand_favicon']))
                            <div class="mt-2">
                                <img src="{{ Storage::url($settings['brand_favicon']) }}" alt="Current Favicon" class="h-8 w-8 border rounded p-1">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SMTP Tab -->
        <div x-show="activeTab === 'smtp'" class="space-y-6 max-w-2xl" style="display: none;">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Global SMTP Settings</h3>
                <p class="text-sm text-gray-500 mb-4">These settings will be used as the default mailer for all tenants unless they configure their own.</p>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                        <input type="text" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="smtp.mailtrap.io">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                            <input type="text" name="smtp_port" value="{{ $settings['smtp_port'] ?? '587' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                            <input type="text" name="smtp_encryption" value="{{ $settings['smtp_encryption'] ?? 'tls' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                        <input type="email" name="smtp_from_address" value="{{ $settings['smtp_from_address'] ?? 'noreply@example.com' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input type="text" name="smtp_from_name" value="{{ $settings['smtp_from_name'] ?? '${APP_NAME}' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="pt-6 border-t border-gray-200">
                        <h4 class="text-md font-medium text-gray-900 mb-2">Test Configuration</h4>
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Send Test Email To</label>
                                <input type="email" name="test_email" value="{{ auth()->user()->email }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" form="test-email-form">
                            </div>
                            <button type="submit" form="test-email-form" class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-md transition self-end">
                                Send Test Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden Form for Test Email -->


        <!-- Templates Tab -->
        <div x-show="activeTab === 'templates'" class="space-y-6 max-w-4xl" style="display: none;">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Welcome Email Template</h3>
                <p class="text-sm text-gray-500 mb-4">Customize the email sent to new tenants upon signup. Available placeholders: <code>@{{ name }}</code>, <code>@{{ email }}</code>, <code>@{{ password }}</code>, <code>@{{ login_url }}</code>, <code>@{{ store_name }}</code>.</p>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Banner Image</label>
                        <input type="file" name="email_banner" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if(isset($settings['email_banner']))
                            <div class="mt-2 p-2 bg-gray-100 rounded border border-gray-300 inline-block">
                                <img src="{{ Storage::url($settings['email_banner']) }}" alt="Email Banner" class="h-32 object-contain">
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" name="welcome_email_subject" value="{{ $settings['welcome_email_subject'] ?? 'Welcome to Your New Store!' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Body Content (HTML allowed)</label>
                        @php
                            $defaultTemplate = '<h2>Hello {{ name }},</h2><p>Congratulations! Your store <strong>{{ store_name }}</strong> is ready.</p><p>You can login here: <a href="{{ login_url }}">{{ login_url }}</a></p><p>Username: {{ email }}<br>Password: {{ password }}</p><p>Thanks,<br>' . config('app.name') . ' Team</p>';
                        @endphp
                        <textarea name="welcome_email_body" rows="15" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm">{{ $settings['welcome_email_body'] ?? $defaultTemplate }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">
                Save Settings
            </button>
        </div>
    </form>
    
    <!-- Hidden Form for Test Email -->
    <form id="test-email-form" action="{{ route('superadmin.settings.test-email') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
@endsection
