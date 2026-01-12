@extends('layouts.superadmin')

@section('header')
    {{ __('Subscription Requests') }}
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                @if($transactions->isEmpty())
                    <p class="text-gray-500 text-center py-4">No pending payment requests.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proof</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $transaction->tenant_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->plan_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                        ₦{{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                        {{ $transaction->reference }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                        @if($transaction->proof_path)
                                            <a href="{{ route('global.media', ['path' => $transaction->proof_path]) }}" target="_blank" class="hover:underline">View Receipt</a>
                                        @else
                                            <span class="text-gray-400">None</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <form action="{{ route('superadmin.subscription-requests.approve', $transaction->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this subscription?');">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-bold">Approve</button>
                                            </form>
                                            <form action="{{ route('superadmin.subscription-requests.reject', $transaction->id) }}" method="POST" onsubmit="return confirm('Reject this request?');">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
