@extends('admin.layout')

@section('title', $renter->name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('admin.renters.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Renters</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Renter Profile -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $renter->name }}</h1>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $renter->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $renter->status == 'inactive' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $renter->status == 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($renter->status) }}
                        </span>
                    </div>
                    <a href="{{ route('admin.renters.edit', $renter) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Edit</a>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Email:</span>
                        <p class="font-medium">{{ $renter->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Phone:</span>
                        <p class="font-medium">{{ $renter->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">ID Number:</span>
                        <p class="font-medium">{{ $renter->id_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Address:</span>
                        <p class="font-medium">{{ $renter->address ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($renter->notes)
                <div class="mt-4 pt-4 border-t">
                    <span class="text-gray-500 text-sm">Notes:</span>
                    <p class="text-sm mt-1">{{ $renter->notes }}</p>
                </div>
                @endif
            </div>

            <!-- AR Ledger -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">AR Ledger</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Debit</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Credit</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $balance = 0; @endphp
                            @forelse($renter->transactions()->with(['journalEntry', 'account'])->get() as $transaction)
                                @php $balance += ($transaction->debit - $transaction->credit); @endphp
                                <tr>
                                    <td class="px-4 py-2 text-sm">{{ $transaction->journalEntry->entry_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-sm">{{ $transaction->journalEntry->description }}</td>
                                    <td class="px-4 py-2 text-sm text-right">{{ $transaction->debit > 0 ? '₦'.number_format($transaction->debit, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-sm text-right">{{ $transaction->credit > 0 ? '₦'.number_format($transaction->credit, 2) : '' }}</td>
                                    <td class="px-4 py-2 text-sm text-right font-semibold {{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">₦{{ number_format($balance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">No transactions yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Contract Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Details</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500">Rental Amount:</span>
                        <p class="font-bold text-lg">₦{{ number_format($renter->rental_amount, 2) }}</p>
                        <span class="text-xs text-gray-500">per {{ $renter->payment_frequency }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Contract Period:</span>
                        <p class="font-medium">{{ $renter->contract_start_date?->format('M d, Y') }} - {{ $renter->contract_end_date?->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Security Deposit:</span>
                        <p class="font-medium">₦{{ number_format($renter->security_deposit, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Outstanding Balance -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Outstanding Balance</h3>
                <p class="text-3xl font-bold {{ $renter->outstanding_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    ₦{{ number_format($renter->outstanding_balance, 2) }}
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <button onclick="document.getElementById('invoiceModal').classList.remove('hidden')" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Generate Invoice</button>
                    <button onclick="document.getElementById('paymentModal').classList.remove('hidden')" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Record Payment</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div id="invoiceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-semibold mb-4">Generate Invoice</h3>
        <form action="{{ route('admin.renters.invoice', $renter) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" step="0.01" name="amount" value="{{ $renter->rental_amount }}" required class="mt-1 block w-full rounded-md border-gray-300">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <input type="text" name="description" value="Rental Invoice for {{ now()->format('F Y') }}" required class="mt-1 block w-full rounded-md border-gray-300">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Invoice Date</label>
                <input type="date" name="invoice_date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('invoiceModal').classList.add('hidden')" class="bg-gray-300 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Generate</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-semibold mb-4">Record Payment</h3>
        <form action="{{ route('admin.renters.payment', $renter) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Amount</label>
                <input type="number" step="0.01" name="amount" required class="mt-1 block w-full rounded-md border-gray-300">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select name="payment_method" required class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="cash">Cash</option>
                    <option value="bank">Bank Transfer</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Payment Date</label>
                <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <input type="text" name="notes" class="mt-1 block w-full rounded-md border-gray-300">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="bg-gray-300 px-4 py-2 rounded-md">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Record Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection
