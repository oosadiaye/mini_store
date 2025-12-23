@extends('layouts.superadmin')

@section('header', 'Edit Plan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('superadmin.plans.index') }}" class="text-gray-500 hover:text-gray-700 text-sm flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Plans
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('superadmin.plans.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Price & Currency -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Price</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $plan->price) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $plan->currency) }}" required maxlength="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 uppercase">
                    </div>
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Duration (Months)</label>
                    <input type="number" name="duration_months" value="{{ old('duration_months', $plan->duration_months) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                 <!-- Features -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Features</label>
                    <textarea name="features" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="One feature per line">{{ old('features', is_array($plan->features) ? implode("\n", $plan->features) : '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Enter each feature on a new line.</p>
                </div>

                 <!-- Options -->
                 <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label class="ml-2 block text-sm text-gray-900">Active</label>
                    </div>
                    <div>
                        <label class="inline-block text-sm font-medium text-gray-700 mr-2">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}" class="inline-block w-20 rounded-md border-gray-300 shadow-sm text-sm p-1">
                    </div>
                 </div>

                 <div class="pt-4 border-t border-gray-100 flex justify-end">
                     <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition shadow-sm font-medium">Update Plan</button>
                 </div>
            </div>
        </form>
    </div>
</div>
@endsection
