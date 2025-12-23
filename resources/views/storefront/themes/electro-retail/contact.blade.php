@extends('storefront.themes.electro-retail.layout')

@section('pageTitle', 'Contact Us')

@section('content')
    
    {{-- Page Header --}}
    <div class="bg-gray-100 border-b border-gray-200 mb-12">
        <div class="container-custom py-12 text-center">
             <h1 class="font-heading font-bold text-4xl text-electro-dark mb-4">Get In Touch</h1>
             <p class="text-gray-500 max-w-xl mx-auto">Have a question about a product or need support with your order? Our team is available 24/7 to assist you.</p>
        </div>
    </div>

    <div class="container-custom mb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            {{-- Contact Info --}}
            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white border border-gray-100 rounded-xl p-8 shadow-lg">
                    <h3 class="font-heading font-bold text-xl text-electro-dark mb-6">Contact Information</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded bg-blue-50 text-electro-blue flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm uppercase mb-1">Headquarters</h5>
                                <p class="text-gray-500 text-sm">123 Tech Boulevard,<br>Silicon Valley, CA, 94000</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded bg-blue-50 text-electro-blue flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm uppercase mb-1">Phone</h5>
                                <p class="text-gray-500 text-sm">+1 (800) 123-4567<br>Mon-Fri, 9am - 6pm EST</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded bg-blue-50 text-electro-blue flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm uppercase mb-1">Email</h5>
                                <p class="text-gray-500 text-sm">support@electroretail.com<br>sales@electroretail.com</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-100 pt-6">
                        <h5 class="font-bold text-gray-800 text-sm uppercase mb-4">Follow Us</h5>
                        <div class="flex gap-2">
                             <a href="#" class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-electro-blue hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                             <a href="#" class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-electro-blue hover:text-white transition"><i class="fab fa-twitter"></i></a>
                             <a href="#" class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-electro-blue hover:text-white transition"><i class="fab fa-instagram"></i></a>
                             <a href="#" class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-electro-blue hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <div class="lg:col-span-2">
                 <form action="#" method="POST" class="bg-white border border-gray-200 rounded-xl p-8 shadow-sm">
                    <h3 class="font-heading font-bold text-xl text-electro-dark mb-6">Send Us A Message</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Name</label>
                            <input type="text" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Email</label>
                            <input type="email" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue" placeholder="john@example.com">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Subject</label>
                            <select class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue">
                                <option>Order Status</option>
                                <option>Product Inquiry</option>
                                <option>Technical Support</option>
                                <option>Returns & Refunds</option>
                                <option>Other</option>
                            </select>
                        </div>
                         <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Message</label>
                            <textarea rows="5" class="w-full border-gray-300 rounded focus:ring-electro-blue focus:border-electro-blue" placeholder="How can we help you?"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="bg-electro-blue text-white font-heading font-bold uppercase px-8 py-3 rounded hover:bg-blue-600 transition shadow-lg">Send Message</button>
                </form>
            </div>

        </div>
    </div>

    {{-- Map --}}
    <div class="h-[400px] w-full bg-gray-200 relative grayscale">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537353153167!3d-37.817323442021234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sEnvato!5e0!3m2!1sen!2s!4v1642646244799!5m2!1sen!2s" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>

@endsection
