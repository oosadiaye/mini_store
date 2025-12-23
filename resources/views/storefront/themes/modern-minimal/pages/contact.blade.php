@extends('storefront.themes.modern-minimal.layout')

@php
    $settings = \App\Models\ThemeSetting::getSettings();
    $contact = $settings['contact'] ?? [];
@endphp

@section('pageTitle', $contact['title'] ?? 'Contact Us')

@section('content')
    @include('storefront.themes.modern-minimal.components.page-header', [
        'title' => $contact['title'] ?? 'Contact Us', 
        'subtitle' => $contact['subtitle'] ?? 'We would love to hear from you.', 
        'breadcrumbs' => ['Contact' => '#']
    ])

    <div class="container mx-auto px-4 py-16 md:py-24 max-w-6xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
            
            {{-- Contact Info --}}
            <div>
                <h2 class="text-2xl font-serif font-medium mb-8">Get in Touch</h2>
                
                <div class="space-y-8 text-lg font-light text-gray-600">
                    @if(!empty($contact['contact_info']['address']))
                    <div class="flex items-start gap-4">
                        <svg class="w-6 h-6 text-black mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <p>{!! nl2br(e($contact['contact_info']['address'])) !!}</p>
                    </div>
                    @endif

                    @if(!empty($contact['contact_info']['email']))
                    <div class="flex items-center gap-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <a href="mailto:{{ $contact['contact_info']['email'] }}" class="hover:underline hover:text-black transition">{{ $contact['contact_info']['email'] }}</a>
                    </div>
                    @endif

                    @if(!empty($contact['contact_info']['phone']))
                    <div class="flex items-center gap-4">
                        <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <a href="tel:{{ $contact['contact_info']['phone'] }}" class="hover:underline hover:text-black transition">{{ $contact['contact_info']['phone'] }}</a>
                    </div>
                    @endif
                </div>

                {{-- Map --}}
                @if(!empty($contact['map']['enabled']) && !empty($contact['map']['embed_url']))
                    <div class="mt-12 h-64 bg-gray-100 rounded-sm overflow-hidden">
                        <iframe src="{{ $contact['map']['embed_url'] }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                @endif
            </div>

            {{-- Form --}}
            <div class="bg-gray-50 p-8 md:p-10 rounded-sm">
                <h3 class="font-serif text-xl font-medium mb-6">Send a Message</h3>
                @include('storefront.themes.modern-minimal.components.contact-form', ['settings' => $contact['form'] ?? []])
            </div>
        </div>
    </div>
@endsection
