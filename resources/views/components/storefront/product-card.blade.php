@props(['product'])

<div class="group relative flex flex-col h-full bg-white rounded-[20px] overflow-hidden border border-gray-50 transition-all duration-500 hover:shadow-[0_40px_80px_-15px_rgba(10,37,64,0.12)] hover:-translate-y-2">
    {{-- Image Container (Aspect Ratio 1:1) --}}
    <div class="relative aspect-square w-full overflow-hidden bg-gray-50">
        <a href="{{ route('storefront.product.detail', ['tenant' => app('tenant')->slug, 'slug' => $product['slug']]) }}" class="block h-full w-full">
            @if(isset($product['image_url']) && $product['image_url'])
                <img src="{{ $product['image_url'] }}" 
                     alt="{{ $product['name'] }}"
                     class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
            @else
                <div class="h-full w-full flex items-center justify-center bg-gray-50 text-gray-200">
                    <svg class="h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
        </a>
        
        {{-- Flash Sale Badge --}}
        @if(isset($product['is_flash_sale']) && $product['is_flash_sale'])
            <div class="absolute top-3 right-3 z-10">
                <span class="bg-red-500 text-white text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded">
                    Sale
                </span>
            </div>
        @endif

        {{-- Hover Overlay: Quick Action --}}
        <div class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-500 z-10">
            <button @click.stop="addToCart({{ $product['id'] }})" 
                    class="w-full bg-[#0A2540] text-white py-3 rounded-lg font-bold shadow-lg text-sm flex justify-center items-center hover:bg-[#1a3a5a] transition-colors"
                    :disabled="loading === {{ $product['id'] }}">
                <span x-show="loading !== {{ $product['id'] }}">Add to Cart</span>
                <svg width="20" height="20" x-show="loading === {{ $product['id'] }}" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Details --}}
    <div class="p-5 flex flex-col flex-1 items-start text-left">
        @if(isset($product['category']))
            <span class="text-[10px] uppercase tracking-widest text-gray-400 font-semibold mb-2">
                {{ is_array($product['category']) ? ($product['category']['name'] ?? '') : $product['category'] }}
            </span>
        @endif
        
        <h3 class="text-base font-bold text-gray-900 mb-2 leading-snug line-clamp-2 w-full flex-grow">
            <a href="{{ route('storefront.product.detail', ['tenant' => app('tenant')->slug, 'slug' => $product['slug']]) }}" class="hover:text-brand-600 hover:underline transition-colors">
                {{ $product['name'] }}
            </a>
        </h3>
        
        <div class="flex items-center gap-2 mt-auto pt-2 w-full border-t border-gray-50">
            @if(isset($product['is_flash_sale']) && $product['is_flash_sale'] && isset($product['flash_sale_price']))
                <p class="text-lg font-bold text-red-600">
                    ₦{{ number_format($product['flash_sale_price'], 2) }}
                </p>
                <p class="text-xs text-gray-400 line-through">
                    ₦{{ number_format($product['price'], 2) }}
                </p>
            @elseif(isset($product['compare_at_price']) && $product['compare_at_price'])
                <p class="text-lg font-bold text-gray-900">
                    ₦{{ number_format($product['price'], 2) }}
                </p>
                <p class="text-xs text-gray-400 line-through">
                    ₦{{ number_format($product['compare_at_price'], 2) }}
                </p>
            @else
                <p class="text-lg font-bold text-gray-900">
                    ₦{{ number_format($product['price'], 2) }}
                </p>
            @endif
        </div>
    </div>
</div>
