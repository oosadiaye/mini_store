@extends('storefront.layout')

@section('title', $post->title)
@section('meta_description', $post->excerpt)

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-sm text-gray-500">
            <a href="{{ route('storefront.home') }}" class="hover:text-primary transition">Home</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900 font-medium">Blog</span>
        </nav>

        <article class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            @if($post->image_url)
                <div class="aspect-video w-full overflow-hidden">
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            <div class="p-8 md:p-12">
                <header class="mb-8">
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                        <span class="bg-primary/10 text-primary px-3 py-1 rounded-full font-bold text-xs uppercase tracking-wider">
                            News
                        </span>
                        <span class="flex items-center">
                            <i class="far fa-calendar mr-2"></i>
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}
                        </span>
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-serif leading-tight mb-6">
                        {{ $post->title }}
                    </h1>

                    @if($post->excerpt)
                        <div class="text-xl text-gray-600 font-light italic border-l-4 border-primary pl-6">
                            {{ $post->excerpt }}
                        </div>
                    @endif
                </header>

                <div class="prose prose-lg prose-indigo max-w-none text-gray-700">
                    {!! nl2br(e($post->content)) !!}
                </div>
                
                <div class="mt-12 pt-8 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('storefront.home') }}" class="inline-flex items-center text-gray-600 hover:text-primary transition font-medium">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Home
                    </a>
                    
                    <div class="flex gap-4">
                        <!-- Share buttons could go here -->
                        <button class="text-gray-400 hover:text-[#1877F2] transition"><i class="fab fa-facebook fa-lg"></i></button>
                        <button class="text-gray-400 hover:text-[#1DA1F2] transition"><i class="fab fa-twitter fa-lg"></i></button>
                        <button class="text-gray-400 hover:text-[#0A66C2] transition"><i class="fab fa-linkedin fa-lg"></i></button>
                    </div>
                </div>
            </div>
        </article>
    </div>
</div>
@endsection
