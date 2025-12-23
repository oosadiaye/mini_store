    <section class="relative h-[500px] flex items-center bg-gradient-premium overflow-hidden">
        <div class="absolute inset-0 z-0">
             {{-- Optional background image or pattern --}}
             <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-secondary/10"></div>
        </div>
        <div class="container mx-auto px-4 md:px-8 relative z-10 text-center md:text-left">
            <h1 class="text-5xl md:text-7xl font-serif font-bold mb-6 text-gray-900 leading-tight">
                Refined Retail <br> <span class="text-primary bg-clip-text text-transparent bg-gradient-brand">Reimagined.</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-lg mx-auto md:mx-0 font-light">
                Discover a curated collection of premium essentials designed to elevate your everyday lifestyle.
            </p>
            <a href="{{ route('storefront.products.index') }}" class="inline-block px-8 py-4 bg-gray-900 text-white font-medium uppercase tracking-widest hover:bg-primary transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 rounded-full">
                Start Shopping
            </a>
        </div>
        {{-- Decorative Circle --}}
        <div class="absolute -right-20 -bottom-40 w-[600px] h-[600px] bg-secondary/10 rounded-full blur-3xl opacity-50 mix-blend-multiply z-0"></div>
        <div class="absolute -left-20 -top-20 w-[400px] h-[400px] bg-primary/10 rounded-full blur-3xl opacity-50 mix-blend-multiply z-0"></div>
    </section>
