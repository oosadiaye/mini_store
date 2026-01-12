@extends('layouts.superadmin')

@section('header', 'Edit Tenant')

@section('content')
<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Tenant Details</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Update the tenant's basic information and subscription plan.
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('superadmin.tenants.update', $tenant) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white sm:p-6 space-y-6">
                        
                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Tenant Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $tenant->name) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-2 border-gray-300 rounded-md" required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-2 border-gray-300 rounded-md" required>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subdomain (Read Only) -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="slug" class="block text-sm font-medium text-gray-700">Subdomain/Slug</label>
                            <input type="text" id="slug" value="{{ $tenant->slug }}" disabled class="mt-1 block w-full shadow-sm sm:text-sm border-2 border-gray-300 rounded-md bg-gray-100 text-gray-500 cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500">Subdomains cannot be changed once created.</p>
                        </div>

                        <!-- Subscription Plan -->
                        <div class="col-span-6 sm:col-span-4">
                            <label for="plan_id" class="block text-sm font-medium text-gray-700">Subscription Plan</label>
                            <select id="plan_id" name="plan_id" class="mt-1 block w-full py-2 px-3 border-2 border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id) == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} ({{ number_format($plan->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-2 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Active Status</label>
                                <p class="text-gray-500">Inactive tenants cannot access their dashboard.</p>
                            </div>
                        </div>

                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                        <a href="{{ route('superadmin.tenants.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900 underline">Cancel</a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
