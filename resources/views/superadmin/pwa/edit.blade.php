@extends('layouts.superadmin')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Central Admin PWA Branding
    </h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <form action="{{ route('superadmin.pwa.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- App Name -->
                <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">App Name</span>
                    <input name="pwa_admin_name" 
                           value="{{ $settings['pwa_admin_name'] ?? 'MiniStore Admin' }}"
                           class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                           placeholder="e.g. MiniStore Admin"/>
                    <span class="text-xs text-gray-500">Name displayed on splash screen and install prompt.</span>
                </label>

                <!-- Short Name -->
                <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Short Name</span>
                    <input name="pwa_admin_short_name" 
                           value="{{ $settings['pwa_admin_short_name'] ?? 'Admin' }}"
                           class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" 
                           placeholder="e.g. Admin"/>
                     <span class="text-xs text-gray-500">Name displayed on home screen (max 12 chars recommended).</span>
                </label>

                <!-- Theme Color -->
                <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Theme Color</span>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="color" name="pwa_admin_theme_color" 
                               value="{{ $settings['pwa_admin_theme_color'] ?? '#4f46e5' }}"
                               class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <input type="text" readonly value="{{ $settings['pwa_admin_theme_color'] ?? '#4f46e5' }}" class="text-sm text-gray-600 dark:text-gray-400 uppercase">
                    </div>
                </label>

                <!-- Background Color -->
                <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Background Color</span>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="color" name="pwa_admin_bg_color" 
                               value="{{ $settings['pwa_admin_bg_color'] ?? '#ffffff' }}"
                               class="h-10 w-20 rounded border border-gray-300 cursor-pointer">
                        <input type="text" readonly value="{{ $settings['pwa_admin_bg_color'] ?? '#ffffff' }}" class="text-sm text-gray-600 dark:text-gray-400 uppercase">
                    </div>
                </label>
            </div>

            <!-- Icon Upload -->
            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">App Icon (512x512 PNG)</span>
                <input type="file" name="pwa_admin_icon" accept="image/png"
                       class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"/>
                
                @if(isset($settings['pwa_admin_icon']))
                    <div class="mt-2">
                        <p class="text-xs text-gray-500 mb-1">Current Icon:</p>
                        <img src="{{ Storage::disk('public')->url($settings['pwa_admin_icon']) }}" class="w-16 h-16 rounded shadow-sm border">
                    </div>
                @endif
            </label>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Save Branding
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
