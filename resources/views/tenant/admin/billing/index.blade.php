@extends('layouts.app')

@section('header', 'Plans & Billing')

@section('content')
<div class="space-y-6">
    <!-- Current Plan Status -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Current Subscription</h3>
                <div class="mt-2 flex items-center gap-4">
                    <span class="text-2xl font-bold text-gray-900">{{ $currentPlan ? $currentPlan->name : 'Free Plan' }}</span>
                    @if($tenant->subscription_ends_at)
                        @if($tenant->subscription_ends_at->isFuture())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Expired</span>
                        @endif
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Lifetime / Free</span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    @if($tenant->subscription_ends_at)
                        Expires on {{ $tenant->subscription_ends_at->format('M d, Y') }} ({{ $tenant->subscription_ends_at->diffForHumans() }})
                    @endif
                </p>
            </div>
            <!-- Usage Stats (Example) -->
            <div class="bg-gray-50 rounded p-4 text-sm text-gray-600 space-y-1 min-w-[200px]">
                <div class="flex justify-between">
                    <span>Products</span>
                    <span class="font-medium">- / {{ $tenant->getLimit('products_limit') == -1 ? '∞' : $tenant->getLimit('products_limit') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Transactions</span>
                    <span class="font-medium">- / {{ $tenant->getLimit('orders_limit') == -1 ? '∞' : $tenant->getLimit('orders_limit') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Plans -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-lg shadow-sm border {{ $currentPlan && $currentPlan->id == $plan->id ? 'border-blue-500 ring-1 ring-blue-500' : 'border-gray-200' }} p-6 flex flex-col">
            <h3 class="text-lg font-medium text-gray-900">{{ $plan->name }}</h3>
            <div class="mt-4">
                <span class="text-3xl font-bold text-gray-900">{{ number_format($plan->price) }}</span>
                <span class="text-gray-500">/ {{ $plan->duration_days }} days</span>
            </div>
            
            <ul class="mt-6 space-y-4 flex-1">
                @foreach($plan->features ?? [] as $feature => $enabled)
                    @if($enabled && $feature != 'features') 
                    <li class="flex items-start">
                        <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="ml-3 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                    </li>
                    @endif
                @endforeach
                {{-- Manually list caps if needed --}}
                @if(isset($plan->caps['products_limit']) && $plan->caps['products_limit'] > 0)
                     <li class="flex items-start">
                        <svg class="flex-shrink-0 h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="ml-3 text-sm text-gray-500">{{ $plan->caps['products_limit'] }} Products</span>
                    </li>
                @endif
            </ul>

            <div class="mt-8">
                @if($currentPlan && $currentPlan->id == $plan->id)
                    <button disabled class="w-full bg-gray-100 text-gray-500 font-bold py-2 px-4 rounded-md cursor-not-allowed">
                        Current Plan
                    </button>
                    @if($tenant->subscription_ends_at && $tenant->subscription_ends_at->diffInDays(now()) < 7)
                        <a href="{{ route('admin.billing.checkout', $plan) }}" class="mt-2 block w-full text-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Renew Now
                        </a>
                    @endif
                @else
                    <a href="{{ route('admin.billing.checkout', $plan) }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md shadow-sm transition">
                        {{ $plan->price == 0 ? 'Switch to Free' : 'Upgrade' }}
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Payment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Reference</th>
                        <th class="px-6 py-3">Method</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($history as $payment)
                    <tr>
                        <td class="px-6 py-4">{{ $payment->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $payment->transaction_reference }}</td>
                        <td class="px-6 py-4">{{ ucfirst($payment->payment_method) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $payment->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No payment history found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
