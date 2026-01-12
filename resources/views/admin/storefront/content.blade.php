<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Storefront Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
@php
    $routes = [
        "update" => route("admin.store-content.update", ["tenant" => app("tenant")->slug]),
        "regenerate" => route("admin.store-content.regenerate", ["tenant" => app("tenant")->slug]),
        "generate_policy" => route("admin.store-content.generate-policy", ["tenant" => app("tenant")->slug]),
        "media_base" => route("tenant.media", ["tenant" => app("tenant")->slug])
    ];
@endphp

            <store-content
                :initial-content='@json($content ?? [])'
                :initial-contact='@json($contact ?? [])'
                :initial-split-banner='@json($splitBanner ?? [])'
                :initial-about-us='@json($aboutUs ?? [])'
                :initial-policies='@json($policies ?? [])'
                :show-new-arrivals='@json($showNewArrivals ?? false)'
                :show-best-sellers='@json($showBestSellers ?? false)'
                :config='@json($config ?? [])'
                :routes='@json($routes)'
            ></store-content>

             <!-- REGENERATE SECTION (Kept outside Vue if preferred, or could be moved inside. Logic was simple form submit) -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-12">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-gray-900">Reset to Industry Defaults</h4>
                        <p class="text-sm text-gray-500 mt-1">Made a mistake? Re-apply the AI-generated content for your niche.</p>
                    </div>
                    <form action="{{ route('admin.store-content.regenerate', ['tenant' => app('tenant')->slug]) }}" method="POST" onsubmit="return confirm('Are you sure? This will overwrite your current titles and images.');">
                        @csrf
                        <button type="submit" class="bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-red-600 font-medium py-2 px-4 rounded-lg shadow-sm transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Regenerate from Niche
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
