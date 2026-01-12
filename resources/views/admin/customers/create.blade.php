@extends('admin.layout')

@section('title', 'Add Customer')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Add New Customer</h2>
        <a href="{{ route('admin.customers.index', $tenant->slug) }}" class="text-gray-600 hover:text-gray-900">← Back to Customers</a>
    </div>

    <form action="{{ route('admin.customers.store', $tenant->slug) }}" method="POST" class="bg-white rounded-lg shadow p-8" x-data="{ isStorefront: true }" v-pre>
        @csrf
        <input type="hidden" name="is_storefront_customer" :value="isStorefront ? 1 : 0">

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div>
                 <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                 <input type="text" name="phone" value="{{ old('phone') }}"
                     class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                 @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Storefront Access</h3>
                        <p class="text-sm text-gray-500">Allow this customer to log in to the online store?</p>
                    </div>
                    <button type="button" 
                        @click="isStorefront = !isStorefront"
                        :class="isStorefront ? 'bg-indigo-600' : 'bg-gray-200'"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">Use setting</span>
                        <span aria-hidden="true" 
                            :class="isStorefront ? 'translate-x-5' : 'translate-x-0'"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-show="isStorefront" x-transition>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                    <input type="password" name="password" :required="isStorefront"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                    @error('password')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                    <input type="password" name="password_confirmation" :required="isStorefront"
                         class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            
            <hr class="border-gray-200 my-4">
            <h3 class="text-lg font-medium text-gray-900">Address Details</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
                <input type="text" name="address_line1" value="{{ old('address_line1') }}"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" value="{{ old('address_line2') }}"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city') }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State / Province</label>
                    <input type="text" name="state" value="{{ old('state') }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" value="{{ old('country') }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.customers.index', $tenant->slug) }}" class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Create Customer
            </button>
        </div>
    </form>
</div>
@endsection
