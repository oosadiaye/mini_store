<x-storefront.layout :config="$config" :menuCategories="$menuCategories">
    <div class="bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-10">
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-4">
                        {{ $page_title }}
                    </h1>

                    <div class="prose prose-indigo max-w-none text-gray-700">
                        @if(!empty($content))
                            {!! $content !!}
                        @else
                            <div class="text-center py-10">
                                <p class="text-gray-500 italic">This policy has not been updated yet.</p>
                                <a href="{{ route('storefront.home', ['tenant' => app('tenant')->slug]) }}" class="inline-block mt-4 text-indigo-600 hover:text-indigo-800 font-medium">
                                    &larr; Return to Home
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-storefront.layout>
