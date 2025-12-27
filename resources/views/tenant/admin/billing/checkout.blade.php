@extends('layouts.app')

@section('header', 'Checkout')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
        </div>
        
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h4 class="text-xl font-bold text-gray-900">{{ $plan->name }}</h4>
                    <p class="text-gray-500">{{ $plan->duration_days }} Days Access</p>
                </div>
                <div class="text-2xl font-bold text-blue-600">
                    {{ number_format($plan->price, 2) }} USD
                </div>
            </div>

            <hr class="my-6">

            <h4 class="text-md font-medium text-gray-900 mb-4">Select Payment Method</h4>

            <form action="{{ route('admin.billing.pay', $plan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4 mb-6">
                    <!-- Manual / Bank Transfer -->
                    <label class="relative block border rounded-lg p-4 cursor-pointer hover:border-blue-500 focus-within:ring-2 ring-blue-500 bg-white">
                        <div class="flex items-center">
                            <input type="radio" name="payment_method" value="manual" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" checked onclick="toggleDetails('manual')">
                            <span class="ml-3 font-medium text-gray-900">Bank Transfer / Manual</span>
                        </div>
                        <div id="manual-details" class="mt-4 ml-7 text-sm text-gray-600 bg-gray-50 p-4 rounded border border-gray-200">
                            <p class="mb-2"><strong>Bank Details:</strong><br>
                            Bank Name: Global Bank<br>
                            Account No: 1234567890<br>
                            Account Name: MiniStore Inc.<br>
                            Ref: Use tenant ID <strong>{{ tenant('id') }}</strong></p>
                            
                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Upload Receipt/Proof</label>
                                <input type="file" name="proof" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            
                            <div class="mt-2">
                                <textarea name="notes" placeholder="Additional notes..." class="w-full text-sm rounded border-gray-300"></textarea>
                            </div>
                        </div>
                    </label>

                    {{-- Dynamic Gateways loop --}}
                    @foreach($gateways as $gateway)
                        @if(in_array($gateway->name, ['paystack', 'flutterwave', 'opay']))
                        <label class="relative block border rounded-lg p-4 cursor-pointer hover:border-blue-500 focus-within:ring-2 ring-blue-500 bg-white">
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="{{ $gateway->name }}" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" onclick="toggleDetails('{{ $gateway->name }}')">
                                <span class="ml-3 font-medium text-gray-900">{{ $gateway->display_name }}</span>
                            </div>
                        </label>
                        @endif
                    @endforeach
                </div>

                <div class="flex items-center justify-end border-t pt-6">
                    <a href="{{ route('admin.billing.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 mr-6">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-md shadow-sm transition">
                        Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleDetails(method) {
        const manualDetails = document.getElementById('manual-details');
        if (method === 'manual') {
            manualDetails.classList.remove('hidden');
            manualDetails.style.display = 'block';
        } else {
            manualDetails.classList.add('hidden');
            manualDetails.style.display = 'none';
        }
    }
</script>
@endsection
