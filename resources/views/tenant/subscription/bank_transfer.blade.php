<x-guest-layout>
    <div class="mb-4 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Manual Bank Transfer</h2>
            <p class="text-sm text-gray-600">Please transfer the amount to the account below and upload your receipt.</p>
    </div>

    <div class="bg-indigo-50 border border-indigo-100 rounded-md p-4 mb-6">
        <h3 class="font-bold text-indigo-800 mb-2">Bank Details</h3>
        <div class="space-y-1 text-sm text-indigo-700">
            <p><span class="font-medium">Bank Name:</span> {{ $paymentConfig['bank_name'] ?? 'N/A' }}</p>
            <p><span class="font-medium">Account Name:</span> {{ $paymentConfig['account_name'] ?? 'N/A' }}</p>
            <p><span class="font-medium">Account Number:</span> <span class="font-mono text-lg font-bold">{{ $paymentConfig['account_number'] ?? 'N/A' }}</span></p>
            @if(isset($paymentConfig['instructions']))
            <p class="mt-2 text-xs text-indigo-500 italic">{{ $paymentConfig['instructions'] }}</p>
            @endif
        </div>
        
        <div class="mt-4 pt-4 border-t border-indigo-200">
            @if($proration)
                <div class="mb-3">
                    <p class="text-xs text-indigo-600">Original Price: <s>₦{{ number_format($plan->price) }}</s></p>
                    <p class="text-xs text-green-600 font-medium">Credit Applied: -₦{{ number_format($proration['credit'], 2) }}</p>
                    <p class="text-xs text-indigo-500">({{ $proration['unused_days'] }} unused days from current plan)</p>
                </div>
                <p class="text-sm font-medium text-indigo-900">Amount Due: <span class="text-lg font-bold text-green-600">₦{{ number_format($proration['amount_due'], 2) }}</span></p>
            @else
                <p class="text-sm font-medium text-indigo-900">Amount Due: <span class="text-lg font-bold">₦{{ number_format($plan->price) }}</span></p>
            @endif
            <p class="text-xs text-indigo-600">Plan: {{ $plan->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('tenant.subscription.submit-payment', ['tenant' => app('tenant')->slug]) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
        
        <!-- Payment Proof -->
        <div class="mt-4">
            <label for="proof" class="block font-medium text-sm text-gray-700">Upload Payment Receipt</label>
            <input id="proof" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" type="file" name="proof" required accept="image/*,application/pdf" />
            @error('proof')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notes -->
        <div class="mt-4">
            <label for="notes" class="block font-medium text-sm text-gray-700">Additional Notes (Optional)</label>
            <textarea id="notes" name="notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="2"></textarea>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a href="{{ route('tenant.subscription.index', ['tenant' => app('tenant')->slug]) }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Cancel') }}
            </a>

            <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Submit Payment') }}
            </button>
        </div>
    </form>
</x-guest-layout>
