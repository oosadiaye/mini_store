<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 h-screen overflow-hidden" x-data="customerDisplay()">
    
    <div class="flex h-full">
        <!-- Left: Branding / Ads -->
        <div class="w-1/2 bg-indigo-900 flex flex-col items-center justify-center text-white p-12">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" class="max-w-xs mb-8 rounded-xl shadow-2xl bg-white p-4">
            @else
                <h1 class="text-6xl font-black tracking-tighter mb-4">{{ $tenant->name }}</h1>
            @endif
            
            <p class="text-2xl opacity-75 text-center">Thank you for shopping with us!</p>
            
            <!-- Connection Status -->
            <div class="fixed bottom-4 left-4 flex items-center gap-2 text-xs opacity-50">
                <div class="w-2 h-2 rounded-full" :class="connected ? 'bg-green-400' : 'bg-red-400'"></div>
                <span x-text="connected ? 'Connected' : 'Waiting for POS...'"></span>
            </div>
        </div>

        <!-- Right: Receipt -->
        <div class="w-1/2 bg-white p-8 flex flex-col shadow-2xl h-full">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-4">Your Order</h2>
            
            <!-- Items -->
            <div class="flex-1 overflow-y-auto space-y-4 pr-2">
                <template x-if="data.cart.length === 0">
                    <div class="h-full flex items-center justify-center text-gray-400 text-lg">
                        Processing...
                    </div>
                </template>

                <template x-for="item in data.cart" :key="item.id">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <div class="font-bold text-xl text-gray-800" x-text="item.name"></div>
                            <div class="text-gray-500">
                                <span x-text="item.quantity"></span> x <span x-text="formatPrice(item.price)"></span>
                            </div>
                        </div>
                        <div class="font-bold text-xl text-gray-900" x-text="formatPrice(item.price * item.quantity)"></div>
                    </div>
                </template>
            </div>

            <!-- Totals -->
            <div class="mt-auto pt-6 border-t-2 border-gray-100 space-y-3">
                <div class="flex justify-between text-xl text-gray-600">
                    <span>Subtotal</span>
                    <span x-text="formatPrice(data.subtotal || 0)"></span>
                </div>
                <template x-if="data.taxRate > 0">
                     <div class="flex justify-between text-xl text-gray-600">
                        <span>Tax</span>
                        <span x-text="formatPrice(data.tax || 0)"></span>
                    </div>
                </template>
                
                <div class="flex justify-between text-4xl font-black text-indigo-900 py-4">
                    <span>Total</span>
                    <span x-text="formatPrice(data.total || 0)"></span>
                </div>

                <template x-if="data.amountTendered > 0">
                    <div class="bg-indigo-50 p-4 rounded-xl space-y-2">
                        <div class="flex justify-between text-lg text-indigo-800">
                            <span>Paid</span>
                            <span x-text="formatPrice(data.amountTendered)"></span>
                        </div>
                         <div class="flex justify-between text-2xl font-bold" :class="data.change < 0 ? 'text-red-600' : 'text-green-600'">
                            <span>Change</span>
                            <span x-text="formatPrice(Math.max(0, data.change))"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
    function customerDisplay() {
        return {
            connected: false,
            data: {
                cart: [],
                subtotal: 0,
                tax: 0,
                total: 0,
                amountTendered: 0,
                change: 0,
                taxRate: 0,
                currencySymbol: '{{ $currencySymbol }}'
            },

            init() {
                // Initial load from storage
                this.loadState();
                
                // Listen for changes from other tabs
                window.addEventListener('storage', (event) => {
                    if (event.key === 'pos_state') {
                        this.loadState();
                    }
                });
                
                this.connected = true;
            },

            loadState() {
                const stored = localStorage.getItem('pos_state');
                if (stored) {
                    try {
                        this.data = JSON.parse(stored);
                    } catch (e) {
                        console.error('Error parsing POS state', e);
                    }
                }
            },

            formatPrice(amount) {
                return (this.data.currencySymbol || '{{ $currencySymbol }}') + Number(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
    }
    </script>
</body>
</html>
