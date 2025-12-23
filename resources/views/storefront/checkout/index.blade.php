@include('storefront.themes.' . \App\Models\ThemeSetting::getActiveThemeSlug() . '.checkout.index')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Checkout</h1>

        <form action="{{ route('storefront.checkout.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Customer Info -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Contact Info -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800">Contact Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border" required>
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800">Shipping Address</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border" required>
                                @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border" required>
                                @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="address" value="{{ old('address') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border" placeholder="Street address, P.O. box, etc." required>
                                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border" required>
                                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal/Zip Code</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border" required>
                                @error('postal_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <select name="country" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-2 border" required>
                                    <option value="United States">United States</option>
                                    <option value="United Kingdom">United Kingdom</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                    <option value="Nigeria">Nigeria</option>
                                </select>
                                @error('country') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100" x-data="{ selectedPaymentId: null }">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800">Payment Method</h2>
                        
                        @if($paymentTypes->isEmpty())
                            <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg">
                                No payment methods available. Please contact support.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($paymentTypes as $paymentType)
                                <label class="block border rounded-lg cursor-pointer hover:bg-gray-50 transition overflow-hidden" 
                                       :class="selectedPaymentId == {{ $paymentType->id }} ? 'border-primary bg-indigo-50' : 'border-gray-200'">
                                    <div class="flex items-center p-4">
                                        <input type="radio" name="payment_type_id" value="{{ $paymentType->id }}" 
                                               class="text-primary focus:ring-primary h-4 w-4" 
                                               x-model="selectedPaymentId" required>
                                        <div class="ml-4 flex-1">
                                            <span class="block font-medium text-gray-900">{{ $paymentType->name }}</span>
                                            @if($paymentType->require_gateway)
                                                <span class="block text-xs text-gray-500">Secure Online Payment ({{ ucfirst($paymentType->gateway_provider) }})</span>
                                            @elseif($paymentType->type === 'bank')
                                                 <span class="block text-xs text-gray-500">Manual Transfer</span>
                                            @else
                                                <span class="block text-xs text-gray-500">Pay on delivery</span>
                                            @endif
                                        </div>
                                        @if($paymentType->require_gateway)
                                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded font-bold uppercase">Online</span>
                                        @endif
                                    </div>
                                    
                                    @if($paymentType->type === 'bank')
                                    <!-- Bank Details Dropdown -->
                                    <div x-show="selectedPaymentId == {{ $paymentType->id }}" x-collapse class="bg-gray-50 p-4 border-t border-gray-200 text-sm text-gray-700 space-y-2">
                                        @php $bank = is_string($paymentType->bank_details) ? json_decode($paymentType->bank_details, true) : $paymentType->bank_details; @endphp
                                        <p><span class="font-semibold">Bank Name:</span> {{ $bank['bank_name'] ?? 'N/A' }}</p>
                                        <p><span class="font-semibold">Account Number:</span> {{ $bank['account_number'] ?? 'N/A' }}</p>
                                        <p><span class="font-semibold">Account Name:</span> {{ $bank['account_name'] ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 italic mt-2">Please transfer the total amount to the details above.</p>
                                    </div>
                                    @endif
                                </label>
                                @endforeach
                            </div>
                        @endif
                        @error('payment_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 sticky top-24">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800">Order Summary</h2>
                        
                        <div class="space-y-4 mb-6 max-h-80 overflow-y-auto">
                            @foreach($cart->items as $item)
                            <div class="flex items-start pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                                <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded-md overflow-hidden">
                                    @if($item->product->images->count() > 0)
                                        <img src="{{ $item->product->images->first()->url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">?</div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                    @if($item->variant)
                                        <div class="text-xs text-gray-500">{{ $item->variant->name }}</div>
                                    @endif
                                    <div class="flex justify-between items-center mt-1">
                                        <div class="text-sm text-gray-500">Qty: {{ $item->quantity }}</div>
                                        <div class="text-sm font-medium text-gray-900">{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($item->price * $item->quantity, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span>{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($cart->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Tax</span>
                                <span>{{ tenant('data')['currency_symbol'] ?? '₦' }}0.00</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-gray-900 pt-2 border-t border-gray-200 mt-2">
                                <span>Total</span>
                                <span>{{ tenant('data')['currency_symbol'] ?? '₦' }}{{ number_format($cart->items->sum(fn($i) => $i->price * $i->quantity), 2) }}</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-6 bg-primary text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition font-medium shadow-md">
                            Place Order
                        </button>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('storefront.cart.index') }}" class="text-sm text-primary hover:text-indigo-700">Return to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
