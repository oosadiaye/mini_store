@extends('admin.layout')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Add New Account</h3>
        </div>
        
        <form action="{{ route('admin.accounts.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Code</label>
                <input type="text" name="account_code" value="{{ old('account_code', $copyData['account_code'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                <input type="text" name="account_name" value="{{ old('account_name', $copyData['account_name'] ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Account (Optional)</label>
                    <select name="parent_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">None (Root)</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ (old('parent_id', $copyData['parent_id'] ?? '') == $parent->id) ? 'selected' : '' }}>{{ $parent->account_code }} - {{ $parent->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Type</label>
                    <select name="account_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach(['asset', 'liability', 'equity', 'revenue', 'expense'] as $type)
                            <option value="{{ $type }}" {{ (old('account_type', $copyData['account_type'] ?? '') == $type) ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Subledger / Control Account</label>
                <select name="sub_ledger_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">None</option>
                    <option value="customer" {{ (old('sub_ledger_type') == 'customer') ? 'selected' : '' }}>Customer (AR)</option>
                    <option value="supplier" {{ (old('sub_ledger_type') == 'supplier') ? 'selected' : '' }}>Supplier (AP)</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">If set, manual entries to this account will require selecting a specific Customer or Supplier.</p>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-indigo-700 shadow-sm">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
