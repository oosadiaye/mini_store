    {{-- Featured Products --}}
    <section class="container-custom mb-16">
        <div class="flex justify-between items-end mb-8 border-b border-gray-200 pb-2">
            <h2 class="text-2xl md:text-3xl font-heading font-bold text-electro-dark">Featured <span class="text-electro-blue">Products</span></h2>
            <div class="flex gap-6 text-sm font-bold uppercase">
                <a href="#" class="text-electro-blue border-b-2 border-electro-blue pb-2.5">New Arrivals</a>
                <a href="#" class="text-gray-400 hover:text-gray-800 pb-2.5">Best Sellers</a>
                <a href="#" class="text-gray-400 hover:text-gray-800 pb-2.5">On Sale</a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
             @foreach($new_arrivals->take(5) as $product)
                 @include('storefront.themes.electro-retail.components.product-card', ['product' => $product])
             @endforeach
        </div>
    </section>
