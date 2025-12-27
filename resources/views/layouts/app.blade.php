<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        @if(session('superadmin_impersonator_id'))
            <div class="bg-indigo-600 px-4 py-3 text-white">
                <div class="flex items-center justify-between max-w-7xl mx-auto">
                    <p class="text-sm font-medium">
                        Using {{ config('app.name') }} as <span class="font-bold underline">{{ Auth::user()->name }}</span> (Impersonation Mode)
                    </p>
                    <form action="{{ route('superadmin.stop-impersonation') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-indigo-100 text-indigo-700 hover:bg-indigo-200 px-3 py-1 rounded-md text-xs font-semibold uppercase tracking-wide transition">
                            Back to SuperAdmin
                        </button>
                    </form>
                </div>
            </div>
        @endif
        <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.sidebar')

            <div class="flex-1 flex flex-col overflow-hidden">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900">
                    {{ $slot }}
                </main>
            </div>
        </div>
    
        @include('components.chat-widget')

        <!-- Onboarding / Announcement Modal -->
        @if(isset($sharedOnboarding) && $sharedOnboarding)
            <div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        {{ $sharedOnboarding->title }}
                                    </h3>
                                    <div class="mt-4">
                                        @if($sharedOnboarding->attachment_type === 'video' && $sharedOnboarding->attachment_path)
                                            <div class="aspect-w-16 aspect-h-9 mb-4">
                                                <video controls autoplay class="w-full rounded-lg shadow-sm">
                                                    <source src="{{ Storage::url($sharedOnboarding->attachment_path) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        @elseif($sharedOnboarding->attachment_type === 'image' && $sharedOnboarding->attachment_path)
                                            <img src="{{ Storage::url($sharedOnboarding->attachment_path) }}" alt="Onboarding" class="w-full rounded-lg shadow-sm mb-4">
                                        @endif
                                        
                                        <div class="text-sm text-gray-600 space-y-2 whitespace-pre-wrap">{{ $sharedOnboarding->content }}</div>

                                        @if($sharedOnboarding->action_url)
                                            <div class="mt-4">
                                                <a href="{{ $sharedOnboarding->action_url }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    Learn More &rarr;
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="show = false; markAnnouncementRead({{ $sharedOnboarding->id }})" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Got it, thanks!
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <script>
            function markAnnouncementRead(id) {
                fetch(`/admin/announcements/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        // Optional: remove badge or update UI if needed dynamically
                    }
                }).catch(error => console.error('Error marking as read:', error));
            }
        </script>
    </body>
</html>
