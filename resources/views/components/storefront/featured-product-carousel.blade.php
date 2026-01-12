@props(['featuredProducts'])
<div id="featured" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" x-data="cartActions()">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
        <a href="#" class="text-[color:var(--brand-color)] font-medium hover:underline">View All &rarr;</a>
    </div>

    @if($featuredProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">
            @foreach($featuredProducts as $product)
                <div class="group relative flex flex-col bg-white rounded-[32px] overflow-hidden border border-gray-50 transition-all duration-500 hover:shadow-[0_40px_80px_-15px_rgba(10,37,64,0.12)] hover:-translate-y-2">
                    <!-- Image Container -->
                    <div class="relative aspect-[4/5] overflow-hidden bg-gray-50">
                        @if($product->primary_image)
                            <img src="{{ route('tenant.media', ['path' => $product->primary_image]) }}" 
                                 class="h-full w-full object-cover object-center group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gray-50 text-gray-200">
                                <svg class="h-20 w-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        
                        <!-- Hover Overlay: Quick Action -->
                        <div class="absolute inset-x-0 bottom-0 p-6 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                            <button @click="addToCart({{ $product->id }})" 
                                    class="w-full bg-[#0A2540] text-white py-4 rounded-2xl font-bold shadow-xl flex justify-center items-center hover:bg-[#1a3a5a] transition-colors"
                                    :disabled="loading === {{ $product->id }}">
                                <span x-show="loading !== {{ $product->id }}" class="flex items-center gap-2">
                                    <svg width="20" height="20" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add to Cart
                                </span>
                                <svg width="20" height="20" x-show="loading === {{ $product->id }}" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Stock Badge -->
                        @if($product->available_stock < 5 && $product->track_inventory)
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur shadow-sm text-[#0A2540] text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-full">
                                    Only {{ $product->available_stock }} left
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="p-8 pb-10 flex flex-col items-center text-center">
                        <span class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-2">{{ $product->category->name ?? 'Essentials' }}</span>
                        <h3 class="text-xl font-bold text-[#0A2540] mb-2 leading-tight">
                            {{ $product->name }}
                        </h3>
                        <p class="text-lg font-bold text-[#0A2540]/60">
                             ₦{{ number_format($product->price, 2) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-24 bg-gray-50/50 rounded-[40px] border border-dashed border-gray-200">
            <div class="max-w-md mx-auto">
                <img src="{{ asset('assets/illustrations/empty-bags.png') }}" alt="Empty Store" class="w-64 h-auto mx-auto mb-8 opacity-80">
                <h3 class="text-2xl font-bold text-[#0A2540] mb-2">Your shelves are waiting!</h3>
                <p class="text-gray-500 mb-8">Add products in your admin dashboard to see them shine here in the spotlight.</p>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-8 py-4 bg-[#0A2540] text-white font-bold rounded-2xl shadow-xl hover:-translate-y-1 transition-all">
                    Go to Dashboard &rarr;
                </a>
            </div>
        </div>
    @endif
</div>

<script>
    function cartActions() {
        return {
            loading: null,
            async addToCart(productId) {
                this.loading = productId;
                try {
                    const res = await fetch('{{ route("storefront.cart.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ product_id: productId, quantity: 1 })
                    });
                    
                    const data = await res.json();
                    
                    if (res.ok) {
                        alert('Added to cart!'); 
                    } else {
                        alert(data.error || 'Failed to add item');
                    }
                } catch (e) {
                    alert('Something went wrong');
                } finally {
                    this.loading = null;
                }
            }
        }
    }
</script>
