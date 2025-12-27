@extends('admin.layout')

@section('title', 'Add Customer')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.customers.index', $tenant->slug) }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Customers</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Add New Customer</h1>
    </div>

    <form action="{{ route('admin.customers.store', $tenant->slug) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password *</label>
                <input type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('password')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.customers.index', $tenant->slug) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Create Customer</button>
        </div>
    </form>
</div>
@endsection
