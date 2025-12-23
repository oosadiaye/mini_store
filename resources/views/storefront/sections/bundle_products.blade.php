@php
    $settings = $section->settings ?? [];
    $title = $settings['title'] ?? 'Bundle Products';
    $products = $bundleProducts ?? collect();
    
    // Get active theme slug using helper method
    $themeSlug = \App\Models\ThemeSetting::getActiveThemeSlug();
@endphp

<section class="py-12 bg-white" data-aos="fade-up">
    <div class="container mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">{{ $title }}</h2>
        
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Banner -->
            <div class="lg:w-1/4 bg-blue-600 rounded-xl p-8 text-white flex flex-col justify-center items-start shadow-lg overflow-hidden relative group">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10 bg-repeat" style="background-image: url('data:image/svg+xml;base64,...');"></div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-blue-500 rounded-full opacity-50 blur-2xl"></div>

                <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-bold uppercase tracking-wider mb-4 backdrop-blur-sm">Special Offer</span>
                
                <h3 class="text-3xl font-bold leading-tight mb-4">
                    Buy in bulk for your business and get volume discounts!
                </h3>
                
                <p class="text-blue-100 mb-8 text-sm">
                    Save up to 30% when you purchase bundle packs. Perfect for resellers and corporate gifts.
                </p>
                
                <a href="/shop?category=bundles" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-md group-hover:shadow-lg transform group-hover:-translate-y-1">
                    Buy Now <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <!-- Right Grid -->
            <div class="lg:w-3/4">
                @if($products->isEmpty())
                     <div class="flex items-center justify-center h-full bg-gray-50 border border-dashed rounded-xl text-gray-400">
                         No bundle products found.
                     </div>
                @else
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            @include("storefront.themes.{$themeSlug}.components.product-card", ['product' => $product])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
