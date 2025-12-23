@php
    $settings = $section->settings ?? [];
    $bgColor = $settings['background_color'] ?? '#f9fafb';
    $bgImage = $settings['background_image'] ?? '';
    $textColor = $settings['text_color'] ?? '#111827';
@endphp

<section id="{{ $section_id ?? '' }}" class="relative py-16 overflow-hidden" style="background-color: {{ $bgColor }};">
    @if($bgImage)
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('{{ $bgImage }}');"></div>
    @endif

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold tracking-tight sm:text-4xl" style="color: {{ $textColor }}">
                {{ $section->title ?? 'Subscribe to our newsletter' }}
            </h2>
            <p class="mt-4 text-lg leading-6 text-gray-500">
                {{ $section->content ?? 'The latest news, articles, and resources, sent to your inbox weekly.' }}
            </p>
            
            <form class="mt-8 sm:flex justify-center" action="#" method="POST" onsubmit="event.preventDefault(); alert('Newsletter subscription verified (Demo)');">
                <label for="email-address" class="sr-only">Email address</label>
                <input id="email-address" name="email" type="email" autocomplete="email" required class="w-full px-5 py-3 placeholder-gray-500 focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs border-gray-300 rounded-md shadow-sm" placeholder="Enter your email">
                <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                    <button type="submit" class="w-full flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ $settings['button_text'] ?? 'Subscribe' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
