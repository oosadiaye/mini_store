@extends('superadmin.layout')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Create New Tenant</h1>
        <a href="{{ route('superadmin.tenants.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Back to Tenants</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('superadmin.tenants.store') }}" method="POST" class="p-6 space-y-8">
            @csrf

            <!-- Tenant Information -->
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Tenant Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Tenant Name (Store Name)</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="e.g. Acme Corp">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="subdomain" class="block text-sm font-medium text-gray-700">Subdomain (Slug)</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="subdomain" id="subdomain" value="{{ old('subdomain') }}" required class="flex-1 min-w-0 block w-full rounded-none rounded-l-md border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="acme">
                            <span class="inline-flex items-center px-3 rounded-r-md border-2 border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                .{{ config('app.url_base', 'mini.tryquot.com') }}
                            </span>
                        </div>
                        @error('subdomain') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="plan_id" class="block text-sm font-medium text-gray-700">Subscription Plan</label>
                    <select name="plan_id" id="plan_id" required class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ $plan->price > 0 ? $plan->currency . number_format($plan->price, 2) : 'Free' }})
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Admin User Information -->
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Admin User (Owner)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-gray-700">Admin Name</label>
                        <input type="text" name="admin_name" id="admin_name" value="{{ old('admin_name') }}" required class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="John Doe">
                        @error('admin_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700">Admin Email</label>
                        <input type="email" name="admin_email" id="admin_email" value="{{ old('admin_email') }}" required class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="admin@example.com">
                        @error('admin_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-gray-200">
                <div class="flex justify-end">
                    <a href="{{ route('superadmin.tenants.index') }}" class="bg-white py-2 px-4 border-2 border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Create Tenant
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
