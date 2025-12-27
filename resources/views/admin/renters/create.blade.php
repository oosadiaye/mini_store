@extends('admin.layout')

@section('title', 'Add New Renter')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.renters.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Renters</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Add New Renter</h1>
    </div>

    <form action="{{ route('admin.renters.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div class="md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('phone')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">ID Number</label>
                <input type="text" name="id_number" value="{{ old('id_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('id_number')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address') }}</textarea>
                @error('address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <!-- Contract Details -->
            <div class="md:col-span-2 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Details</h3>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contract Start Date</label>
                <input type="date" name="contract_start_date" value="{{ old('contract_start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('contract_start_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contract End Date</label>
                <input type="date" name="contract_end_date" value="{{ old('contract_end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('contract_end_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Rental Amount *</label>
                <input type="number" step="0.01" name="rental_amount" value="{{ old('rental_amount') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('rental_amount')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Payment Frequency *</label>
                <select name="payment_frequency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="monthly" {{ old('payment_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="weekly" {{ old('payment_frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="quarterly" {{ old('payment_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="yearly" {{ old('payment_frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
                @error('payment_frequency')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Security Deposit</label>
                <input type="number" step="0.01" name="security_deposit" value="{{ old('security_deposit', 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('security_deposit')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                @error('notes')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('admin.renters.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Create Renter</button>
        </div>
    </form>
</div>
@endsection
