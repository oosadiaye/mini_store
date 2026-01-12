@props(['menuCategories'])
<div class="bg-[#F6F9FC] py-20 border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-[#0A2540] mb-2">Curated Collections</h2>
            <p class="text-gray-500 max-w-lg mx-auto">Explore our premium selection categorized for your convenience.</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($menuCategories as $cat)
                <a href="#" class="group relative bg-white p-8 rounded-[24px] border border-gray-50 transition-all duration-500 hover:shadow-[0_20px_50px_rgba(10,37,64,0.08)] hover:-translate-y-2 text-center flex flex-col items-center">
                    <div class="h-16 w-16 bg-[#0A2540]/5 rounded-full mb-6 flex items-center justify-center text-[#0A2540] group-hover:bg-[#0A2540] group-hover:text-white transition-all duration-500 group-hover:scale-110">
                        <svg width="32" height="32" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </div>
                    <span class="font-bold text-[#0A2540] tracking-tight">{{ $cat->name }}</span>
                    <div class="absolute inset-0 border-2 border-transparent group-hover:border-[#0A2540]/5 rounded-[24px] pointer-events-none transition-all"></div>
                </a>
            @endforeach
        </div>
    </div>
</div>
