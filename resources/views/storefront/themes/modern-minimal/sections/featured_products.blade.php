@php
    // Fetch random products for demo, ideally would be flagged as 'featured'
    $featuredProducts = \App\Models\Product::inRandomOrder()->take(4)->get();
@endphp

<section class="py-16 md:py-24 bg-gray-50 border-t border-gray-100">
    <div class="container mx-auto px-4 md:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-serif mb-4">Curated For You</h2>
            <p class="text-gray-500 max-w-lg mx-auto">Handpicked essentials that blend form and function for the modern lifestyle.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-10">
            @foreach($featuredProducts as $product)
                @include('storefront.themes.modern-minimal.components.product-card', ['product' => $product])
            @endforeach
            
            @if($featuredProducts->isEmpty())
                <div class="col-span-full text-center text-gray-400 py-12">
                    No products found.
                </div>
            @endif
        </div>
    </div>
</section>
