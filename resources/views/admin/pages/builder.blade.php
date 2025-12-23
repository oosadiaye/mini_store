@extends('admin.layout')

@section('content')
<div class="flex h-[calc(100vh-64px)] overflow-hidden bg-gray-100">
    <!-- Sidebar / Settings -->
    <div class="w-96 bg-white border-r border-gray-200 flex flex-col shadow-xl z-20">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Page Builder</h3>
                <p class="text-xs text-gray-500 truncate max-w-[150px]" title="{{ $page->title }}">{{ $page->title }}</p>
            </div>
            <a href="{{ route('storefront.page', $page->slug) }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 flex items-center">
                External <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        </div>
        
        <!-- Sections List (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <!-- Add New Section -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Add Component</h4>
                <form action="{{ route('admin.pages.sections.store', $page->id) }}" method="POST" class="grid grid-cols-2 gap-3">
                    @csrf
                    <button name="section_type" value="hero" class="group p-3 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 text-left transition flex flex-col items-center justify-center space-y-2">
                        <span class="text-2xl">🖼️</span>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-700">Hero</span>
                    </button>
                    <button name="section_type" value="text" class="group p-3 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 text-left transition flex flex-col items-center justify-center space-y-2">
                        <span class="text-2xl">📝</span>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-700">Text</span>
                    </button>
                    <button name="section_type" value="products" class="group p-3 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 text-left transition flex flex-col items-center justify-center space-y-2">
                        <span class="text-2xl">🛍️</span>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-700">Product Grid</span>
                    </button>
                    <button name="section_type" value="banner" class="group p-3 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 text-left transition flex flex-col items-center justify-center space-y-2">
                        <span class="text-2xl">📢</span>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-700">Banner</span>
                    </button>
                    <button name="section_type" value="product-slider" class="group p-3 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 text-left transition flex flex-col items-center justify-center space-y-2">
                        <span class="text-2xl">🎠</span>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-700">Prod. Slider</span>
                    </button>
                    <button name="section_type" value="recently-viewed" class="group p-3 border border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 text-left transition flex flex-col items-center justify-center space-y-2">
                        <span class="text-2xl">👁️</span>
                        <span class="text-xs font-medium text-gray-600 group-hover:text-indigo-700">Recently Viewed</span>
                    </button>
                </form>
            </div>

            <hr class="border-gray-100">

            <!-- Active Sections -->
            <div>
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Active Layers</h4>
                <div id="sections-list" class="space-y-3">
                    @foreach($page->sections as $section)
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden group" data-id="{{ $section->id }}">
                        <!-- Header / Handle -->
                        <div class="bg-gray-50 px-3 py-2 border-b border-gray-100 flex justify-between items-center cursor-move handle hover:bg-gray-100 transition">
                            <span class="text-xs font-semibold text-gray-700 flex items-center">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                {{ ucfirst($section->section_type) }}
                            </span>
                            <form action="{{ route('admin.pages.sections.delete', [$page->id, $section->id]) }}" method="POST" onsubmit="return confirm('Remove section?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500 transition p-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>

                        <!-- Edit Form -->
                        <div class="p-3 bg-white">
                            <form action="{{ route('admin.pages.sections.update', [$page->id, $section->id]) }}" method="POST" class="space-y-3" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                
                                <div class="relative">
                                    <input type="text" name="title" value="{{ $section->title }}" placeholder="Section Heading" class="w-full text-xs font-medium border-0 border-b border-gray-200 focus:border-indigo-500 focus:ring-0 px-0 py-1 bg-transparent placeholder-gray-300">
                                </div>

                                @if(in_array($section->section_type, ['hero', 'text', 'banner', 'products']))
                                <div>
                                    <textarea name="content" rows="2" placeholder="Content..." class="w-full text-xs rounded bg-gray-50 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 resize-none">{{ $section->content }}</textarea>
                                </div>
                                @endif

                                <!-- Type Specific Settings -->
                                @if($section->section_type === 'hero')
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" name="settings[button_text]" value="{{ $section->settings['button_text'] ?? '' }}" placeholder="Btn Text" class="text-xs rounded border-gray-200">
                                        <input type="color" name="settings[bg_color]" value="{{ $section->settings['bg_color'] ?? '#000000' }}" class="w-full h-8 p-0 border-0 rounded">
                                    </div>
                                    <div class="mb-2">
                                        <label class="text-[10px] text-gray-500 font-bold uppercase">Size</label>
                                        <select name="settings[hero_size]" class="w-full text-xs rounded border-gray-200">
                                            <option value="small" {{ ($section->settings['hero_size'] ?? '') == 'small' ? 'selected' : '' }}>Small (Compact)</option>
                                            <option value="medium" {{ ($section->settings['hero_size'] ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium (Standard)</option>
                                            <option value="large" {{ ($section->settings['hero_size'] ?? '') == 'large' ? 'selected' : '' }}>Large (Impact)</option>
                                            <option value="full" {{ ($section->settings['hero_size'] ?? '') == 'full' ? 'selected' : '' }}>Full Screen</option>
                                        </select>
                                    </div>
                                    <div class="space-y-1">
                                         <label class="text-[10px] text-gray-500 font-bold uppercase">Background Image</label>
                                         <input type="file" name="settings[hero_image]" class="text-xs w-full">
                                         @if(!empty($section->settings['hero_image']))
                                            <p class="text-[10px] text-green-600">Image uploaded</p>
                                         @endif
                                    </div>
                                @endif

                                @if(in_array($section->section_type, ['products', 'product-slider']))
                                    <div class="grid grid-cols-2 gap-2">
                                         <div class="col-span-1">
                                            <label class="text-[10px] text-gray-500 font-bold uppercase">Limit</label>
                                            <input type="number" name="settings[limit]" value="{{ $section->settings['limit'] ?? 8 }}" class="w-full text-xs rounded border-gray-200">
                                         </div>
                                         @if($section->section_type === 'products')
                                         <div class="col-span-1">
                                            <label class="text-[10px] text-gray-500 font-bold uppercase">Columns</label>
                                            <select name="settings[columns]" class="w-full text-xs rounded border-gray-200">
                                                <option value="2" {{ ($section->settings['columns'] ?? '4') == '2' ? 'selected' : '' }}>2 Cols</option>
                                                <option value="3" {{ ($section->settings['columns'] ?? '4') == '3' ? 'selected' : '' }}>3 Cols</option>
                                                <option value="4" {{ ($section->settings['columns'] ?? '4') == '4' ? 'selected' : '' }}>4 Cols</option>
                                                <option value="5" {{ ($section->settings['columns'] ?? '4') == '5' ? 'selected' : '' }}>5 Cols</option>
                                            </select>
                                         </div>
                                         @endif
                                    </div>
                                @endif

                                @if($section->section_type === 'recently-viewed')
                                     <div class="col-span-1">
                                        <label class="text-[10px] text-gray-500 font-bold uppercase">Count</label>
                                        <input type="number" name="settings[limit]" value="{{ $section->settings['limit'] ?? 4 }}" class="w-full text-xs rounded border-gray-200">
                                     </div>
                                @endif

                                <div class="flex justify-end">
                                    <button type="submit" class="text-xs bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded hover:bg-indigo-100 transition font-medium">
                                        Apply Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Live Preview -->
    <div class="flex-1 bg-gray-200 flex flex-col relative">
        <div class="bg-white border-b border-gray-200 p-2 flex justify-center items-center text-xs text-gray-500 space-x-4 shadow-sm z-10">
            <span class="flex items-center"><div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div> Live Editing Mode</span>
        </div>
        
        <div class="flex-1 overflow-hidden relative p-8 flex justify-center items-start">
            <div class="w-full h-full bg-white shadow-2xl rounded-lg overflow-hidden max-w-[1200px] transition-all duration-300" id="preview-frame-container">
                <iframe id="preview-frame" src="{{ route('storefront.page', $page->slug) }}?editor=true" class="w-full h-full border-0"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Initialize Sortable
    new Sortable(document.getElementById('sections-list'), {
        animation: 150,
        handle: '.handle',
        onEnd: function (evt) {
            const order = [];
            document.querySelectorAll('#sections-list > div').forEach((el, index) => {
                order.push(el.dataset.id);
            });

            // Send reorder request
            fetch('{{ route('admin.pages.reorder', $page->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            }).then(() => {
                document.getElementById('preview-frame').contentWindow.location.reload();
            });
        }
    });

    // Auto-reload preview on form submit
    document.querySelectorAll('form').forEach(form => {
        // We let normal submit happen for now to keep it simple, 
        // effectively reloading the whole page (Sidebar + Iframe).
        // For a smoother experience, we'd use fetch() here.
    });
</script>
@endsection
