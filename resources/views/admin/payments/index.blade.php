@extends('admin.layout')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Outstanding Payments</h2>
</div>

<div x-data="{ activeTab: 'receivables' }">
    <!-- Tabs -->
    <div class="flex space-x-4 border-b border-gray-200 mb-6">
        <button @click="activeTab = 'receivables'" :class="{ 'border-indigo-600 text-indigo-600': activeTab === 'receivables', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'receivables' }" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition">
            Receivables (Customer Invoices)
            @if($receivables->total() > 0)
            <span class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2.5 rounded-full text-xs hover:bg-indigo-200 transition">
                {{ $receivables->total() }}
            </span>
            @endif
        </button>
        <button @click="activeTab = 'payables'" :class="{ 'border-red-600 text-red-600': activeTab === 'payables', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'payables' }" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition">
            Payables (Supplier Bills)
            @if($payables->total() > 0)
            <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2.5 rounded-full text-xs hover:bg-red-200 transition">
                {{ $payables->total() }}
            </span>
            @endif
        </button>
    </div>

    <!-- Receivables Content -->
    <div x-show="activeTab === 'receivables'" x-cloak>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b border-gray-100 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Order #</th>
                        <th class="px-6 py-3">Customer</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Paid</th>
                        <th class="px-6 py-3 text-right">Balance Due</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($receivables as $order)
                    @php 
                        $balance = $order->total - $order->amount_paid;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 font-medium text-indigo-600">
                            <a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a>
                        </td>
                        <td class="px-6 py-4">{{ $order->customer->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right font-medium">${{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4 text-right text-green-600">${{ number_format($order->amount_paid, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-red-600">${{ number_format($balance, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                            <button @click="$dispatch('open-payment-modal', { type: 'customer', id: {{ $order->id }}, balance: {{ $balance }}, ref: '{{ $order->order_number }}' })" 
                                class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase border border-indigo-200 px-3 py-1 rounded-full hover:bg-indigo-50 transition">
                                Record Payment
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">No outstanding customer invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($receivables->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $receivables->appends(['active_tab' => 'receivables'])->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Payables Content -->
    <div x-show="activeTab === 'payables'" x-cloak>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 border-b border-gray-100 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">PO #</th>
                        <th class="px-6 py-3">Supplier</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Paid</th>
                        <th class="px-6 py-3 text-right">Balance Due</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payables as $po)
                    @php 
                        $balance = $po->total - $po->amount_paid;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            <a href="{{ route('admin.purchase-orders.show', $po->id) }}">
                                {{ substr($po->id, 0, 8) }}
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ $po->supplier->name }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $po->order_date ? $po->order_date->format('M d, Y') : '-' }}</td>
                        <td class="px-6 py-4 text-right font-medium">${{ number_format($po->total, 2) }}</td>
                        <td class="px-6 py-4 text-right text-green-600">${{ number_format($po->amount_paid, 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-red-600">${{ number_format($balance, 2) }}</td>
                        <td class="px-6 py-4 text-right">
                             <button @click="$dispatch('open-payment-modal', { type: 'supplier', id: {{ $po->id }}, balance: {{ $balance }}, ref: 'PO-{{ substr($po->id, 0, 8) }}' })" 
                                class="text-indigo-600 hover:text-indigo-900 text-xs font-bold uppercase border border-indigo-200 px-3 py-1 rounded-full hover:bg-indigo-50 transition">
                                Pay Bill
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">No outstanding supplier bills found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($payables->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payables->appends(['active_tab' => 'payables'])->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Payment Modal (Alpine) -->
    <div x-data="{ open: false, type: '', id: null, amount: 0, ref: '', paymentRef: '' }" 
         @open-payment-modal.window="open = true; type = $event.detail.type; id = $event.detail.id; amount = $event.detail.balance; ref = $event.detail.ref" 
         x-show="open" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form method="POST" :action="type === 'customer' ? '/admin/orders/' + id + '/payment' : '/admin/purchase-orders/' + id + '/payment'">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Record Payment <span x-text="type === 'customer' ? 'from Customer' : 'to Supplier'"></span>
                                </h3>
                                <div class="mt-2 text-sm text-gray-500">
                                    Record payment for <span x-text="ref" class="font-bold"></span>.
                                </div>
                                
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" name="amount" step="0.01" x-model="amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                        <select name="payment_method" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="credit_card">Credit Card</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Reference / Notes</label>
                                        <input type="text" name="reference" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Confirm Payment
                        </button>
                        <button @click="open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
