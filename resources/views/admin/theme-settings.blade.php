@extends('storefront.layout')

@section('content')
<div class="bg-gray-50 min-h-screen pb-20">
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-serif font-bold text-gray-900">Theme Editor <span class="text-gray-400 font-sans text-lg font-normal ml-2">({{ ucfirst($themeSlug) }})</span></h1>
            <a href="/" target="_blank" class="text-sm font-bold uppercase tracking-widest hover:underline">View Storefront ↗</a>
        </div>

        @if(session('status'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-8 shadow-sm" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('admin.theme-settings.update') }}" method="POST" 
              x-data="{ 
                  activeTab: 'hero',
                  categories: {{ json_encode($settings['categories_slider'] ?? []) }},
                  products: {{ json_encode($settings['featured_products'] ?? []) }},
                  banners: {{ json_encode($settings['banners'] ?? []) }},
                  shop: {{ json_encode($settings['shop'] ?? []) }},
                  deals: {{ json_encode($settings['deals'] ?? []) }},
                  about: {{ json_encode($settings['about'] ?? []) }},
                  contact: {{ json_encode($settings['contact'] ?? []) }}, 
                  
                  // Helper for About Sections
                  addAboutSection() { 
                      if(!this.about.sections) this.about.sections = []; 
                      this.about.sections.push({title: 'New Section', content: '', image: ''}); 
                  },
                  removeAboutSection(index) { this.about.sections.splice(index, 1); },

                  // Helper for About Stats
                  addAboutStat() { 
                      if(!this.about.stats) this.about.stats = []; 
                      this.about.stats.push({label: 'Label', value: '100+'}); 
                  },
                  removeAboutStat(index) { this.about.stats.splice(index, 1); },

                  // Helper for About Team
                  addAboutTeam() { 
                      if(!this.about.team) this.about.team = []; 
                      this.about.team.push({name: 'Name', role: 'Role', image: ''}); 
                  },
                  removeAboutTeam(index) { this.about.team.splice(index, 1); }
              }">
            @csrf

            <div class="grid grid-cols-12 gap-8">
                {{-- Sidebar Navigation --}}
                <div class="col-span-12 md:col-span-3 space-y-2">
                    @foreach([
                        'hero' => 'Hero Section',
                        'categories' => 'Category Slider',
                        'featured' => 'Featured Products',
                        'banners' => 'Promo Banners',
                        'shop' => 'Shop Page',
                        'deals' => 'Deal of the Day',
                        'about' => 'About Page',
                        'contact' => 'Contact Page',
                        'footer' => 'Footer',
                        'style' => 'Colors & Typography',
                    ] as $key => $label)
                    <button type="button" 
                            @click="activeTab = '{{ $key }}'"
                            :class="{ 'bg-black text-white shadow-lg': activeTab === '{{ $key }}', 'bg-white text-gray-600 hover:bg-gray-100': activeTab !== '{{ $key }}' }"
                            class="w-full text-left px-6 py-4 rounded-sm font-medium transition duration-200">
                        {{ $label }}
                    </button>
                    @endforeach
                    
                    <div class="pt-8 sticky top-8">
                        <button type="submit" class="w-full bg-green-600 text-white font-bold py-4 rounded-sm shadow-lg hover:bg-green-700 transition transform hover:-translate-y-0.5">
                            Save Changes
                        </button>
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="col-span-12 md:col-span-9 space-y-6">
                    
                    {{-- Hero Section --}}
                    <div x-show="activeTab === 'hero'" x-transition class="bg-white p-8 rounded-sm shadow-sm space-y-6">
                        <h2 class="text-xl font-bold border-b pb-4 mb-6">Hero Configuration</h2>
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Main Title</label>
                                <input type="text" name="hero[title]" value="{{ $settings['hero']['title'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Subtitle</label>
                                <textarea name="hero[subtitle]" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition" rows="2">{{ $settings['hero']['subtitle'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Background Image URL</label>
                                <input type="text" name="hero[image]" value="{{ $settings['hero']['image'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Button Text</label>
                                    <input type="text" name="hero[button_text]" value="{{ $settings['hero']['button_text'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Button URL</label>
                                    <input type="text" name="hero[button_url]" value="{{ $settings['hero']['button_url'] ?? '' }}" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Categories Slider --}}
                    <div x-show="activeTab === 'categories'" x-cloak class="bg-white p-8 rounded-sm shadow-sm">
                        <div class="flex justify-between items-center border-b pb-4 mb-6">
                            <h2 class="text-xl font-bold">Category Slider</h2>
                            <button type="button" @click="addCategory()" class="text-xs bg-black text-white px-3 py-2 uppercase font-bold tracking-widest hover:bg-gray-800">Add Slide</button>
                        </div>
                        
                        <div class="space-y-6">
                            <template x-for="(cat, index) in categories" :key="index">
                                <div class="bg-gray-50 p-4 rounded border border-gray-100 relative group">
                                    <button type="button" @click="removeCategory(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600 font-bold">&times;</button>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input type="hidden" :name="'categories_slider['+index+'][id]'" :value="index"> <!-- Dummy ID to ensure array index preservation if needed -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Name</label>
                                            <input type="text" :name="'categories_slider['+index+'][name]'" x-model="cat.name" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Link URL</label>
                                            <input type="text" :name="'categories_slider['+index+'][url]'" x-model="cat.url" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Image URL</label>
                                            <input type="text" :name="'categories_slider['+index+'][image]'" x-model="cat.image" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </template>
                             <div x-show="categories.length === 0" class="text-center text-gray-400 py-8 italic">No categories added.</div>
                        </div>
                    </div>

                    {{-- Featured Products --}}
                    <div x-show="activeTab === 'featured'" x-cloak class="bg-white p-8 rounded-sm shadow-sm">
                        <div class="flex justify-between items-center border-b pb-4 mb-6">
                            <h2 class="text-xl font-bold">Featured Products (Manual Override)</h2>
                            <button type="button" @click="addProduct()" class="text-xs bg-black text-white px-3 py-2 uppercase font-bold tracking-widest hover:bg-gray-800">Add Product</button>
                        </div>

                        <div class="space-y-6">
                            <template x-for="(prod, index) in products" :key="index">
                                <div class="bg-gray-50 p-4 rounded border border-gray-100 relative group">
                                    <button type="button" @click="removeProduct(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600 font-bold">&times;</button>
                                    <input type="hidden" :name="'featured_products['+index+'][id]'" :value="prod.id || index">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Product Title</label>
                                            <input type="text" :name="'featured_products['+index+'][title]'" x-model="prod.title" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Price</label>
                                            <input type="number" step="0.01" :name="'featured_products['+index+'][price]'" x-model="prod.price" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Image URL</label>
                                            <input type="text" :name="'featured_products['+index+'][image]'" x-model="prod.image" class="w-full bg-white border border-gray-200 rounded px-3 py-2 text-sm">
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="products.length === 0" class="text-center text-gray-400 py-8 italic">No manual products added. Storefront will use database values.</div>
                        </div>
                    </div>

                    {{-- Promo Banners --}}
                    <div x-show="activeTab === 'banners'" x-cloak class="bg-white p-8 rounded-sm shadow-sm">
                        <div class="flex justify-between items-center border-b pb-4 mb-6">
                            <h2 class="text-xl font-bold">Promotional Banners</h2>
                            <button type="button" @click="addBanner()" class="text-xs bg-black text-white px-3 py-2 uppercase font-bold tracking-widest hover:bg-gray-800">Add Banner</button>
                        </div>
                        
                        <div class="space-y-8">
                             <template x-for="(banner, index) in banners" :key="index">
                                <div class="bg-gray-50 p-6 rounded border border-gray-200 relative">
                                    <button type="button" @click="removeBanner(index)" class="absolute top-4 right-4 text-red-400 hover:text-red-600 font-bold">&times;</button>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                                            <input type="text" :name="'banners['+index+'][title]'" x-model="banner.title" class="w-full bg-white border border-gray-200 rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Subtitle</label>
                                            <input type="text" :name="'banners['+index+'][subtitle]'" x-model="banner.subtitle" class="w-full bg-white border border-gray-200 rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-bold text-gray-700 mb-2">Image URL</label>
                                            <input type="text" :name="'banners['+index+'][image]'" x-model="banner.image" class="w-full bg-white border border-gray-200 rounded px-3 py-2">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2">CTA Text</label>
                                                <input type="text" :name="'banners['+index+'][button_text]'" x-model="banner.button_text" class="w-full bg-white border border-gray-200 rounded px-3 py-2">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2">CTA URL</label>
                                                <input type="text" :name="'banners['+index+'][button_url]'" x-model="banner.button_url" class="w-full bg-white border border-gray-200 rounded px-3 py-2">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                             <div x-show="banners.length === 0" class="text-center text-gray-400 py-8 italic">No banners added.</div>
                         </div>
                    </div>


                    {{-- Shop Page Settings --}}
                    <div x-show="activeTab === 'shop'" x-cloak class="bg-white p-8 rounded-sm shadow-sm space-y-6">
                        <h2 class="text-xl font-bold border-b pb-4 mb-6">Shop Page Configuration</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Page Title</label>
                                <input type="text" name="shop[title]" x-model="shop.title" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Subtitle</label>
                                <input type="text" name="shop[subtitle]" x-model="shop.subtitle" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Products Per Page</label>
                                <input type="number" name="shop[products_per_page]" x-model="shop.products_per_page" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pagination Type</label>
                                <select name="shop[pagination][type]" x-model="shop.pagination.type" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3">
                                    <option value="standard">Standard Numbers</option>
                                    <option value="load_more">Load More Button</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 pt-4 border-t border-gray-100">
                                <h3 class="font-bold mb-4">Filters</h3>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="shop[filters][enable_category_filter]" x-model="shop.filters.enable_category_filter" class="form-checkbox text-black border-gray-300 rounded focus:ring-black">
                                        <span>Enable Category Filter</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="shop[filters][enable_price_filter]" x-model="shop.filters.enable_price_filter" class="form-checkbox text-black border-gray-300 rounded focus:ring-black">
                                        <span>Enable Price Filter</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="shop[filters][enable_sorting]" x-model="shop.filters.enable_sorting" class="form-checkbox text-black border-gray-300 rounded focus:ring-black">
                                        <span>Enable Sorting</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Deals Settings --}}
                    <div x-show="activeTab === 'deals'" x-cloak class="bg-white p-8 rounded-sm shadow-sm space-y-6">
                        <h2 class="text-xl font-bold border-b pb-4 mb-6">Deal of the Day Configuration</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="flex items-center space-x-2 mb-4">
                                    <input type="checkbox" name="deals[enabled]" x-model="deals.enabled" class="form-checkbox text-black border-gray-300 rounded focus:ring-black">
                                    <span class="font-bold">Enable Deal Strip</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Badge Text</label>
                                <input type="text" name="deals[badge_text]" x-model="deals.badge_text" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3" placeholder="Deal of Day">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Main Title</label>
                                <input type="text" name="deals[title]" x-model="deals.title" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3" placeholder="Flash Sale: 50% Off">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">End Date & Time</label>
                                <input type="datetime-local" name="deals[end_time]" x-model="deals.end_time" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Target URL</label>
                                <input type="text" name="deals[url]" x-model="deals.url" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3">
                            </div>
                        </div>
                    </div>

                    {{-- About Page Settings --}}
                    <div x-show="activeTab === 'about'" x-cloak class="bg-white p-8 rounded-sm shadow-sm space-y-6">
                        <div class="border-b pb-6 mb-6">
                            <h2 class="text-xl font-bold mb-4">Hero Section</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                                    <input type="text" name="about[hero][title]" x-model="about.hero.title" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Subtitle</label>
                                    <input type="text" name="about[hero][subtitle]" x-model="about.hero.subtitle" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Hero Image URL</label>
                                    <input type="text" name="about[hero][image]" x-model="about.hero.image" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3">
                                </div>
                            </div>
                        </div>

                        {{-- Content Sections Repeater --}}
                        <div class="border-b pb-6 mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold">Content Sections</h2>
                                <button type="button" @click="addAboutSection()" class="text-xs bg-black text-white px-3 py-2 uppercase font-bold tracking-widest hover:bg-gray-800">Add Section</button>
                            </div>
                            <div class="space-y-4">
                                <template x-for="(section, index) in about.sections" :key="index">
                                    <div class="bg-gray-50 p-4 rounded border border-gray-200 relative">
                                        <button type="button" @click="removeAboutSection(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600 font-bold">&times;</button>
                                        <div class="grid gap-4">
                                            <input type="text" :name="'about[sections]['+index+'][title]'" x-model="section.title" placeholder="Title" class="w-full bg-white border border-gray-200 rounded px-3 py-2">
                                            <textarea :name="'about[sections]['+index+'][content]'" x-model="section.content" placeholder="Content" class="w-full bg-white border border-gray-200 rounded px-3 py-2" rows="3"></textarea>
                                            <input type="text" :name="'about[sections]['+index+'][image]'" x-model="section.image" placeholder="Image URL" class="w-full bg-white border border-gray-200 rounded px-3 py-2">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Stats Repeater --}}
                        <div class="border-b pb-6 mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold">Stats Row</h2>
                                <button type="button" @click="addAboutStat()" class="text-xs bg-black text-white px-3 py-2 uppercase font-bold tracking-widest hover:bg-gray-800">Add Stat</button>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <template x-for="(stat, index) in about.stats" :key="index">
                                    <div class="bg-gray-50 p-4 rounded border border-gray-200 relative">
                                        <button type="button" @click="removeAboutStat(index)" class="absolute top-1 right-1 text-red-400 hover:text-red-600 text-xs font-bold">&times;</button>
                                        <input type="text" :name="'about[stats]['+index+'][value]'" x-model="stat.value" placeholder="Value (e.g. 10k+)" class="w-full bg-white border border-gray-200 rounded px-2 py-1 mb-2 text-sm font-bold">
                                        <input type="text" :name="'about[stats]['+index+'][label]'" x-model="stat.label" placeholder="Label (e.g. Customers)" class="w-full bg-white border border-gray-200 rounded px-2 py-1 text-xs">
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Team Repeater --}}
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold">Team Members</h2>
                                <button type="button" @click="addAboutTeam()" class="text-xs bg-black text-white px-3 py-2 uppercase font-bold tracking-widest hover:bg-gray-800">Add Member</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <template x-for="(member, index) in about.team" :key="index">
                                    <div class="bg-gray-50 p-4 rounded border border-gray-200 relative flex items-start gap-4">
                                        <button type="button" @click="removeAboutTeam(index)" class="absolute top-2 right-2 text-red-400 hover:text-red-600 font-bold">&times;</button>
                                        <div class="w-16 h-16 bg-gray-200 rounded-full overflow-hidden flex-shrink-0">
                                            <img :src="member.image || 'https://via.placeholder.com/150'" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-grow space-y-2">
                                            <input type="text" :name="'about[team]['+index+'][name]'" x-model="member.name" placeholder="Name" class="w-full bg-white border border-gray-200 rounded px-2 py-1 text-sm font-bold">
                                            <input type="text" :name="'about[team]['+index+'][role]'" x-model="member.role" placeholder="Role" class="w-full bg-white border border-gray-200 rounded px-2 py-1 text-xs text-gray-500">
                                            <input type="text" :name="'about[team]['+index+'][image]'" x-model="member.image" placeholder="Image URL" class="w-full bg-white border border-gray-200 rounded px-2 py-1 text-xs">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Page Settings --}}
                    <div x-show="activeTab === 'contact'" x-cloak class="bg-white p-8 rounded-sm shadow-sm space-y-6">
                        <h2 class="text-xl font-bold border-b pb-4 mb-6">Contact Page Configuration</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                             <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                                <input type="text" name="contact[title]" x-model="contact.title" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Subtitle</label>
                                <input type="text" name="contact[subtitle]" x-model="contact.subtitle" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            
                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <h3 class="font-bold mb-4">Contact Info</h3>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                                <input type="text" name="contact[contact_info][email]" x-model="contact.contact_info.email" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Phone</label>
                                <input type="text" name="contact[contact_info][phone]" x-model="contact.contact_info.phone" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                                <input type="text" name="contact[contact_info][address]" x-model="contact.contact_info.address" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                            
                            <div class="md:col-span-2 border-t pt-4 mt-2">
                                <h3 class="font-bold mb-4">Map & Form</h3>
                            </div>
                            <div>
                                <label class="flex items-center space-x-2 mb-2">
                                    <input type="checkbox" name="contact[map][enabled]" x-model="contact.map.enabled" class="form-checkbox text-black border-gray-300 rounded focus:ring-black">
                                    <span>Enable Google Maps</span>
                                </label>
                                <input type="text" name="contact[map][embed_url]" x-model="contact.map.embed_url" placeholder="Google Maps Embed URL" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition mt-2">
                            </div>
                            <div>
                                <label class="flex items-center space-x-2 mb-2">
                                    <input type="checkbox" name="contact[form][enable_phone_field]" x-model="contact.form.enable_phone_field" class="form-checkbox text-black border-gray-300 rounded focus:ring-black">
                                    <span>Enable Phone Field in Form</span>
                                </label>
                                <label class="block text-sm font-bold text-gray-700 mb-1 mt-2">Submit Button Text</label>
                                <input type="text" name="contact[form][submit_text]" x-model="contact.form.submit_text" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div x-show="activeTab === 'footer'" x-cloak class="bg-white p-8 rounded-sm shadow-sm space-y-6">
                        <h2 class="text-xl font-bold border-b pb-4 mb-6">Footer Settings</h2>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Newsletter Text</label>
                            <input type="text" name="footer[newsletter_text]" value="{{ $settings['footer']['newsletter_text'] ?? 'Subscribe for updates' }}" class="w-full bg-gray-50 border border-gray-200 rounded px-4 py-3 focus:outline-none focus:border-black transition">
                        </div>

                         <div>
                            <div class="flex justify-between items-center mb-4">
                                <label class="block text-sm font-bold text-gray-700">Social Links</label>
                                <button type="button" @click="addSocial()" class="text-xs bg-gray-200 text-black px-2 py-1 uppercase font-bold tracking-widest hover:bg-gray-300">Add Link</button>
                            </div>
                            <div class="space-y-3">
                                <template x-for="(social, index) in socials" :key="index">
                                    <div class="flex gap-4 items-center">
                                        <select :name="'footer[social_links]['+index+'][platform]'" x-model="social.platform" class="bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm">
                                            <option value="facebook">Facebook</option>
                                            <option value="instagram">Instagram</option>
                                            <option value="twitter">Twitter</option>
                                            <option value="tiktok">TikTok</option>
                                            <option value="linkedin">LinkedIn</option>
                                        </select>
                                        <input type="text" :name="'footer[social_links]['+index+'][url]'" x-model="social.url" placeholder="https://..." class="flex-grow bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm">
                                        <button type="button" @click="removeSocial(index)" class="text-red-400 hover:text-red-600">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Styling --}}
                    <div x-show="activeTab === 'style'" x-cloak class="bg-white p-8 rounded-sm shadow-sm space-y-6">
                        <h2 class="text-xl font-bold border-b pb-4 mb-6">Colors & Typography</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <h3 class="font-bold text-gray-900">Colors</h3>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Primary Background</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="theme_colors[primary]" value="{{ $settings['theme_colors']['primary'] ?? '#111111' }}" class="h-10 w-10 border-none p-0">
                                        <input type="text" name="theme_colors[primary]" value="{{ $settings['theme_colors']['primary'] ?? '#111111' }}" class="flex-grow bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm font-mono uppercase">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Secondary / Text</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="theme_colors[secondary]" value="{{ $settings['theme_colors']['secondary'] ?? '#ffffff' }}" class="h-10 w-10 border-none p-0">
                                        <input type="text" name="theme_colors[secondary]" value="{{ $settings['theme_colors']['secondary'] ?? '#ffffff' }}" class="flex-grow bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm font-mono uppercase">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Accent Color</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="theme_colors[accent]" value="{{ $settings['theme_colors']['accent'] ?? '#3b82f6' }}" class="h-10 w-10 border-none p-0">
                                        <input type="text" name="theme_colors[accent]" value="{{ $settings['theme_colors']['accent'] ?? '#3b82f6' }}" class="flex-grow bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm font-mono uppercase">
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h3 class="font-bold text-gray-900">Typography</h3>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Heading Font</label>
                                    <select name="typography[heading_font]" class="w-full bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm">
                                        <option value="Playfair Display" {{ ($settings['typography']['heading_font'] ?? '') == 'Playfair Display' ? 'selected' : '' }}>Playfair Display (Serif)</option>
                                        <option value="Inter" {{ ($settings['typography']['heading_font'] ?? '') == 'Inter' ? 'selected' : '' }}>Inter (Sans)</option>
                                        <option value="Roboto" {{ ($settings['typography']['heading_font'] ?? '') == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Body Font</label>
                                    <select name="typography[body_font]" class="w-full bg-gray-50 border border-gray-200 rounded px-3 py-2 text-sm">
                                        <option value="Inter" {{ ($settings['typography']['body_font'] ?? '') == 'Inter' ? 'selected' : '' }}>Inter (Sans)</option>
                                        <option value="Roboto" {{ ($settings['typography']['body_font'] ?? '') == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                                        <option value="Open Sans" {{ ($settings['typography']['body_font'] ?? '') == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection
