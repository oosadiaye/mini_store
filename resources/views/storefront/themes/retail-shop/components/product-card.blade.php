@props(['product'])

<div class="group relative">
    {{-- Image --}}
    <div class="w-full aspect-square bg-gray-100 rounded-xl overflow-hidden relative shadow-sm hover:shadow-lg transition-shadow duration-300">
        <a href="{{ route('storefront.product', $product) }}">
            @if($product->primary_image)
                <img src="{{ $product->primary_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            @endif
        </a>

        {{-- Badge --}}
        @if($product->price < $product->compare_price)
            <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] uppercase font-bold px-2 py-1 rounded-full shadow-sm">Sale</span>
        @endif

        {{-- Quick Actions --}}
        <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0 transition-all duration-300">
            <button class="flex-1 bg-white/90 backdrop-blur text-gray-900 text-xs font-bold uppercase py-2.5 rounded-l-lg hover:bg-teal-500 hover:text-white transition shadow-sm">
                Quick View
            </button>
            <form action="{{ route('storefront.cart.add', $product) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="w-full bg-gray-900 text-white text-xs font-bold uppercase py-2.5 rounded-r-lg hover:bg-teal-600 transition shadow-sm border-l border-gray-100">
                    Add
                </button>
            </form>
        </div>
    </div>

    {{-- Details --}}
    <div class="mt-4">
        <h3 class="text-lg font-serif font-bold text-gray-900 leading-tight group-hover:text-teal-600 transition truncate">
            <a href="{{ route('storefront.product', $product) }}">
                {{ $product->name }}
            </a>
        </h3>
        <div class="flex items-center justify-between mt-1">
            <p class="text-sm font-medium text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
            <div class="flex items-center gap-2">
                @if($product->compare_price > $product->price)
                    <span class="text-xs text-gray-400 line-through">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($product->compare_price, 2) }}</span>
                @endif
                <span class="text-teal-600 font-bold">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($product->price, 2) }}</span>
            </div>
        </div>
    </div>
</div>
