@extends('layouts.superadmin')

@section('header', 'Payment Approvals')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Pending Manual Payments</h3>
        <p class="text-sm text-gray-500 mt-1">Review manual bank transfers and approve subscriptions.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-500">
            <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                <tr>
                    <th class="px-6 py-3">Tenant</th>
                    <th class="px-6 py-3">Plan</th>
                    <th class="px-6 py-3">Amount</th>
                    <th class="px-6 py-3">Reference</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3 text-center">Proof</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($payments as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $payment->tenant->id }}</div>
                        <div class="text-xs text-gray-500">
                            @if($payment->tenant->users->first())
                                {{ $payment->tenant->users->first()->email }}
                            @else
                                No User Found
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $payment->plan->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">
                        {{ number_format($payment->amount, 2) }}
                    </td>
                    <td class="px-6 py-4 font-mono text-xs">
                        {{ $payment->transaction_reference ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $payment->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($payment->payment_proof)
                            <a href="{{ Storage::url($payment->payment_proof) }}" target="_blank" class="text-blue-600 hover:text-blue-900 underline text-xs">View Proof</a>
                        @else
                            <span class="text-gray-400 text-xs">No Proof</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <form action="{{ route('superadmin.payment-approvals.approve', $payment) }}" method="POST" class="inline-block" onsubmit="return confirm('Approve this payment and activate subscription?');">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-900 font-bold text-xs uppercase bg-green-50 px-3 py-1 rounded border border-green-200">Approve</button>
                        </form>
                        
                        <form action="{{ route('superadmin.payment-approvals.reject', $payment) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject this payment?');">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-xs uppercase bg-red-50 px-3 py-1 rounded border border-red-200">Reject</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No pending payments</h3>
                        <p class="mt-1 text-sm text-gray-500">All manual payments have been processed.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-200">
        {{ $payments->links() }}
    </div>
</div>
@endsection
