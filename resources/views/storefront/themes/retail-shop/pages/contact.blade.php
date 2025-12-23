@extends('storefront.themes.retail-shop.layout')

@section('pageTitle', 'Contact Us')

@section('content')

    {{-- Hero --}}
    <div class="bg-gray-50 py-16 md:py-24 border-b border-gray-100">
        <div class="container mx-auto px-4 md:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 mb-4">Get in Touch</h1>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">
                Have a question or just want to say hello? We'd love to hear from you.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            {{-- Contact Info --}}
            <div class="space-y-12">
                <div>
                    <h3 class="text-2xl font-serif font-bold mb-6">Visit Our Store</h3>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-gray-600 font-light text-lg">
                                123 Fashion Avenue<br>
                                Design District, NY 10012<br>
                            </p>
                            <a href="#" class="text-teal-600 font-medium hover:underline mt-2 inline-block">Get Directions &rarr;</a>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-serif font-bold mb-6">Customer Support</h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <a href="mailto:support@example.com" class="text-gray-600 hover:text-teal-600 text-lg">support@example.com</a>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <a href="tel:+15550192834" class="text-gray-600 hover:text-teal-600 text-lg">+1 (555) 019-2834</a>
                        </div>
                    </div>
                </div>

                 {{-- Socials --}}
                 <div class="flex gap-4 pt-4">
                    @foreach(['facebook', 'twitter', 'instagram', 'linkedin'] as $social)
                        <a href="#" class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-teal-600 hover:text-white hover:border-teal-600 transition duration-300">
                             <img src="https://cdn.jsdelivr.net/npm/simple-icons@v6/icons/{{ $social }}.svg" class="w-5 h-5 filter grayscale invert" alt="{{ $social }}">
                        </a>
                    @endforeach
                 </div>
            </div>

            {{-- Contact Form --}}
            <div class="glass-card p-8 md:p-10 rounded-2xl bg-white shadow-xl relative">
                {{-- Decorative Blob --}}
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-100 rounded-full blur-2xl opacity-50 -z-10"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-teal-100 rounded-full blur-2xl opacity-50 -z-10"></div>

                <h3 class="text-2xl font-serif font-bold mb-8">Send us a Message</h3>
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold uppercase tracking-wider text-gray-500">Name</label>
                            <input type="text" name="name" class="w-full bg-gray-50 border-gray-100 rounded-lg p-3 focus:ring-2 focus:ring-teal-500 transition" placeholder="John Doe">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold uppercase tracking-wider text-gray-500">Email</label>
                            <input type="email" name="email" class="w-full bg-gray-50 border-gray-100 rounded-lg p-3 focus:ring-2 focus:ring-teal-500 transition" placeholder="john@example.com">
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-sm font-bold uppercase tracking-wider text-gray-500">Subject</label>
                        <select name="subject" class="w-full bg-gray-50 border-gray-100 rounded-lg p-3 focus:ring-2 focus:ring-teal-500 transition">
                            <option>General Inquiry</option>
                            <option>Order Status</option>
                            <option>Returns</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold uppercase tracking-wider text-gray-500">Message</label>
                        <textarea name="message" rows="5" class="w-full bg-gray-50 border-gray-100 rounded-lg p-3 focus:ring-2 focus:ring-teal-500 transition" placeholder="How can we help?"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-teal-600 to-purple-600 text-white font-bold uppercase tracking-widest py-4 rounded-lg shadow-lg hover:shadow-xl hover:scale-[1.02] transition duration-300">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="h-[400px] w-full bg-gray-200 grayscale contrast-125 border-t border-gray-200">
        {{-- Placeholder Map --}}
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3024.2219901290355!2d-74.00369368400567!3d40.71312937933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a23e28c1191%3A0x49f75d3281df052a!2s150%20Park%20Row%2C%20New%20York%2C%20NY%2010007!5e0!3m2!1sen!2sus!4v1644268175409!5m2!1sen!2sus" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>

@endsection
