@extends('admin.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Warehouse</h2>
        <a href="{{ route('admin.warehouses.index') }}" class="text-gray-600 hover:text-gray-900">← Back</a>
    </div>

    <form action="{{ route('admin.warehouses.update', $warehouse) }}" method="POST" class="bg-white rounded-lg shadow p-8">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse Name *</label>
                    <input type="text" name="name" value="{{ old('name', $warehouse->name) }}" required
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                    <input type="text" name="code" value="{{ old('code', $warehouse->code) }}" required
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Unique warehouse identifier</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('address', $warehouse->address) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city', $warehouse->city) }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $warehouse->phone) }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $warehouse->email) }}"
                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Manager</label>
                    <select name="manager_id" class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id', $warehouse->manager_id) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}
                        class="rounded border-2 border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2">
                    <span class="text-sm font-medium text-gray-700">Active</span>
                </label>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.warehouses.index') }}" class="px-6 py-2 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Update Warehouse
            </button>
        </div>
    </form>
</div>
@endsection
