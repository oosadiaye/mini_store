@extends('layouts.app')

@section('header', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <form action="{{ route('tenant.settings.update', ['tenant' => $tenant->slug]) }}" method="POST">
        @csrf
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Email Configuration</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Configure how your store sends emails.</p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6 space-y-6">
                
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="smtp_enabled" name="smtp_enabled" type="checkbox" value="1" 
                               class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                               {{ ($tenant->settings['smtp_enabled'] ?? false) ? 'checked' : '' }}
                               onchange="document.getElementById('smtp_fields').style.display = this.checked ? 'block' : 'none'">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="smtp_enabled" class="font-medium text-gray-700">Use Custom SMTP Server</label>
                        <p class="text-gray-500">If unchecked, we will use our global email server to send your notifications.</p>
                    </div>
                </div>

                <div id="smtp_fields" style="display: {{ ($tenant->settings['smtp_enabled'] ?? false) ? 'block' : 'none' }};">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                            <input type="text" name="smtp_host" value="{{ $tenant->settings['smtp_host'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                                <input type="text" name="smtp_port" value="{{ $tenant->settings['smtp_port'] ?? '587' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                                <input type="text" name="smtp_encryption" value="{{ $tenant->settings['smtp_encryption'] ?? 'tls' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="smtp_username" value="{{ $tenant->settings['smtp_username'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="smtp_password" value="{{ $tenant->settings['smtp_password'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">From Address</label>
                            <input type="email" name="smtp_from_address" value="{{ $tenant->settings['smtp_from_address'] ?? '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">
                Save Tenant Settings
            </button>
        </div>
    </form>
</div>
@endsection
