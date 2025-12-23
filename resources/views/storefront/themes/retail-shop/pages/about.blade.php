@extends('storefront.themes.retail-shop.layout')

@section('pageTitle', 'About Us')

@section('content')

    {{-- Hero --}}
    <div class="bg-gray-900 py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80')] bg-cover bg-center"></div>
        <div class="container mx-auto px-4 md:px-8 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-white mb-6">Our Story</h1>
            <p class="text-gray-300 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                Building a bridge between premium quality and everyday accessibility. This is who we are.
            </p>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="bg-teal-600 py-12 text-white">
        <div class="container mx-auto px-4 md:px-8">
            <div class="flex flex-col md:flex-row justify-around items-center gap-8 md:gap-0 divide-y md:divide-y-0 md:divide-x divide-teal-500/50">
                <div class="text-center px-8 w-full">
                    <div class="text-4xl font-bold font-serif mb-2">12k+</div>
                    <div class="text-teal-200 text-sm uppercase tracking-widest">Happy Customers</div>
                </div>
                <div class="text-center px-8 w-full">
                    <div class="text-4xl font-bold font-serif mb-2">5</div>
                    <div class="text-teal-200 text-sm uppercase tracking-widest">Years of Excellence</div>
                </div>
                <div class="text-center px-8 w-full">
                    <div class="text-4xl font-bold font-serif mb-2">100+</div>
                    <div class="text-teal-200 text-sm uppercase tracking-widest">Premium Brands</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Section --}}
    <div class="container mx-auto px-4 md:px-8 py-20">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <div class="absolute -top-4 -left-4 w-full h-full border-2 border-teal-500 rounded-lg"></div>
                <img src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?ixlib=rb-1.2.1&auto=format&fit=crop&w=1000&q=80" alt="About Image" class="relative z-10 rounded-lg shadow-xl w-full h-auto">
            </div>
            <div>
                <h2 class="text-3xl font-serif font-bold mb-6 text-gray-900">Dedicated to Quality</h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    We started with a simple mission: to provide high-quality products that don't break the bank. Our team meticulously selects every item in our inventory to ensure it meets our strict standards for durability and design.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    From sourcing the finest materials to partnering with ethical manufacturers, every step of our process is guided by a commitment to excellence and sustainability.
                </p>
            </div>
        </div>
    </div>

    {{-- Team Section --}}
    <div class="bg-gray-50 py-20">
        <div class="container mx-auto px-4 md:px-8">
            <h2 class="text-3xl font-serif font-bold mb-12 text-center">Meet the Team</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach(['CEO', 'Designer', 'Marketing', 'Support'] as $role)
                <div class="glass-card p-8 text-center rounded-xl bg-white hover:shadow-xl transition group">
                    <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-6 bg-cover bg-center grayscale group-hover:grayscale-0 transition duration-500" style="background-image: url('https://i.pravatar.cc/150?u={{ $role }}')"></div>
                    <h3 class="text-xl font-bold font-serif text-gray-900">Alex Doe</h3>
                    <p class="text-teal-600 text-sm uppercase tracking-wider font-medium mb-4">{{ $role }}</p>
                    <div class="flex justify-center gap-3 opacity-0 group-hover:opacity-100 transition">
                         <a href="#" class="text-gray-400 hover:text-teal-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection
