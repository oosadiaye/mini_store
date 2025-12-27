@extends('layouts.superadmin')

@section('header', 'Payment Gateways')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <p class="text-gray-600 mb-6">Configure payment gateways for tenant subscription payments. Enable gateways and add API credentials below.</p>

        @foreach($gateways as $gateway)
            <div class="mb-8 pb-8 border-b border-gray-200 last:border-0">
                <form action="{{ route('superadmin.payment-gateways.update', $gateway) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $gateway->display_name }}</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $gateway->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-700">{{ $gateway->is_active ? 'Active' : 'Inactive' }}</span>
                            </label>
                        </div>
                    </div>

                    @if($gateway->name === 'paystack')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                                <input type="text" name="config[public_key]" value="{{ $gateway->getConfigValue('public_key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="pk_test_...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                                <input type="password" name="config[secret_key]" value="{{ $gateway->getConfigValue('secret_key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="sk_test_...">
                            </div>
                        </div>
                    @elseif($gateway->name === 'flutterwave')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                                <input type="text" name="config[public_key]" value="{{ $gateway->getConfigValue('public_key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="FLWPUBK_TEST-...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                                <input type="password" name="config[secret_key]" value="{{ $gateway->getConfigValue('secret_key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="FLWSECK_TEST-...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Encryption Key</label>
                                <input type="password" name="config[encryption_key]" value="{{ $gateway->getConfigValue('encryption_key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    @elseif($gateway->name === 'opay')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Merchant ID</label>
                                <input type="text" name="config[merchant_id]" value="{{ $gateway->getConfigValue('merchant_id') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Public Key</label>
                                <input type="text" name="config[public_key]" value="{{ $gateway->getConfigValue('public_key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                                <input type="password" name="config[secret_key]" value="{{ $gateway->getConfigValue('secret_key') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    @elseif($gateway->name === 'bank_transfer')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                <input type="text" name="config[bank_name]" value="{{ $gateway->getConfigValue('bank_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. First Bank">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                                <input type="text" name="config[account_name]" value="{{ $gateway->getConfigValue('account_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. Mini Store Service">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                <input type="text" name="config[account_number]" value="{{ $gateway->getConfigValue('account_number') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. 1234567890">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instructions (Optional)</label>
                                <textarea name="config[instructions]" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. Please include your Tenant Name as the transfer description.">{{ $gateway->getConfigValue('instructions') }}</textarea>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">
                            Save {{ $gateway->display_name }} Settings
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
