@extends('layouts.superadmin')

@section('header', 'Edit Plan')

@section('content')
<div class="mb-6">
    <a href="{{ route('superadmin.plans.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Plans
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-2xl mx-auto">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Edit Plan: {{ $plan->name }}</h2>
    
    <form action="{{ route('superadmin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Plan Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}" required 
                       class="w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price (₦)</label>
                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $plan->price) }}" required 
                           class="w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-1">Duration (Days)</label>
                    <input type="number" name="duration_days" id="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" required 
                           class="w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('duration_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Trial Days -->
                <div>
                    <label for="trial_days" class="block text-sm font-medium text-gray-700 mb-1">Trial Period (Days)</label>
                    <input type="number" name="trial_days" id="trial_days" value="{{ old('trial_days', $plan->trial_days ?? 0) }}" 
                           class="w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">0 for no trial.</p>
                    @error('trial_days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Features & Limits -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">features & Limits</label>
                <div class="mb-2 p-3 bg-blue-50 text-blue-800 text-sm rounded border border-blue-100">
                    <strong>Note:</strong> Select features to enable for this plan.
                </div>
                
                <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    @php
                        $modules = [
                            // Core Features
                            'products' => [
                                'label' => 'Products Management',
                                'limit' => 'max_products',
                                'limit_label' => 'Max Products'
                            ],
                            'categories' => [
                                'label' => 'Categories',
                                'limit' => null
                            ],
                            'customers' => [
                                'label' => 'Customers Management',
                                'limit' => 'max_customers',
                                'limit_label' => 'Max Customers'
                            ],
                            'suppliers' => [
                                'label' => 'Suppliers Management',
                                'limit' => 'max_suppliers',
                                'limit_label' => 'Max Suppliers'
                            ],
                            'sales' => [
                                'label' => 'Sales Orders',
                                'limit' => 'max_sales',
                                'limit_label' => 'Max Sales Orders'
                            ],
                            'purchases' => [
                                'label' => 'Purchase Orders',
                                'limit' => 'max_purchases',
                                'limit_label' => 'Max Purchase Orders'
                            ],
                            'inventory' => [
                                'label' => 'Basic Inventory Management',
                                'limit' => null
                            ],
                            'reports_inventory' => [
                                'label' => 'Stock Reports',
                                'limit' => null
                            ],
                            'inventory_advanced' => [
                                'label' => 'Advanced Inventory (Warehouses, Transfers, Adjustments)',
                                'limit' => 'max_warehouses',
                                'limit_label' => 'Max Warehouses'
                            ],
                            'pos_retail' => [
                                'label' => 'Point of Sale (POS)',
                                'limit' => 'max_transactions',
                                'limit_label' => 'Max Monthly Transactions'
                            ],
                            'online_store' => [
                                'label' => 'Online Store (Storefront)',
                                'limit' => null
                            ],
                            'accounting_core' => [
                                'label' => 'Core Accounting (Chart of Accounts, Journals)',
                                'limit' => null
                            ],
                            'accounting_advanced' => [
                                'label' => 'Advanced Accounting (Expenses, Incomes, Reports)',
                                'limit' => null
                            ],
                            'crm' => [
                                'label' => 'CRM (Customer Relationship Management)',
                                'limit' => null
                            ],
                            'marketing' => [
                                'label' => 'Marketing (Coupons, Promotions)',
                                'limit' => null
                            ],
                            'reports_basic' => [
                                'label' => 'Basic Reports',
                                'limit' => null
                            ],
                            'reports_advanced' => [
                                'label' => 'Advanced Reports & Analytics',
                                'limit' => null
                            ],
                            'team_management' => [
                                'label' => 'Team Management (Users & Roles)',
                                'limit' => 'max_users',
                                'limit_label' => 'Max Users'
                            ],
                            'support' => [
                                'label' => 'Support Tickets',
                                'limit' => null
                            ],
                            'priority_support' => [
                                'label' => 'Priority Support',
                                'limit' => null
                            ],
                            'custom_domain' => [
                                'label' => 'Custom Domain',
                                'limit' => null
                            ],
                            'woocommerce' => [
                                'label' => 'WooCommerce Integration',
                                'limit' => null
                            ],
                        ];
                        $currentFeatures = $plan->features ?? [];
                        $currentCaps = $plan->caps ?? [];
                    @endphp

                    @foreach($modules as $key => $config)
                        <div class="border-b border-gray-200 last:border-0 pb-4 last:pb-0" 
                             x-data="{ enabled: {{ in_array($key, old('features', $currentFeatures)) ? 'true' : 'false' }} }">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="feature_{{ $key }}" name="features[]" value="{{ $key }}" type="checkbox" 
                                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-2 border-gray-300 rounded"
                                           x-model="enabled">
                                </div>
                                <div class="ml-3 flex-1">
                                    <label for="feature_{{ $key }}" class="font-medium text-gray-700 block">{{ $config['label'] }}</label>
                                    
                                    @if($config['limit'])
                                        <div x-show="enabled" class="mt-2 pl-2 border-l-2 border-indigo-200" x-transition>
                                            <label for="{{ $config['limit'] }}" class="block text-xs font-medium text-gray-500 mb-1">{{ $config['limit_label'] }}</label>
                                            <input type="number" name="caps[{{ $config['limit'] }}]" id="{{ $config['limit'] }}" 
                                                   value="{{ old("caps.{$config['limit']}", $currentCaps[$config['limit']] ?? '') }}" 
                                                   class="w-full max-w-xs rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                   placeholder="Unlimited">
                                            <p class="text-xs text-gray-400 mt-1">Leave empty for unlimited.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('features') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Active Status -->
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="is_active" name="is_active" type="checkbox" value="1" 
                           class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-2 border-gray-300 rounded"
                           {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                </div>
                <div class="ml-3 text-sm">
                    <label for="is_active" class="font-medium text-gray-700">Active</label>
                    <p class="text-gray-500">Enable this plan for new subscriptions.</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">
                Update Plan
            </button>
        </div>
    </form>
</div>
@endsection
