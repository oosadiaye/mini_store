@extends('admin.layout')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <!-- Header -->
    <div class="mb-8 text-center pt-5">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Record New Income</h2>
        <p class="mt-2 text-lg text-gray-600">Enter transaction details to generate a journal entry.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6">
            <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Transaction Details
            </h3>
        </div>
        
        <form action="{{ route('admin.incomes.store') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">Transaction Date</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base transition duration-200 bg-gray-50 hover:bg-white focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">Branch / Warehouse</label>
                        <div class="relative">
                            <select name="warehouse_id" class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base bg-gray-50 hover:bg-white appearance-none">
                                <option value="">Select Branch (Optional)</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">Payment Type</label>
                         <div class="relative">
                            <select name="payment_type_id" required class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base bg-gray-50 hover:bg-white appearance-none">
                                <option value="">Select Payment Method</option>
                                @foreach($paymentTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 pl-1">Selected payment type determines the asset account to be debited.</p>
                    </div>

                     <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">Amount</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-lg">{{ $tenant->data['currency_symbol'] ?? '₦' }}</span>
                            </div>
                            <input type="number" name="amount" step="0.01" required class="w-full rounded-xl border-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 pl-10 pr-4 text-lg font-bold text-gray-900 placeholder-gray-300" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">Revenue Account (Credit)</label>
                        <div class="relative">
                            <select name="revenue_account_id" required class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base bg-gray-50 hover:bg-white appearance-none">
                                @foreach($revenueAccounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">Payer / Customer</label>
                        <div class="relative">
                            <select name="customer_id" class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base bg-gray-50 hover:bg-white appearance-none">
                                <option value="">Select Customer (Optional)</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-base font-semibold text-gray-700 mb-2">Description / Notes</label>
                        <textarea name="description" rows="3" class="w-full rounded-xl border-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-base bg-gray-50 hover:bg-white" placeholder="Enter details about this income..."></textarea>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.incomes.index') }}" class="text-gray-600 hover:text-gray-900 font-medium px-4">Cancel</a>
                <button type="submit" class="bg-indigo-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 hover:shadow-lg transform transition hover:-translate-y-0.5 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Record Income
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
