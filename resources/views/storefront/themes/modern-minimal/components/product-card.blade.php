@props(['product'])

<div class="group">
    <a href="/products/{{ $product->id }}">
        <div class="aspect-square bg-gray-100 mb-4 overflow-hidden relative">
            <img src="{{ $product->image_url ?? 'https://via.placeholder.com/400x400' }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            
            {{-- Quick Add (Desktop Hover) --}}
            <div class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition duration-300 hidden md:block bg-gradient-to-t from-black/50 to-transparent">
                <button class="w-full bg-white text-black py-3 text-xs font-bold uppercase tracking-widest hover:bg-gray-100">
                    Add to Cart
                </button>
            </div>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-medium text-gray-900 group-hover:underline decoration-1 underline-offset-4">{{ $product->name }}</h3>
                <p class="text-gray-500 text-sm mt-1">{{ tenant('currency_symbol') ?? '$' }}{{ number_format($product->price, 2) }}</p>
            </div>
            @if($product->sale_price)
                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 uppercase tracking-wider">Sale</span>
            @endif
        </div>
    </a>
</div>
