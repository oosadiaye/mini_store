    {{-- Categories Icon Row --}}
    <section class="container-custom mb-16">
        <div class="flex flex-wrap md:flex-nowrap justify-between gap-4">
            @foreach($categories->take(8) as $cat)
                <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="group flex flex-col items-center justify-center bg-white border border-gray-100 rounded-lg p-6 w-[45%] md:w-auto flex-1 hover:border-electro-blue hover:shadow-lg transition">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mb-3 group-hover:bg-electro-blue group-hover:text-white transition">
                         {{-- Placeholder Icons based on iteration or name --}}
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="font-heading font-bold text-sm text-gray-800 group-hover:text-electro-blue transition">{{ $cat->name }}</span>
                </a>
            @endforeach
        </div>
    </section>
