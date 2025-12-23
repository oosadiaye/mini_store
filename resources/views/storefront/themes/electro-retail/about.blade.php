@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'About Us')

@section('content')
    
    {{-- Hero --}}
    <section class="bg-electro-dark text-white py-20 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-1.2.1&auto=format&fit=crop&w=1600&q=80')] bg-cover bg-center"></div>
        <div class="container-custom relative z-10 text-center">
            <h1 class="font-heading font-bold text-4xl md:text-5xl mb-4">Empowering Your Digital Lifestyle</h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">We are more than just a tech store. We are enthusiasts, creators, and innovators bringing you the future of technology today.</p>
        </div>
    </section>

    {{-- Stats Row --}}
    <section class="bg-electro-blue text-white py-12">
        <div class="container-custom grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="font-heading font-bold text-4xl md:text-5xl mb-1">10k+</div>
                <div class="text-sm uppercase tracking-wide opacity-80">Products Available</div>
            </div>
            <div>
                <div class="font-heading font-bold text-4xl md:text-5xl mb-1">50k+</div>
                <div class="text-sm uppercase tracking-wide opacity-80">Happy Customers</div>
            </div>
             <div>
                <div class="font-heading font-bold text-4xl md:text-5xl mb-1">99%</div>
                <div class="text-sm uppercase tracking-wide opacity-80">Satisfaction Rate</div>
            </div>
             <div>
                <div class="font-heading font-bold text-4xl md:text-5xl mb-1">24/7</div>
                <div class="text-sm uppercase tracking-wide opacity-80">Support Availability</div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="container-custom py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20">
            <div>
                <h2 class="font-heading font-bold text-3xl text-electro-dark mb-6">Our Mission</h2>
                <div class="w-16 h-1 bg-electro-neon mb-6"></div>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Founded in 2020, Electro Retail started with a simple vision: to make high-end technology accessible to everyone. We believe that technology should solve problems, unlock creativity, and enhance daily life. 
                </p>
                <p class="text-gray-600 leading-relaxed">
                    We meticulously curate our inventory to ensure every product meets our strict standards for quality, performance, and durability. From the latest gaming rigs to smart home essentials, we are your trusted partner in the digital age.
                </p>
            </div>
            <div class="bg-gray-100 rounded-xl overflow-hidden shadow-lg h-[400px]">
                <img src="https://images.unsplash.com/photo-1531297461136-82lw8e8e0e?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
             <div class="bg-gray-100 rounded-xl overflow-hidden shadow-lg h-[400px] md:order-last">
                <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover">
            </div>
            <div>
                <h2 class="font-heading font-bold text-3xl text-electro-dark mb-6">Why Shop With Us?</h2>
                <div class="w-16 h-1 bg-electro-neon mb-6"></div>
                <ul class="space-y-4">
                    <li class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-electro-blue flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Authentic Products</h4>
                            <p class="text-sm text-gray-500">We source directly from manufacturers to guarantee authenticity.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                         <div class="w-10 h-10 rounded-full bg-blue-50 text-electro-blue flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Fast Shipping</h4>
                            <p class="text-sm text-gray-500">Same-day dispatch for orders placed before 2 PM.</p>
                        </div>
                    </li>
                     <li class="flex items-start gap-4">
                         <div class="w-10 h-10 rounded-full bg-blue-50 text-electro-blue flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Expert Support</h4>
                            <p class="text-sm text-gray-500">Our team of tech experts is ready to assist you anytime.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Newsletter CTA (Repeated element for flow) --}}
    <section class="bg-gray-900 py-16 text-center text-white">
        <div class="container-custom">
            <h2 class="font-heading font-bold text-3xl mb-4">Join The Tech Revolution</h2>
            <p class="text-gray-400 mb-8 max-w-lg mx-auto">Stay ahead of the curve. Subscribe to our newsletter for exclusive deals, tech news, and product launches.</p>
            <form class="flex max-w-md mx-auto">
                <input type="email" placeholder="Your Email Address" class="flex-1 bg-white text-gray-900 px-6 py-4 rounded-l focus:outline-none">
                <button class="bg-electro-blue text-white font-heading font-bold uppercase px-8 py-4 rounded-r hover:bg-blue-600 transition">Subscribe</button>
            </form>
        </div>
    </section>

@endsection
