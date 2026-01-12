<x-storefront.layout :config="$config" :menuCategories="$menuCategories" :schema="$schema">
    
    <!-- Hero Section (Mini) -->
    <div class="relative bg-gray-50 py-16 md:py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10 pattern-dots"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-[#0A2540] font-heading mb-4">
                Get in Touch
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                {{ $schema['content']['contact_subtitle'] ?? 'We are here to help and answer any question you might have.' }}
            </p>
        </div>
    </div>

    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                
                <!-- Left Column: Contact Info & Map -->
                <div>
                    <h3 class="text-2xl font-bold text-[#0A2540] mb-8 font-heading">Contact Information</h3>
                    
                    <div class="space-y-8">
                        @if(!empty($contactInfo['email']))
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-[#0A2540]">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Email Us</h4>
                                <p class="text-gray-600 mt-1">Our friendly team is here to help.</p>
                                <a href="mailto:{{ $contactInfo['email'] }}" class="text-[#0A2540] font-medium hover:underline mt-1 block">
                                    {{ $contactInfo['email'] }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if(!empty($contactInfo['phone']))
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Call Us</h4>
                                <p class="text-gray-600 mt-1">Mon-Fri from 8am to 5pm.</p>
                                <a href="tel:{{ $contactInfo['phone'] }}" class="text-[#0A2540] font-medium hover:underline mt-1 block">
                                    {{ $contactInfo['phone'] }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if(!empty($contactInfo['address']))
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Visit Us</h4>
                                <p class="text-gray-600 mt-1">Come say hello at our office HQ.</p>
                                <p class="text-[#0A2540] font-medium mt-1">
                                    {{ $contactInfo['address'] }}
                                </p>
                                
                                <div class="mt-6 rounded-xl overflow-hidden shadow-sm border border-gray-100">
                                    {{-- Simple placeholder map or embed wrapper --}}
                                    <div class="bg-gray-100 h-48 flex items-center justify-center text-gray-400">
                                        <a href="https://maps.google.com/?q={{ urlencode($contactInfo['address']) }}" target="_blank" class="flex items-center gap-2 hover:text-[#0A2540] transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            View on Google Maps
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                            {{-- Fallback map if no address --}}
                             <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                 <div>
                                    <h4 class="text-lg font-bold text-gray-900">Online Store</h4>
                                    <p class="text-gray-600 mt-1">We operate globally.</p>
                                 </div>
                             </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Contact Form -->
                <div x-data="contactForm()" class="bg-gray-50 p-8 md:p-10 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
                    
                    <!-- Success Overlay -->
                    <div x-show="submitted" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute inset-0 bg-white z-10 flex flex-col items-center justify-center text-center p-8"
                         style="display: none;">
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Message Sent!</h3>
                        <p class="text-gray-600 mb-8">Thank you for contacting us. We will get back to you shortly.</p>
                        <button @click="resetForm()" class="text-[#0A2540] font-medium hover:underline">
                            Send another message
                        </button>
                    </div>

                    <h3 class="text-2xl font-bold text-[#0A2540] mb-6 font-heading">Send us a Message</h3>
                    
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="name" x-model="form.name" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#0A2540] focus:ring focus:ring-[#0A2540]/20 transition-all outline-none"
                                   placeholder="John Doe">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" x-model="form.email" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#0A2540] focus:ring focus:ring-[#0A2540]/20 transition-all outline-none"
                                   placeholder="john@example.com">
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                            <input type="text" id="subject" x-model="form.subject" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#0A2540] focus:ring focus:ring-[#0A2540]/20 transition-all outline-none"
                                   placeholder="How can we help?">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                            <textarea id="message" x-model="form.message" rows="4" required
                                      class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#0A2540] focus:ring focus:ring-[#0A2540]/20 transition-all outline-none resize-none"
                                      placeholder="Tell us more..."></textarea>
                        </div>

                        <button type="submit" 
                                :disabled="loading"
                                class="w-full bg-[#0A2540] text-white font-bold py-3.5 rounded-full hover:shadow-lg hover:-translate-y-0.5 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span x-show="!loading">Send Message</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function contactForm() {
            return {
                form: {
                    name: '',
                    email: '',
                    subject: '',
                    message: ''
                },
                loading: false,
                submitted: false,
                async submit() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route("storefront.api.contact", ["tenant" => app("tenant")->slug]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.form)
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.submitted = true;
                            // Optional: Reset form immediately or wait for reset button
                        } else {
                            alert(data.message || 'Something went wrong. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An unexpected error occurred.');
                    } finally {
                        this.loading = false;
                    }
                },
                resetForm() {
                    this.submitted = false;
                    this.form = {
                        name: '',
                        email: '',
                        subject: '',
                        message: ''
                    };
                }
            }
        }
    </script>
</x-storefront.layout>
