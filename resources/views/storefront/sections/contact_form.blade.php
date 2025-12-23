@php
    $settings = $section->settings ?? [];
@endphp

<section class="py-16 bg-gray-50" x-data="contactForm()">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $section->title ?? 'Get In Touch' }}</h2>
            <p class="text-gray-600">{{ $section->content ?? 'We\'d love to hear from you' }}</p>
        </div>
        
        <!-- Contact Form -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <form @submit.prevent="submitForm" class="space-y-6">
                @csrf
                
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           x-model="formData.name"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="John Doe">
                </div>
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           x-model="formData.email"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="john@example.com">
                </div>
                
                <!-- Phone Field -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <input type="tel" 
                           id="phone" 
                           x-model="formData.phone"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="+1 (555) 000-0000">
                </div>
                
                <!-- Subject Field -->
                <div>
                    <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="subject" 
                           x-model="formData.subject"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="How can we help?">
                </div>
                
                <!-- Message Field -->
                <div>
                    <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                        Message <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" 
                              x-model="formData.message"
                              required
                              rows="5"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition resize-none"
                              placeholder="Tell us more about your inquiry..."></textarea>
                </div>
                
                <!-- Success Message -->
                <div x-show="success" 
                     x-transition
                     class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    <p class="font-semibold">✓ Message sent successfully!</p>
                    <p class="text-sm">We'll get back to you as soon as possible.</p>
                </div>
                
                <!-- Error Message -->
                <div x-show="error" 
                     x-transition
                     class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <p class="font-semibold">✗ Something went wrong</p>
                    <p class="text-sm" x-text="errorMessage"></p>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="loading"
                        :class="loading ? 'opacity-50 cursor-not-allowed' : 'hover:bg-indigo-700'"
                        class="w-full bg-indigo-600 text-white font-bold py-4 px-6 rounded-lg transition shadow-lg">
                    <span x-show="!loading">Send Message</span>
                    <span x-show="loading">Sending...</span>
                </button>
            </form>
        </div>
    </div>
</section>

<script>
function contactForm() {
    return {
        formData: {
            name: '',
            email: '',
            phone: '',
            subject: '',
            message: ''
        },
        loading: false,
        success: false,
        error: false,
        errorMessage: '',
        
        async submitForm() {
            this.loading = true;
            this.success = false;
            this.error = false;
            
            try {
                // Simulate API call - replace with actual endpoint
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                // Reset form
                this.formData = {
                    name: '',
                    email: '',
                    phone: '',
                    subject: '',
                    message: ''
                };
                
                this.success = true;
                setTimeout(() => this.success = false, 5000);
            } catch (err) {
                this.error = true;
                this.errorMessage = 'Please try again later.';
                setTimeout(() => this.error = false, 5000);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
