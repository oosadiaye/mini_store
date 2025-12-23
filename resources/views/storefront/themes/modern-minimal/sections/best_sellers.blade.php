@php
    // Logic for best sellers - using random for demo
    $bestSellers = \App\Models\Product::inRandomOrder()->take(4)->get();
@endphp

<section class="py-16 md:py-24 bg-white">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-serif">Best Sellers</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-10">
            @foreach($bestSellers as $product)
                @include('storefront.themes.modern-minimal.components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
