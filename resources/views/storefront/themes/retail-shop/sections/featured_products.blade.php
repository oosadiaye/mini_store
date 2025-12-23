    {{-- Featured/New Arrivals --}}
    <section class="py-16 md:py-24 bg-gray-50 border-t border-gray-100">
        <div class="container mx-auto px-4 md:px-8">
            <h2 class="text-3xl md:text-4xl font-serif font-bold mb-12 text-center">New Arrivals</h2>
            
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 gap-y-12">
                @foreach($new_arrivals->take(4) as $product)
                     @include('storefront.themes.retail-shop.components.product-card', ['product' => $product])
                @endforeach
            </div>
             
             <div class="mt-16 text-center">
                 <a href="{{ route('storefront.products.index') }}" class="inline-block px-10 py-3 border border-gray-900 text-gray-900 font-medium uppercase tracking-widest hover:bg-primary hover:text-white hover:border-primary transition rounded-full">
                     Shop All
                 </a>
             </div>
        </div>
    </section>
