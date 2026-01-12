@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create Coupon</h2>
        <a href="{{ route('admin.coupons.index') }}" class="text-gray-600 hover:text-gray-900">← Back</a>
    </div>

    <form action="{{ route('admin.coupons.store') }}" method="POST" class="bg-white rounded-lg shadow p-8">
        @csrf

        <div class="space-y-6">
            <!-- Code -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code *</label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. SUMMER2024"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 uppercase font-mono">
            </div>

            <div class="grid grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                    <select name="type" required class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                </div>

                <!-- Value -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Value *</label>
                    <input type="number" name="value" value="{{ old('value') }}" required step="0.01" min="0"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <!-- Min Spend -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Spend</label>
                    <input type="number" name="min_spend" value="{{ old('min_spend') }}" step="0.01" min="0" placeholder="0.00"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Usage Limit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Usage Limit</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" placeholder="Unlimited"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-2 border-gray-300 rounded mr-2">
                <label class="text-sm font-medium text-gray-700">Active</label>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
             <a href="{{ route('admin.coupons.index') }}" class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 mr-4">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Create Coupon
            </button>
        </div>
    </form>
</div>
@endsection
