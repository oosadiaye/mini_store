@extends('admin.layout')

@push('styles')
<style>
    .theme-scroll-preview {
        background-size: 100% auto;
        background-position: top center;
        transition: background-position 3s ease-in-out;
    }
    .theme-scroll-preview:hover {
        background-position: bottom center;
    }
</style>
@endpush

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Theme Customization</h2>
        <p class="text-gray-600">Customize the look and feel of your storefront.</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('storefront.home') }}" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition border border-gray-300 flex items-center">
            <span class="mr-2">👁️</span> Live Preview
        </a>
        <button type="submit" form="theme-form" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition shadow-md">
            Save Changes
        </button>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
    <p>{{ session('success') }}</p>
</div>
@endif

<form id="theme-form" action="{{ route('admin.theme.update') }}" method="POST" class="space-y-12">
    @csrf
    <input type="hidden" name="template_id" value="{{ $currentSettings->template_id }}">
    
    <!-- Section 1: Themes (Full Width) -->
    <section>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                Choose a Theme
            </h3>
            <span class="text-sm text-gray-500">Select a design base for your store</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @php
                $placeholders = [
                    'modern-minimal' => 'https://images.unsplash.com/photo-1481480746807-0a2c98d63309?auto=format&fit=crop&q=80&w=400&h=1200',
                    'elegant-boutique' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=400&h=1200',
                    'tech-geeks' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=400&h=1200',
                    'organic-fresh' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?auto=format&fit=crop&q=80&w=400&h=1200',
                    'midnight-luxury' => 'https://images.unsplash.com/photo-1492707892479-7bc8d5a49ca7?auto=format&fit=crop&q=80&w=400&h=1200',
                    'vibrant-pop' => 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&q=80&w=400&h=1200',
                    'classic-trade' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&q=80&w=400&h=1200',
                ];
            @endphp

            @foreach($templates as $template)
            @php
                $isActive = $activeSettings && $activeSettings->template_id == $template->id;
                $isEditing = $currentSettings->template_id == $template->id;
            @endphp
            <div class="bg-white rounded-xl shadow border {{ $isEditing ? 'border-indigo-500 ring-2 ring-indigo-500 ring-opacity-50' : 'border-gray-200' }} flex flex-col overflow-hidden transition hover:shadow-lg h-full relative group">
                
                <!-- Dynamic Visual Preview -->
                <div class="h-64 bg-gray-50 relative overflow-hidden group-hover:translate-y-[-10px] transition-transform duration-500 cursor-default select-none flex flex-col">
                    @php
                        $colors = $template->default_settings['colors'] ?? ['primary' => '#4f46e5', 'secondary' => '#1f2937'];
                        $headerStyle = $template->default_settings['layout_settings']['header_style'] ?? 'modern';
                        $pC = $colors['primary']; // Primary
                        $sC = $colors['secondary']; // Secondary
                    @endphp

                    <!-- Mock Header -->
                    @if($headerStyle === 'modern')
                        {{-- Modern: Gradient/White Sticky --}}
                        <div class="h-10 w-full flex items-center justify-between px-4 shadow-sm z-10 shrink-0" style="background: linear-gradient(to right, #ffffff, {{$pC}}08);">
                            <div class="flex items-center gap-2">
                                <div class="w-5 h-5 rounded bg-gradient-to-br" style="background-image: linear-gradient(to bottom right, {{$pC}}, {{$sC}});"></div>
                                <div class="h-2 w-16 bg-gray-200 rounded"></div>
                            </div>
                            <div class="flex gap-2">
                                <div class="h-1.5 w-6 bg-gray-200 rounded"></div>
                                <div class="h-1.5 w-6 bg-gray-200 rounded"></div>
                                <div class="h-1.5 w-6 bg-gray-200 rounded"></div>
                            </div>
                            <div class="flex gap-2">
                                <div class="w-4 h-4 rounded-full bg-gray-100"></div>
                            </div>
                        </div>
                    @elseif($headerStyle === 'classic')
                        {{-- Classic: Top Bar + Centered --}}
                        <div class="h-3 w-full shrink-0" style="background-color: {{$sC}};"></div>
                        <div class="h-14 w-full bg-white flex flex-col items-center justify-center border-b border-gray-100 shadow-sm z-10 shrink-0 px-4">
                            <div class="w-6 h-6 rounded-full mb-1" style="background-color: {{$pC}};"></div>
                            <div class="flex gap-3 mt-1">
                                <div class="h-1 w-6 bg-gray-200 rounded"></div>
                                <div class="h-1 w-6 bg-gray-200 rounded"></div>
                                <div class="h-1 w-6 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    @elseif($headerStyle === 'minimal')
                        {{-- Minimal: Hamburger + Logo --}}
                        <div class="h-12 w-full bg-white border-b border-gray-100 flex items-center justify-between px-4 z-10 shrink-0">
                            <div class="w-4 h-4 rounded bg-gray-200"></div> <!-- Hamburger -->
                            <div class="flex items-center gap-1">
                                <div class="w-4 h-4 rounded" style="background-color: {{$pC}};"></div>
                                <div class="h-2 w-12 bg-gray-200 rounded"></div>
                            </div>
                            <div class="w-4 h-4 rounded-full bg-gray-100"></div>
                        </div>
                    @else
                        {{-- Default/Unknown --}}
                        <div class="h-10 w-full bg-white border-b border-gray-100 shadow-sm z-10 shrink-0 px-4 flex items-center justify-between">
                            <div class="h-3 w-20 " style="background-color: {{$pC}};"></div>
                            <div class="flex gap-2">
                                <div class="h-2 w-8 bg-gray-200"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Mock Hero Area -->
                    <div class="flex-1 bg-gray-50 relative p-3 overflow-hidden">
                        <div class="bg-white rounded shadow-sm w-full h-32 mb-3 flex items-center justify-center overflow-hidden relative">
                             <div class="absolute inset-0 opacity-40 bg-cover bg-center" style="background-image: url('{{ $placeholders[$template->slug] ?? '' }}'); mix-blend-mode: multiply;"></div>
                             <div class="relative z-10 text-center bg-white/90 backdrop-blur-sm px-4 py-3 rounded-lg shadow-sm">
                                 <div class="h-2 w-20 bg-gray-800 rounded mb-1.5 mx-auto"></div>
                                 <div class="h-1.5 w-12 rounded mx-auto" style="background-color: {{$pC}};"></div>
                             </div>
                        </div>
                        <!-- Mock Products Grid -->
                        <div class="grid grid-cols-2 gap-3 opacity-60">
                            <div class="h-24 rounded bg-white shadow-sm border border-gray-100 flex flex-col p-2">
                                <div class="flex-1 bg-gray-100 rounded mb-1"></div>
                                <div class="h-1.5 w-12 bg-gray-200 mb-1"></div>
                                <div class="h-1.5 w-8 bg-gray-300"></div>
                            </div>
                            <div class="h-24 rounded bg-white shadow-sm border border-gray-100 flex flex-col p-2">
                                <div class="flex-1 bg-gray-100 rounded mb-1"></div>
                                <div class="h-1.5 w-12 bg-gray-200 mb-1"></div>
                                <div class="h-1.5 w-8 bg-gray-300"></div>
                            </div>
                        </div>
                        
                        <!-- Overlay for Active State -->
                        @if($isActive)
                            <div class="absolute top-2 right-2 bg-green-500 text-white text-[9px] uppercase tracking-wider px-2 py-0.5 rounded-full font-bold shadow-md z-20">Active</div>
                        @else
                           <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 transition duration-300 z-0"></div>
                        @endif
                        
                        @if($isEditing && !$isActive)
                            <div class="absolute top-2 left-2 bg-indigo-500 text-white text-[9px] uppercase tracking-wider px-2 py-0.5 rounded-full font-bold shadow-md z-20">Editing</div>
                        @endif
                    </div>
                </div>
                
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-lg text-gray-900 leading-tight">{{ $template->name }}</h4>
                        @if($template->is_premium)
                            <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded uppercase tracking-wide border border-indigo-100">Premium</span>
                        @else
                            <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded uppercase tracking-wide border border-gray-200">Free</span>
                        @endif
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-6 flex-1 leading-relaxed">{{ $template->description }}</p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-50">
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Customize Button (New) -->
                            @if($isEditing)
                                <button type="button" disabled class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg cursor-default">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Editing
                                </button>
                            @else
                                <a href="{{ route('admin.theme.index', ['edit_theme' => $template->slug]) }}" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Customize
                                </a>
                            @endif

                            <!-- Activate Button -->
                            @if($isActive)
                                <button type="button" disabled class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg cursor-default opacity-75">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Active
                                </button>
                            @else
                                <form action="{{ route('admin.theme.activate') }}" method="POST" class="w-full">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                                    <button type="submit" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition">
                                        Activate
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Delete Button (Only if inactive) -->
                            @if(!$isActive)
                                <form action="{{ route('admin.theme.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this theme? This cannot be undone.');" class="col-span-2 border-t border-gray-100 pt-3 mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center justify-center w-full px-4 py-2 text-xs font-medium text-red-500 hover:text-red-700 bg-white hover:bg-red-50 border border-transparent hover:border-red-200 rounded transition">
                                         <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                         Delete Theme
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <hr class="border-gray-200">

    <!-- Section 2: Customization (Split Layout) -->
    <section>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800 flex items-center">
                <span class="bg-gray-100 text-gray-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                Fine-tune Settings
            </h3>
            <span class="text-sm text-gray-500">Customize {{ $currentSettings->template->name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Forms -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- Layout & Features (New) -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                     <h3 class="text-lg font-semibold text-gray-800 mb-4">Layout & Features</h3>
                     
                     <!-- Sections Reordering (Drag & Drop) -->
                     <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Homepage Structure (Drag to Reorder)</label>
                        <ul id="sortable-sections" class="space-y-2">
                             @php 
                                $sections = $currentSettings->layout_settings['sections'] ?? [];
                                $order = $currentSettings->layout_settings['section_order'] ?? [
                                    'home_top', 'home_hero', 'home_features', 'home_categories', 'home_new_arrivals', 
                                    'home_promo', 'home_testimonials', 'home_video', 'home_newsletter'
                                ];
                                
                                // Map for human labels and config keys
                                $sectionMap = [
                                    'home_top' => ['label' => 'Top Announcement Bar', 'key' => 'top_bar'],
                                    'home_hero' => ['label' => 'Hero Slider', 'key' => 'hero'],
                                    'home_features' => ['label' => 'Feature Grid', 'key' => 'features'],
                                    'home_categories' => ['label' => 'Categories Grid', 'key' => 'categories'],
                                    'home_new_arrivals' => ['label' => 'New Arrivals', 'key' => 'new_arrivals'],
                                    'home_promo' => ['label' => 'Promo / Breakout', 'key' => 'promo'],
                                    'home_testimonials' => ['label' => 'Testimonials', 'key' => 'testimonials'],
                                    'home_video' => ['label' => 'Video Section', 'key' => 'video'],
                                    'home_newsletter' => ['label' => 'Newsletter', 'key' => 'newsletter'],
                                ];
                                
                                // Merge any new keys properly if missing from saved order
                                $allKeys = array_keys($sectionMap);
                                $currentOrder = array_intersect($order, $allKeys); // clean invalid
                                $missing = array_diff($allKeys, $currentOrder);
                                $displayOrder = array_merge($currentOrder, $missing);
                            @endphp

                            @foreach($displayOrder as $sectionId)
                                @php $info = $sectionMap[$sectionId]; @endphp
                                <li class="flex items-center bg-gray-50 border border-gray-200 rounded-md p-2 group cursor-move hover:border-indigo-300 transition">
                                    <input type="hidden" name="layout_settings[section_order][]" value="{{ $sectionId }}">
                                    <!-- Handle -->
                                    <div class="mr-3 text-gray-400 group-hover:text-indigo-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                    </div>
                                    <!-- Checkbox -->
                                    <div class="mr-3" onclick="event.stopPropagation()">
                                        <input type="checkbox" name="layout_settings[sections][{{ $info['key'] }}]" value="1" {{ ($sections[$info['key']] ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    <!-- Label -->
                                    <span class="text-sm text-gray-700 font-medium select-none">{{ $info['label'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-xs text-gray-500 mt-2">Uncheck to hide sections. Drag to reorder.</p>
                     </div>

                     <!-- Header Style -->
                     <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Header Style</label>
                        <select name="layout_settings[header_style]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="sticky" {{ ($currentSettings->layout_settings['header_style'] ?? 'sticky') == 'sticky' ? 'selected' : '' }}>Sticky (Default)</option>
                            <option value="transparent" {{ ($currentSettings->layout_settings['header_style'] ?? '') == 'transparent' ? 'selected' : '' }}>Transparent Overlay</option>
                            <option value="boxed" {{ ($currentSettings->layout_settings['header_style'] ?? '') == 'boxed' ? 'selected' : '' }}>Boxed & Centered</option>
                        </select>
                     </div>

                     <!-- Visuals -->
                     <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Button Radius</label>
                        <div class="flex items-center space-x-2">
                            <input type="range" name="layout_settings[visuals][radius]" min="0" max="50" value="{{ $currentSettings->layout_settings['visuals']['radius'] ?? 8 }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <span class="text-xs text-gray-500 w-8">{{ $currentSettings->layout_settings['visuals']['radius'] ?? 8 }}px</span>
                        </div>
                     </div>
                      <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shadow Intensity</label>
                        <select name="layout_settings[visuals][shadow]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="none" {{ ($currentSettings->layout_settings['visuals']['shadow'] ?? '') == 'none' ? 'selected' : '' }}>Flat (None)</option>
                            <option value="sm" {{ ($currentSettings->layout_settings['visuals']['shadow'] ?? 'sm') == 'sm' ? 'selected' : '' }}>Subtle (Small)</option>
                            <option value="md" {{ ($currentSettings->layout_settings['visuals']['shadow'] ?? '') == 'md' ? 'selected' : '' }}>Standard (Medium)</option>
                            <option value="lg" {{ ($currentSettings->layout_settings['visuals']['shadow'] ?? '') == 'lg' ? 'selected' : '' }}>Deep (Large)</option>
                            <option value="xl" {{ ($currentSettings->layout_settings['visuals']['shadow'] ?? '') == 'xl' ? 'selected' : '' }}>Floating (Extra Large)</option>
                        </select>
                     </div>
                </div>

                <!-- Colors -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Brand Colors</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Primary Color</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="colors[primary]" value="{{ $currentSettings->colors['primary'] ?? '#4f46e5' }}" class="h-10 w-10 rounded border border-gray-300 cursor-pointer">
                                <input type="text" name="colors[primary]" value="{{ $currentSettings->colors['primary'] ?? '#4f46e5' }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Secondary Color</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="colors[secondary]" value="{{ $currentSettings->colors['secondary'] ?? '#1f2937' }}" class="h-10 w-10 rounded border border-gray-300 cursor-pointer">
                                <input type="text" name="colors[secondary]" value="{{ $currentSettings->colors['secondary'] ?? '#1f2937' }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Accent Color</label>
                            <div class="flex items-center space-x-2">
                                <input type="color" name="colors[accent]" value="{{ $currentSettings->colors['accent'] ?? '#fbbf24' }}" class="h-10 w-10 rounded border border-gray-300 cursor-pointer">
                                <input type="text" name="colors[accent]" value="{{ $currentSettings->colors['accent'] ?? '#fbbf24' }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fonts -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Typography</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Heading Font</label>
                            <select name="fonts[heading]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="Inter" {{ ($currentSettings->fonts['heading'] ?? '') == 'Inter' ? 'selected' : '' }}>Inter (Clean)</option>
                                <option value="Merriweather" {{ ($currentSettings->fonts['heading'] ?? '') == 'Merriweather' ? 'selected' : '' }}>Merriweather (Serif)</option>
                                <option value="Oswald" {{ ($currentSettings->fonts['heading'] ?? '') == 'Oswald' ? 'selected' : '' }}>Oswald (Bold)</option>
                                <option value="Playfair Display" {{ ($currentSettings->fonts['heading'] ?? '') == 'Playfair Display' ? 'selected' : '' }}>Playfair Display (Elegant)</option>
                                <option value="Poppins" {{ ($currentSettings->fonts['heading'] ?? '') == 'Poppins' ? 'selected' : '' }}>Poppins (Modern)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Body Font</label>
                            <select name="fonts[body]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="Inter" {{ ($currentSettings->fonts['body'] ?? '') == 'Inter' ? 'selected' : '' }}>Inter (Clean)</option>
                                <option value="Roboto" {{ ($currentSettings->fonts['body'] ?? '') == 'Roboto' ? 'selected' : '' }}>Roboto (Neutral)</option>
                                <option value="Open Sans" {{ ($currentSettings->fonts['body'] ?? '') == 'Open Sans' ? 'selected' : '' }}>Open Sans (Friendly)</option>
                                <option value="Lato" {{ ($currentSettings->fonts['body'] ?? '') == 'Lato' ? 'selected' : '' }}>Lato (Clean)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Footer Customization -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Footer Settings</h3>
                    
                    <div class="space-y-4">
                        <!-- About Text -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">About Us Text</label>
                            <textarea name="layout_settings[footer][about]" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $currentSettings->layout_settings['footer']['about'] ?? 'Your trusted online store for quality products.' }}</textarea>
                        </div>

                        <!-- Copyright -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Copyright Text</label>
                            <input type="text" name="layout_settings[footer][copyright]" value="{{ $currentSettings->layout_settings['footer']['copyright'] ?? 'All rights reserved.' }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>

                        <!-- Social Media -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Social Links</label>
                            <div class="space-y-2">
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">FB</span>
                                    <input type="url" name="layout_settings[footer][social][facebook]" placeholder="https://facebook.com/..." value="{{ $currentSettings->layout_settings['footer']['social']['facebook'] ?? '' }}" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">TW</span>
                                    <input type="url" name="layout_settings[footer][social][twitter]" placeholder="https://twitter.com/..." value="{{ $currentSettings->layout_settings['footer']['social']['twitter'] ?? '' }}" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">IG</span>
                                    <input type="url" name="layout_settings[footer][social][instagram]" placeholder="https://instagram.com/..." value="{{ $currentSettings->layout_settings['footer']['social']['instagram'] ?? '' }}" class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="lg:col-span-2">
                 <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                    <div class="bg-gray-50 border-b border-gray-200 px-4 py-2 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Style Preview <span class="text-xs text-gray-400 font-normal ml-2">(Live Updates)</span></span>
                        <div class="flex space-x-1">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                    </div>
                    <div class="bg-gray-100 relative">
                        <iframe id="theme-preview-frame" src="{{ route('storefront.home') }}?preview_template_id={{ $currentSettings->template_id }}" class="w-full h-[600px] border-0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom CSS (Separate Section) -->
    <section>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Advanced: Custom CSS</h3>
            <textarea name="custom_css" rows="6" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono text-xs">{{ $currentSettings->custom_css }}</textarea>
            <p class="mt-2 text-xs text-gray-500">Overrides apply to all themes.</p>
        </div>
    </section>

</form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    function previewTheme(templateId) {
        // Update URL to include preview_template_id
        const baseUrl = '{{ route('storefront.home') }}';
        const iframe = document.getElementById('theme-preview-frame');
        
        // Add loading state
        iframe.style.opacity = '0.5';
        
        // Scroll to preview so user sees it
        iframe.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Reload iframe
        iframe.src = `${baseUrl}?preview_template_id=${templateId}`;
        
        iframe.onload = function() {
            iframe.style.opacity = '1';
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const iframe = document.getElementById('theme-preview-frame');
        const form = document.getElementById('theme-form');

        // Watch for changes on inputs
        form.addEventListener('input', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') {
                const setting = e.target.name;
                const value = e.target.value;
                
                // Send message to iframe
                iframe.contentWindow.postMessage({
                    type: 'updateTheme',
                    setting: setting,
                    value: value
                }, '*');
            }
        });

        // Also watch for check events on checkboxes (change event)
        form.addEventListener('change', function(e) {
             // For checkboxes, we might strictly mean layout sections which require reload
             // or visual toggles. For now, we focus on style inputs.
             // If refreshing is needed for layout changes:
             if (e.target.name.includes('[sections]')) {
                 // Reload iframe to reflect layout changes (server-side rendering required)
                 // Debounce this if possible, or let user click save? 
                 // For "Live Style" request, color/font is priority. Layout usually needs reload.
             }
             
             // Propagate select changes (fonts/shadows)
             if (e.target.tagName === 'SELECT') {
                 iframe.contentWindow.postMessage({
                    type: 'updateTheme',
                    setting: e.target.name,
                    value: e.target.value
                }, '*');
             }
        });

        // Initialize SortableJS
        const el = document.getElementById('sortable-sections');
        if (el) {
            Sortable.create(el, {
                animation: 150,
                handle: '.cursor-move',
                onEnd: function() {
                    // Collect new order
                    const order = Array.from(el.querySelectorAll('input[type="hidden"]')).map(input => input.value);
                    const iframe = document.getElementById('theme-preview-frame');
                    
                    // Send to iframe for live update
                    iframe.contentWindow.postMessage({
                        type: 'updateOrder',
                        order: order
                    }, '*');
                }
            });
        }

        // Footer Live Update
        const footerAbout = document.querySelector('textarea[name="layout_settings[footer][about]"]');
        const footerCopyright = document.querySelector('input[name="layout_settings[footer][copyright]"]');
        const iframe = document.getElementById('theme-preview-frame');

        if(footerAbout) {
            footerAbout.addEventListener('input', function() {
                iframe.contentWindow.postMessage({
                    type: 'updateFooter',
                    key: 'about',
                    value: this.value
                }, '*');
            });
        }

        if(footerCopyright) {
            footerCopyright.addEventListener('input', function() {
                 iframe.contentWindow.postMessage({
                    type: 'updateFooter',
                    key: 'copyright',
                    value: this.value
                }, '*');
            });
        }
    });
</script>
@endpush
