@props(['heroData', 'featuredProducts', 'categorySections'])

<div class="bg-gray-100 min-h-screen">
    {{-- Hero Banner --}}
    @if(!empty($heroData))
        <div class="relative bg-gray-900 overflow-hidden">
            {{-- Background Image --}}
            @if(isset($heroData['banner_image']) && $heroData['banner_image'])
                <div class="absolute inset-0">
                    <img src="{{ Str::startsWith($heroData['banner_image'], 'http') ? $heroData['banner_image'] : route('tenant.media', ['path' => $heroData['banner_image']]) }}" 
                         alt="Hero Banner" 
                         class="w-full h-full object-cover opacity-60">
                </div>
            @endif
            
            <div class="relative max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 text-center">
                <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl mb-4">
                    {{ $heroData['title'] }}
                </h1>
                <p class="mt-2 text-lg text-gray-300 max-w-3xl mx-auto">
                    {{ $heroData['subtitle'] }}
                </p>
            </div>
        </div>
    @endif

    {{-- Dense Header / Search Focus --}}
    <div class="bg-[color:var(--brand-color)] text-white py-4 shadow-md sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            {{-- Title Hidden if Hero Present to save space, or keep for branding? keeping for sticky context --}}
            <div class="font-bold text-xl">{{ $heroData['title'] ?? 'Store' }}</div>
            <div class="flex-1 mx-8">
                <input type="text" placeholder="Search thousands of products..." class="w-full rounded bg-white/10 border-0 text-white placeholder-gray-300 focus:ring-2 focus:ring-white/50">
            </div>
            <div>
                 <span class="text-sm font-medium">Bulk Savings Available</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex gap-6">
        
        {{-- Sidebar Navigation (Categories) --}}
        <div class="hidden lg:block w-64 flex-shrink-0">
             <div class="bg-white shadow rounded-lg p-4">
                <h3 class="font-bold text-gray-900 mb-4 pb-2 border-b">Categories</h3>
                <ul class="space-y-2">
                    @foreach($categorySections as $section)
                        <li>
                            <a href="#category-{{ $section['category_id'] }}" class="text-gray-600 hover:text-[color:var(--brand-color)] flex justify-between items-center group">
                                {{ $section['category_name'] }}
                                <span class="bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs group-hover:bg-gray-200">
                                    {{ count($section['products']) }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
             <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800 font-medium">⚡ Flash Deals</p>
                <p class="text-xs text-yellow-600 mt-1">Order within 2h for same-day delivery.</p>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="flex-1">
            {{-- Featured Deals Grid --}}
            @if($featuredProducts->isNotEmpty())
                <div class="bg-white rounded-lg shadow mb-8 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                        <h2 class="text-lg font-bold text-gray-900">🔥 Hot Deals</h2>
                        <a href="#" class="text-sm text-[color:var(--brand-color)] hover:underline">View All</a>
                    </div>
                    <div class="p-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach($featuredProducts->take(10) as $product)
                            <div class="border rounded-md p-3 hover:shadow-md transition-shadow bg-white text-center">
                                <div class="h-32 mb-2 flex items-center justify-center">
                                    <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" class="max-h-full max-w-full object-contain">
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 line-clamp-2 h-10 mb-1 leading-snug">{{ $product['name'] }}</h3>
                                <div class="text-[color:var(--brand-color)] font-bold">${{ number_format($product['price'], 2) }}</div>
                                <button class="mt-2 w-full bg-gray-100 text-gray-800 text-xs font-semibold py-1 px-2 rounded hover:bg-gray-200">Add</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Category Sections (Dense) --}}
            @foreach($categorySections as $section)
                <div id="category-{{ $section['category_id'] }}" class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-3 border-b border-gray-200 font-bold text-gray-800">
                        {{ $section['category_name'] }}
                    </div>
                     <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        @forelse($section['products'] as $product)
                            <div class="flex flex-col border rounded p-2 hover:border-[color:var(--brand-color)] transition-colors">
                                 <div class="h-24 flex items-center justify-center mb-2 bg-gray-50 rounded">
                                     <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" class="max-h-full max-w-full p-1 mix-blend-multiply">
                                 </div>
                                 <div class="flex-1">
                                     <div class="text-xs text-gray-500 mb-0.5">{{ $section['category_name'] }}</div>
                                     <div class="text-sm font-medium text-gray-900 leading-tight mb-1">{{ $product['name'] }}</div>
                                 </div>
                                 <div class="mt-2 flex items-center justify-between">
                                     <span class="font-bold text-gray-900">${{ number_format($product['price'], 2) }}</span>
                                     <button class="bg-[color:var(--brand-color)] text-white p-1 rounded hover:bg-opacity-90">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                              <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                                         </svg>
                                     </button>
                                 </div>
                            </div>
                        @empty
                             <div class="col-span-full text-center text-gray-500 py-4 text-sm">No products available in this section.</div>
                        @endforelse
                     </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
