@extends('admin.layout')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-2xl font-black text-gray-900">Create Tax Code</h3>
            <p class="text-sm text-gray-500 mt-1">GL accounts will be auto-generated based on tax type</p>
        </div>

        <form action="{{ route('admin.tax-codes.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tax Code *</label>
                    <input type="text" name="code" value="{{ old('code') }}" required 
                           class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="e.g., VAT_10">
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tax Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="e.g., VAT 10%">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rate -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tax Rate (%) *</label>
                    <input type="number" name="rate" value="{{ old('rate') }}" step="0.01" min="0" max="100" required 
                           class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                           placeholder="e.g., 10.00">
                    @error('rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tax Type *</label>
                    <select name="type" required class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                        <option value="both" {{ old('type') === 'both' ? 'selected' : '' }}>Both (Sales & Purchase)</option>
                        <option value="sales" {{ old('type') === 'sales' ? 'selected' : '' }}>Sales Only</option>
                        <option value="purchase" {{ old('type') === 'purchase' ? 'selected' : '' }}>Purchase Only</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" 
                          class="w-full rounded-xl border-2 border-gray-300 px-4 py-3 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                          placeholder="Optional description">{{ old('description') }}</textarea>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked 
                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-blue-800">
                    <strong>Auto-Generated GL Accounts:</strong><br>
                    • Sales Tax: Account will be created in 2100 series (Sales Tax Payable - Liability)<br>
                    • Purchase Tax: Account will be created in 1300 series (Input Tax Receivable - Asset)
                </p>
            </div>

            <div class="flex justify-end gap-4 pt-6 border-t">
                <a href="{{ route('admin.tax-codes.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 shadow-lg">
                    Create Tax Code
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
