<x-storefront.layout :config="\App\Models\StoreConfig::first()" :menuCategories="\App\Models\StoreCollection::take(5)->get()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="max-w-xl mx-auto">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Track Your Order</h1>
                <p class="mt-4 text-lg text-gray-500">Enter your order details below to check the current status.</p>
            </div>

            <div class="bg-white py-8 px-6 shadow-sm rounded-lg sm:px-10 border border-gray-200">
                @if(session('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">{{ session('error') }}</h3>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('storefront.orders.track.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="order_number" class="block text-sm font-medium text-gray-700">Order Number</label>
                        <div class="mt-1">
                            <input id="order_number" name="order_number" type="text" required class="block w-full appearance-none rounded-md border-2 border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="e.g. ORD-12345ABC">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" required class="block w-full appearance-none rounded-md border-2 border-gray-300 px-3 py-2 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" placeholder="Enter the email used for checkout">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md border border-transparent bg-[#0A2540] py-3 px-4 text-sm font-medium text-white shadow-sm hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Track Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-storefront-layout>
