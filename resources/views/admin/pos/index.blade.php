@extends('admin.layout')

@section('content')
<script src="https://unpkg.com/dexie/dist/dexie.js"></script>
<script src="https://unpkg.com/@zxing/library@latest"></script>
<style>
    [x-cloak] { display: none !important; }
</style>
<div class="h-[calc(100vh-6rem)] flex -m-6" x-data="posSystem()">
    <!-- Left: Product Grid - Takes 70% width for more product display -->
    <div class="w-[70%] bg-gray-100 p-2 pr-2 md:p-6 overflow-y-auto border-r border-gray-200">
        <!-- Search & Filter -->
        <div class="mb-2 md:mb-6 flex flex-col gap-1.5 md:flex-row md:gap-4">
            <input type="text" x-model="search" placeholder="Search..." 
                class="flex-1 px-2 py-1.5 md:px-4 md:py-3 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs md:text-lg">
            
            <select x-model="selectedCategory" class="px-2 py-1.5 md:px-4 md:py-3 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs md:text-base">
                <option value="">All</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            
            <!-- Control Buttons -->
            <div class="flex gap-2">
                <!-- Barcode Scanner Button (All Devices) -->
                <button @click="startBarcodeScanner" class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg flex items-center justify-center gap-1 text-xs font-semibold hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    Scan
                </button>
                
                <!-- Fullscreen Button -->
                <button @click="toggleFullscreen" class="hidden md:flex px-3 py-1.5 bg-gray-700 text-white rounded-lg items-center justify-center gap-1 text-xs font-semibold hover:bg-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                    </svg>
                    Fullscreen
                </button>
                
                <!-- Sidebar Toggle Button -->
                <button @click="toggleSidebar" class="hidden md:flex px-3 py-1.5 bg-gray-700 text-white rounded-lg items-center justify-center gap-1 text-xs font-semibold hover:bg-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    Menu
                </button>
            </div>
        </div>

        <!-- Barcode Scanner Modal -->
        <div x-show="scannerActive" x-cloak class="fixed inset-0 bg-black z-50 flex flex-col">
            <div class="p-4 bg-gray-900 flex justify-between items-center">
                <h3 class="text-white font-bold">Scan Barcode</h3>
                <button @click="stopBarcodeScanner" class="text-white px-4 py-2 bg-red-600 rounded-lg">Close</button>
            </div>
            <div class="flex-1 relative">
                <video id="barcode-video" class="w-full h-full object-cover"></video>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-64 h-48 border-2 border-green-500 rounded-lg"></div>
                </div>
            </div>
            <div class="p-4 bg-gray-900 text-white text-center text-sm">
                Position barcode within the frame
            </div>
        </div>

        <div x-show="isLoading" class="flex justify-center py-10">
             <div class="animate-spin rounded-full h-8 md:h-12 w-8 md:w-12 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Products - 2 columns on mobile, 3 on tablet, 4 on desktop, 6 on xl -->
        <div x-show="!isLoading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-1.5 md:gap-4">
            <template x-for="product in filteredProducts" :key="product.id">
                <div @click="addToCart(product)" class="bg-white rounded-lg shadow cursor-pointer hover:shadow-md transition p-1.5 md:p-3 flex flex-col group">
                    <div class="aspect-square relative mb-1.5 md:mb-3 bg-gray-100 rounded overflow-hidden">
                        <img :src="product.image_url" class="object-cover w-full h-full group-hover:scale-110 transition duration-500" 
                        onerror="this.src='https://placehold.co/200x200?text=No+Image'">
                    </div>
                    <h3 class="font-semibold text-gray-800 text-[10px] md:text-sm mb-0.5 md:mb-1 leading-tight line-clamp-2" x-text="product.name"></h3>
                    <p class="text-indigo-600 font-bold text-[10px] md:text-base" x-text="formatPrice(product.price)"></p>
                </div>
            </template>
        </div>
    </div>

    <!-- Right: Cart & Checkout - Takes 30% width (reduced from 45%) -->
    <div class="w-[30%] bg-white flex flex-col h-full shadow-xl z-10">
        <!-- Cart Header -->
        <div class="p-3 md:p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <span>Cart</span>
                <span class="text-xs md:text-sm font-normal text-gray-500 bg-white px-2 py-1 rounded border" x-text="cart.length + ' items'"></span>
            </h2>
            <div class="flex items-center gap-2">
                 <div class="text-xs px-2 py-1 rounded" :class="online ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="online ? 'Online' : 'Offline'"></div>
                 <button @click="connectBluetoothPrinter" class="hidden md:block text-xs px-2 py-1 rounded hover:bg-gray-700 transition" :class="printerConnected ? 'bg-green-600 text-white' : 'bg-gray-800 text-white'">
                    <span x-text="printerConnected ? 'Printer ✓' : 'Connect Printer'"></span>
                 </button>
                 <button @click="openDisplay" class="hidden md:block text-xs bg-gray-800 text-white px-3 py-1 rounded hover:bg-gray-700">Display</button>
            </div>
        </div>
        
        <div class="px-3 md:px-6 pt-3 md:pt-4">
             <select x-model="selectedCustomer" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <option value="">Walk-in Customer</option>
                <template x-for="customer in customers" :key="customer.id">
                    <option :value="customer.id" x-text="customer.name"></option>
                </template>
            </select>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-2 md:p-4 space-y-2 md:space-y-3">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-12 md:w-16 h-12 md:h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-sm md:text-base">Cart is empty</p>
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-center justify-between p-2 md:p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-1 min-w-0 mr-2">
                        <h4 class="font-medium text-gray-800 text-xs md:text-sm truncate" x-text="item.name"></h4>
                        <p class="text-xs text-gray-500" x-text="formatPrice(item.price)"></p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="updateQty(index, -1)" class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 text-sm">-</button>
                        <span class="font-bold w-4 text-center text-sm" x-text="item.quantity"></span>
                        <button @click="updateQty(index, 1)" class="w-7 h-7 md:w-8 md:h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 text-sm">+</button>
                    </div>
                    <div class="text-right ml-2 w-16 md:w-20">
                        <p class="font-bold text-gray-800 text-xs md:text-sm" x-text="formatPrice(item.price * item.quantity)"></p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Calculations -->
        <div class="p-3 md:p-6 bg-gray-50 border-t border-gray-200 space-y-2">
            <div class="flex justify-between text-gray-600 text-sm">
                <span>Subtotal</span>
                <span x-text="formatPrice(subtotal)"></span>
            </div>
            <div class="flex justify-between text-gray-600 text-sm">
                <span>Tax (<span x-text="enableTax ? taxRate + '%' : 'Off'"></span>)</span>
                <span x-text="formatPrice(tax)"></span>
            </div>
            <div class="flex justify-between text-lg md:text-xl font-bold text-gray-900 border-t border-gray-200 pt-2 mt-2">
                <span>Total</span>
                <span x-text="formatPrice(total)"></span>
            </div>

            <!-- Payment Method -->
            <div class="mt-4">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select x-model="selectedPaymentMethod" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <template x-for="type in paymentTypes" :key="type.id">
                        <option :value="type.id" x-text="type.name"></option>
                    </template>
                </select>
            </div>

            <!-- Amount Tendered -->
            <div class="mt-4">
                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Amount Tendered</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 text-sm" x-text="currencySymbol"></span>
                    </div>
                    <input type="number" x-model="amountTendered" step="0.01" class="block w-full rounded-md border-gray-300 pl-7 pr-12 focus:border-indigo-500 focus:ring-indigo-500 text-base md:text-lg font-bold" placeholder="0.00">
                </div>
            </div>

            <!-- Change Display -->
            <div class="mt-4 p-3 md:p-4 bg-gray-100 rounded-lg flex justify-between items-center" :class="change < 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-700'">
                <span class="font-bold text-sm md:text-base">Change Due</span>
                <span class="text-lg md:text-xl font-bold" x-text="formatPrice(Math.max(0, change))"></span>
            </div>
            <div x-show="change < 0" class="text-xs text-red-500 mt-1 text-right">
                Short by <span x-text="formatPrice(Math.abs(change))"></span>
            </div>

            <!-- Pay Button -->
            <button 
                @click="processPayment"
                :disabled="!canCheckout"
                class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-base md:text-lg font-bold py-3 md:py-4 rounded-xl shadow-lg hover:shadow-xl transition transform active:scale-95 flex items-center justify-center">
                <span x-show="!processing">Charge <span x-text="formatPrice(total)"></span></span>
                <span x-show="processing" class="animate-pulse">Processing...</span>
            </button>
            <div x-show="unsyncedOrders > 0" class="mt-2 text-center text-xs text-orange-600 font-bold">
                 <span x-text="unsyncedOrders"></span> Offline Orders Waiting Sync
            </div>
        </div>
    </div>
