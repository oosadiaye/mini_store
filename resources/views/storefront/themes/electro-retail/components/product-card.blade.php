@props(['product'])

<div class="group bg-white border border-gray-100 hover:border-electro-blue hover:shadow-xl transition-all duration-300 rounded-lg relative overflow-hidden flex flex-col h-full">
    
    {{-- Badges --}}
    <div class="absolute top-2 left-2 z-10 flex flex-col gap-1">
        @if($product->price < $product->compare_price)
            <span class="bg-electro-neon text-electro-dark text-[10px] font-bold px-2 py-0.5 uppercase tracking-wide rounded">-{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%</span>
        @endif
        @if(now()->diffInDays($product->created_at) < 7)
            <span class="bg-electro-blue text-white text-[10px] font-bold px-2 py-0.5 uppercase tracking-wide rounded">New</span>
        @endif
    </div>

    {{-- Image --}}
    <div class="relative aspect-square p-4 bg-white flex items-center justify-center group-hover:bg-gray-50 transition-colors">
        <a href="{{ route('storefront.product', $product) }}" class="w-full h-full flex items-center justify-center">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-500">
            @else
                <div class="text-gray-200">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            @endif
        </a>
        
        {{-- Hover Actions --}}
        <div class="absolute inset-x-0 bottom-0 p-2 flex gap-1 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
             <button class="flex-1 bg-electro-dark text-white text-xs font-bold uppercase py-2 hover:bg-electro-blue transition rounded">
                Quick View
            </button>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-3 flex-grow flex flex-col border-t border-gray-100">
        {{-- Category --}}
        <div class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">{{ $product->category->name ?? 'Tech' }}</div>
        
        {{-- Title --}}
        <h3 class="text-sm font-bold text-gray-800 leading-tight mb-2 line-clamp-2 hover:text-electro-blue transition">
            <a href="{{ route('storefront.product', $product) }}">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Rating --}}
        <div class="flex items-center text-yellow-400 text-xs mb-2">
            @for($i=0; $i<5; $i++)
                <svg class="w-3 h-3 {{ $i < 4 ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            @endfor
            <span class="text-gray-400 ml-1">(24)</span>
        </div>
        
        {{-- Tech Specs (Mock) --}}
        <div class="text-[10px] text-gray-500 mb-3 border-l-2 border-electro-gray pl-2 leading-none py-0.5">
            5G Support • 128GB • OLED
        </div>

        {{-- Price --}}
        <div class="mt-auto flex justify-between items-end">
            <div>
                 @if($product->compare_price > $product->price)
                    <div class="text-xs text-gray-400 line-through mb-0.5">{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format($product->compare_price, 2) }}</div>
                @endif
                <div class="text-lg font-bold text-electro-blue">{{ tenant('currency_symbol') ?? (tenant('currency') == 'NGN' ? '₦' : '$') }}{{ number_format($product->price, 2) }}</div>
            </div>
            
            <form action="{{ route('storefront.cart.add', $product) }}" method="POST">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="bg-gray-100 text-electro-dark p-2 rounded hover:bg-electro-neon hover:text-electro-dark transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>
