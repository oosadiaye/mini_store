    {{-- Hero Section --}}
    <section class="mb-8">
        <div class="container-custom py-6">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                
                {{-- Main Slider (Col-span-3) --}}
                <div class="lg:col-span-3 relative h-[400px] md:h-[500px] rounded-xl overflow-hidden group">
                    {{-- Loop through slides (Mock for now, would be driven by Banners model) --}}
                    @php
                        $banners = $banners['main_slider'] ?? collect([]); 
                    @endphp
                    
                    <div class="absolute inset-0 bg-gray-900">
                         @if($banners->count() > 0)
                             {{-- Use actual banners --}}
                         @else
                             {{-- Default Promo Slide --}}
                             <div class="h-full w-full relative">
                                 <img src="https://images.unsplash.com/photo-1550009158-9ebf69173e03?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80" class="w-full h-full object-cover opacity-60">
                                 <div class="absolute inset-0 bg-gradient-to-r from-electro-dark via-transparent to-transparent"></div>
                                 <div class="absolute top-1/2 left-8 md:left-16 transform -translate-y-1/2 max-w-lg">
                                     @php
                                         $heroBadge = tenant()->data['hero_badge'] ?? 'New Arrival';
                                         $heroHeading = tenant()->data['hero_heading'] ?? 'Next Gen Gaming Rigs';
                                         $heroDescription = tenant()->data['hero_description'] ?? 'Experience ray tracing and ultra-performance with our latest custom builds.';
                                         $heroButtonText = tenant()->data['hero_button_text'] ?? 'Shop Now';
                                         
                                         // Split heading by | for line break
                                         $headingParts = explode('|', $heroHeading);
                                     @endphp
                                     
                                     <span class="inline-block px-3 py-1 bg-electro-neon text-electro-dark text-xs font-bold uppercase rounded mb-4">{{ $heroBadge }}</span>
                                     <h2 class="text-4xl md:text-6xl font-heading font-bold text-white mb-4 leading-tight">
                                         @if(count($headingParts) > 1)
                                             {{ $headingParts[0] }} <br> <span class="text-electro-blue">{{ $headingParts[1] }}</span>
                                         @else
                                             {{ $heroHeading }}
                                         @endif
                                     </h2>
                                     <p class="text-gray-300 mb-8 text-lg">{{ $heroDescription }}</p>
                                     <a href="{{ route('storefront.products.index') }}" class="inline-block bg-electro-blue text-white font-heading font-bold uppercase px-8 py-3 hover:bg-white hover:text-electro-dark transition rounded">
                                         {{ $heroButtonText }}
                                     </a>
                                 </div>
                             </div>
                         @endif
                    </div>
                </div>

                {{-- Side Banners (Col-span-1) --}}
                <div class="hidden lg:flex flex-col gap-6 h-full">
                    @php
                        $sideBanner1Image = tenant()->data['side_banner_1'] ?? null;
                        $sideBanner1Title = tenant()->data['side_banner_1_title'] ?? 'Headphones';
                        $sideBanner1Link = tenant()->data['side_banner_1_link_text'] ?? 'View Deals';
                        
                        $sideBanner2Image = tenant()->data['side_banner_2'] ?? null;
                        $sideBanner2Title = tenant()->data['side_banner_2_title'] ?? 'Cameras';
                        $sideBanner2Link = tenant()->data['side_banner_2_link_text'] ?? 'View Deals';
                    @endphp
                    
                    <div class="flex-1 bg-gray-100 rounded-xl overflow-hidden relative group">
                        <img src="{{ $sideBanner1Image ? route('tenant.media', ['path' => $sideBanner1Image]) : 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80' }}" class="absolute inset-0 w-full h-full object-cover transition transform group-hover:scale-105">
                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition"></div>
                        <div class="absolute bottom-6 left-6 text-white">
                            <h4 class="font-heading font-bold text-xl mb-1">{{ $sideBanner1Title }}</h4>
                            <a href="#" class="text-xs uppercase font-bold text-electro-neon hover:underline">{{ $sideBanner1Link }} &rarr;</a>
                        </div>
                    </div>
                    <div class="flex-1 bg-gray-100 rounded-xl overflow-hidden relative group">
                         <img src="{{ $sideBanner2Image ? route('tenant.media', ['path' => $sideBanner2Image]) : 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80' }}" class="absolute inset-0 w-full h-full object-cover transition transform group-hover:scale-105">
                         <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition"></div>
                         <div class="absolute bottom-6 left-6 text-white">
                            <h4 class="font-heading font-bold text-xl mb-1">{{ $sideBanner2Title }}</h4>
                            <a href="#" class="text-xs uppercase font-bold text-electro-neon hover:underline">{{ $sideBanner2Link }} &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