</div>

<script>
function posSystem() {
    return {
        db: null,
        products: [],
        paymentTypes: [], // Load from API or Blade initially
        customers: [],
        cart: [],
        search: '',
        selectedCategory: '',
        selectedPaymentMethod: null,
        selectedCustomer: '',
        amountTendered: '',
        processing: false,
        isLoading: true,
        online: navigator.onLine,
        unsyncedOrders: 0,
        
        taxRate: {{ $taxRate ?? 0 }},
        enableTax: {{ $enableTax ? 'true' : 'false' }},
        currencySymbol: '{{ $currencySymbol }}',
        
        // Barcode Scanner
        scannerActive: false,
        codeReader: null,
        videoStream: null,
        beepSound: null,
        barcodeBuffer: '',
        barcodeTimeout: null,
        
        // Thermal Printer
        bluetoothDevice: null,
        printerCharacteristic: null,
        printerConnected: false,

        async init() {
            // Setup DB
            this.db = new Dexie(`pos_db_{{ tenant('id') }}`);
            this.db.version(1).stores({
                products: 'id, category_id, name',
                customers: 'id, name',
                paymentTypes: 'id',
                orders: '++id, synced' // synced = 0 (false) or 1 (true)
            });

            // Online/Offline Listeners
            window.addEventListener('online', () => { this.online = true; this.syncOrders(); });
            window.addEventListener('offline', () => { this.online = false; });

            this.$watch('cart', () => this.broadcast());
            this.$watch('amountTendered', () => this.broadcast());
            this.$watch('selectedPaymentMethod', () => this.broadcast());

            // Initial Load
            await this.loadData();
            this.countUnsynced();
            this.broadcast();
            
            // Initialize beep sound
            this.initBeepSound();
            
            // Setup USB Barcode Scanner Listener
            this.setupBarcodeListener();
        },

        async loadData() {
            this.isLoading = true;
            try {
                // Try fetching from Server first
                if (this.online) {
                     // We need an endpoint that returns all JSON for POS (Products, Customers, PaymentTypes)
                     // For now, we are simulating "Sync" by using the Blade data passed initially
                     // In a real scenario, we'd hit /api/pos/sync
                     
                     // Use the blade variables to populate DB for the first time
                     const serverProducts = [
                        @foreach($categories as $category)
                            @foreach($category->products as $product)
                            {
                                id: {{ $product->id }},
                                name: "{{ addslashes($product->name) }}",
                                price: {{ $product->price }},
                                category_id: {{ $category->id }},
                                image_url: "{!! $product->image_url !!}",
                                stock: {{ $product->stock ?? 999 }},
                                sku: "{{ $product->sku ?? '' }}",
                                barcode: "{{ $product->barcode ?? '' }}"
                            },
                            @endforeach
                        @endforeach
                     ];
                     const serverCustomers = [
                        @foreach($customers as $customer)
                        { id: {{ $customer->id }}, name: "{{ addslashes($customer->name) }}" },
                        @endforeach
                     ];
                     const serverPaymentTypes = [
                        @foreach($paymentTypes as $pt)
                        { id: {{ $pt->id }}, name: "{{ addslashes($pt->name) }}" },
                        @endforeach
                     ];

                     // Bulk Put (Upsert)
                     await this.db.products.bulkPut(serverProducts);
                     await this.db.customers.bulkPut(serverCustomers);
                     await this.db.paymentTypes.bulkPut(serverPaymentTypes);
                }

                // Always load from DB (Source of Truth)
                this.products = await this.db.products.toArray();
                this.customers = await this.db.customers.toArray();
                this.paymentTypes = await this.db.paymentTypes.toArray();

                // Select default payment
                if(this.paymentTypes.length > 0) this.selectedPaymentMethod = this.paymentTypes[0].id;

            } catch (e) {
                console.error("Error loading data", e);
            } finally {
                this.isLoading = false;
            }
        },

        openDisplay() {
            window.open('{{ route("admin.pos.display") }}', 'CustomerDisplay', 'width=800,height=600,menubar=no,toolbar=no,location=no,status=no');
        },

        broadcast() {
             const state = {
                cart: this.cart,
                subtotal: this.subtotal,
                tax: this.tax,
                total: this.total,
                currencySymbol: this.currencySymbol,
                amountTendered: this.amountTendered,
                change: this.change,
                taxRate: this.enableTax ? this.taxRate : 0,
                timestamp: Date.now()
            };
            localStorage.setItem('pos_state', JSON.stringify(state));
        },

        get filteredProducts() {
            return this.products.filter(p => {
                const matchesSearch = p.name.toLowerCase().includes(this.search.toLowerCase());
                const matchesCategory = this.selectedCategory === '' || p.category_id == this.selectedCategory;
                return matchesSearch && matchesCategory;
            });
        },

        get subtotal() { return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0); },
        get tax() { return !this.enableTax ? 0 : this.subtotal * (this.taxRate / 100); },
        get total() { return this.subtotal + this.tax; },
        get change() { return (parseFloat(this.amountTendered) || 0) - this.total; },
        get canCheckout() {
            if (this.cart.length === 0 || this.processing || !this.selectedPaymentMethod) return false;
            return (parseFloat(this.amountTendered) || 0) >= this.total - 0.01; 
        },

        addToCart(product) {
            const existing = this.cart.find(item => item.id === product.id);
            if (existing) existing.quantity++;
            else this.cart.push({ ...product, quantity: 1 });
            this.playBeep(); // Play beep sound
        },

        updateQty(index, change) {
            this.cart[index].quantity += change;
            if (this.cart[index].quantity <= 0) this.cart.splice(index, 1);
        },

        formatPrice(amount) {
            return this.currencySymbol + Number(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        },

        async processPayment() {
            if (!confirm('Process payment of ' + this.formatPrice(this.total) + '?')) return;
            this.processing = true;

            const orderData = {
                items: this.cart,
                subtotal: this.subtotal,
                tax: this.tax,
                total: this.total,
                payment_method_id: this.selectedPaymentMethod,
                amount_tendered: this.amountTendered,
                change: this.change,
                customer_id: this.selectedCustomer || null,
                created_at: new Date().toISOString()
            };

            if (this.online) {
                // Try sending to Server
                try {
                    const response = await fetch('{{ route("admin.pos.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(orderData)
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Order processed successfully!');
                        await this.printReceipt({...orderData, order_id: data.order_id});
                        this.resetCart();
                    } else {
                        throw new Error(data.message);
                    }
                } catch (e) {
                    console.error("Online payment failed, saving offline", e);
                    // Fallback to offline save if server request fails (but we thought we were online)
                    await this.saveOffline(orderData);
                }
            } else {
                // Save Offline directly
                await this.saveOffline(orderData);
            }
            
            this.processing = false;
        },

        async saveOffline(orderData) {
            try {
                await this.db.orders.add({ ...orderData, synced: 0 });
                alert('Offline: Order saved locally. Will sync when online.');
                this.countUnsynced();
                this.resetCart();
            } catch (e) {
                alert('Error saving offline order: ' + e.message);
            }
        },

        async syncOrders() {
            if (!this.online) return;
            const unsynced = await this.db.orders.where('synced').equals(0).toArray();
            if (unsynced.length === 0) return;

            console.log(`Syncing ${unsynced.length} orders...`);
            
            for (const order of unsynced) {
                try {
                    const response = await fetch('{{ route("admin.pos.store") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(order)
                    });
                    const data = await response.json();
                    if (data.success) {
                        // Mark as synced or delete
                        await this.db.orders.update(order.id, { synced: 1 });
                        // Optionally print receipt here if we want automatic printing after sync
                    }
                } catch(e) {
                    console.error("Sync failed for order", order.id, e);
                }
            }
            this.countUnsynced();
        },

        countUnsynced() {
            this.db.orders.where('synced').equals(0).count().then(c => this.unsyncedOrders = c);
        },

        printReceipt(orderId) {
             const receiptUrl = '{{ route("admin.pos.receipt", ":id") }}'.replace(':id', orderId);
             window.open(receiptUrl, 'Receipt', 'width=400,height=600');
        },

        resetCart() {
            this.cart = [];
            this.amountTendered = '';
            this.selectedCustomer = '';
            this.broadcast();
        },
        
        // Beep Sound
        initBeepSound() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            this.beepSound = () => {
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                oscillator.frequency.value = 800;
                oscillator.type = 'sine';
                gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.1);
            };
        },
        
        playBeep() {
            if (this.beepSound) this.beepSound();
        },
        
        // Barcode Scanner
        async startBarcodeScanner() {
            this.scannerActive = true;
            await this.$nextTick();
            
            try {
                this.codeReader = new ZXing.BrowserMultiFormatReader();
                const videoElement = document.getElementById('barcode-video');
                
                this.codeReader.decodeFromVideoDevice(null, videoElement, (result, err) => {
                    if (result) {
                        const barcode = result.text;
                        this.handleBarcodeScanned(barcode);
                    }
                });
            } catch (error) {
                console.error('Scanner error:', error);
                alert('Camera access denied or not available');
                this.stopBarcodeScanner();
            }
        },
        
        stopBarcodeScanner() {
            if (this.codeReader) {
                this.codeReader.reset();
                this.codeReader = null;
            }
            this.scannerActive = false;
        },
        
        handleBarcodeScanned(barcode) {
            const product = this.products.find(p => p.sku === barcode || p.barcode === barcode);
            if (product) {
                this.addToCart(product);
                this.playBeep();
                this.stopBarcodeScanner();
            } else {
                alert('Product not found: ' + barcode);
            }
        },
        
        // USB Barcode Scanner Listener
        setupBarcodeListener() {
            document.addEventListener('keypress', (e) => {
                // Ignore if user is typing in an input field
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    return;
                }
                
                // Clear timeout if exists
                if (this.barcodeTimeout) {
                    clearTimeout(this.barcodeTimeout);
                }
                
                // Add character to buffer
                if (e.key === 'Enter') {
                    // Barcode scanners typically end with Enter
                    if (this.barcodeBuffer.length > 3) { // Minimum barcode length
                        this.handleBarcodeScanned(this.barcodeBuffer);
                    }
                    this.barcodeBuffer = '';
                } else {
                    this.barcodeBuffer += e.key;
                    
                    // Reset buffer after 100ms of no input (barcode scanners are fast)
                    this.barcodeTimeout = setTimeout(() => {
                        this.barcodeBuffer = '';
                    }, 100);
                }
            });
        },
        
        // Fullscreen Toggle
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                // Enter fullscreen and hide sidebar
                document.documentElement.requestFullscreen().catch(err => {
                    console.error('Fullscreen error:', err);
                });
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.add('hidden');
                }
            } else {
                // Exit fullscreen
                document.exitFullscreen();
            }
        },
        
        // Sidebar Toggle
        toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.toggle('hidden');
            }
        },
        
        // Thermal Printer Functions
        async connectBluetoothPrinter() {
            try {
                this.bluetoothDevice = await navigator.bluetooth.requestDevice({
                    filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }],
                    optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb']
                });
                
                const server = await this.bluetoothDevice.gatt.connect();
                const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                this.printerCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                
                this.printerConnected = true;
                alert('Printer connected successfully!');
            } catch (error) {
                console.error('Bluetooth connection error:', error);
                alert('Failed to connect printer. Make sure Bluetooth is enabled.');
            }
        },
        
        async printReceipt(orderData) {
            if (this.printerConnected && this.printerCharacteristic) {
                await this.printThermal(orderData);
            } else {
                // Fallback to browser print
                this.printBrowser(orderData);
            }
        },
        
        async printThermal(orderData) {
            try {
                const encoder = new TextEncoder();
                const ESC = '\x1B';
                const GS = '\x1D';
                
                // ESC/POS Commands
                let receipt = '';
                receipt += ESC + '@'; // Initialize
                receipt += ESC + 'a' + '\x01'; // Center align
                receipt += ESC + '!' + '\x30'; // Double height/width
                receipt += '{{ tenant("name") }}\n';
                receipt += ESC + '!' + '\x00'; // Normal
                receipt += '\n';
                receipt += ESC + 'a' + '\x00'; // Left align
                receipt += '--------------------------------\n';
                receipt += 'Order #: ' + (orderData.order_id || 'N/A') + '\n';
                receipt += 'Date: ' + new Date().toLocaleString() + '\n';
                receipt += '--------------------------------\n';
                
                // Items
                orderData.items.forEach(item => {
                    receipt += item.name.substring(0, 20) + '\n';
                    receipt += '  ' + item.quantity + ' x ' + this.formatPrice(item.price);
                    receipt += ' = ' + this.formatPrice(item.price * item.quantity) + '\n';
                });
                
                receipt += '--------------------------------\n';
                receipt += 'Subtotal: ' + this.formatPrice(orderData.subtotal) + '\n';
                receipt += 'Tax: ' + this.formatPrice(orderData.tax) + '\n';
                receipt += ESC + '!' + '\x20'; // Bold
                receipt += 'TOTAL: ' + this.formatPrice(orderData.total) + '\n';
                receipt += ESC + '!' + '\x00'; // Normal
                receipt += '--------------------------------\n';
                receipt += ESC + 'a' + '\x01'; // Center
                receipt += 'Thank you for your purchase!\n';
                receipt += '\n\n\n';
                receipt += GS + 'V' + '\x00'; // Cut paper
                
                const data = encoder.encode(receipt);
                await this.printerCharacteristic.writeValue(data);
            } catch (error) {
                console.error('Print error:', error);
                alert('Print failed. Trying browser print...');
                this.printBrowser(orderData);
            }
        },
        
        printBrowser(orderData) {
            const printWindow = window.open('', '', 'width=300,height=600');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        body { font-family: monospace; width: 300px; margin: 20px; }
                        .center { text-align: center; }
                        .bold { font-weight: bold; }
                        .line { border-top: 1px dashed #000; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class="center bold">${'{{ tenant("name") }}'}</div>
                    <div class="line"></div>
                    <div>Order #: ${orderData.order_id || 'N/A'}</div>
                    <div>Date: ${new Date().toLocaleString()}</div>
                    <div class="line"></div>
                    ${orderData.items.map(item => `
                        <div>${item.name}</div>
                        <div>${item.quantity} x ${this.formatPrice(item.price)} = ${this.formatPrice(item.price * item.quantity)}</div>
                    `).join('')}
                    <div class="line"></div>
                    <div>Subtotal: ${this.formatPrice(orderData.subtotal)}</div>
                    <div>Tax: ${this.formatPrice(orderData.tax)}</div>
                    <div class="bold">TOTAL: ${this.formatPrice(orderData.total)}</div>
                    <div class="line"></div>
                    <div class="center">Thank you!</div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    }
}
</script>
@endsection
