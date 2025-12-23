@props(['settings' => []])

<form action="{{ route('storefront.contact.submit') }}" method="POST" class="space-y-6">
    @csrf
    <div>
        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Name</label>
        <input type="text" name="name" id="name" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4">
    </div>
    
    <div>
        <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Email</label>
        <input type="email" name="email" id="email" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4">
    </div>

    @if(!empty($settings['enable_phone_field']))
    <div>
        <label for="phone" class="block text-sm font-bold text-gray-700 mb-2">Phone (Optional)</label>
        <input type="tel" name="phone" id="phone" class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4">
    </div>
    @endif

    <div>
        <label for="message" class="block text-sm font-bold text-gray-700 mb-2">Message</label>
        <textarea name="message" id="message" rows="5" required class="w-full bg-gray-50 border-transparent focus:border-black focus:ring-0 focus:bg-white transition py-3 px-4"></textarea>
    </div>

    <button type="submit" class="w-full bg-black text-white font-bold uppercase tracking-widest py-4 hover:bg-gray-800 transition">
        {{ $settings['submit_text'] ?? 'Send Message' }}
    </button>
</form>
