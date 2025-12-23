@php
    $settings = $section->settings ?? [];
    $limit = $settings['limit'] ?? 4;
    $type = $settings['collection'] ?? 'all';
    
    // Layout Settings
    $colsDesktop = $settings['grid_cols_desktop'] ?? 4;
    $colsMobile = $settings['grid_cols_mobile'] ?? 2;
    $containerWidth = $settings['container_width'] ?? 'container'; // 'container' or 'full'

    // Style Settings
    $styles = [];
    if (!empty($settings['margin_top'])) $styles[] = "margin-top: {$settings['margin_top']}";
    if (!empty($settings['margin_bottom'])) $styles[] = "margin-bottom: {$settings['margin_bottom']}";
    if (!empty($settings['background_color'])) $styles[] = "background-color: {$settings['background_color']}";
    if (!empty($settings['background_image'])) {
        $styles[] = "background-image: url('{$settings['background_image']}')";
        $styles[] = "background-size: cover";
        $styles[] = "background-position: center";
    }
    
    $styleString = implode('; ', $styles);

    // Grid Classes
    // We map numeric values to Tailwind classes
    $gridMap = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
        5 => 'grid-cols-5',
    ];
    
    $desktopClass = isset($gridMap[$colsDesktop]) ? "lg:{$gridMap[$colsDesktop]}" : 'lg:grid-cols-4';
    $mobileClass = isset($gridMap[$colsMobile]) ? $gridMap[$colsMobile] : 'grid-cols-2';

    // Fetch Products
    $query = \App\Models\Product::active();
    
    if ($type === 'new') {
        $query->latest();
    } elseif ($type === 'featured') {
        $query->where('is_featured', true);
    }
    
    $products = $query->take($limit)->get();
@endphp

<section id="{{ $section_id ?? '' }}" class="py-16 {{ empty($settings['background_color']) && empty($settings['background_image']) ? 'bg-white' : '' }}" style="{{ $styleString }}">
    <div class="{{ $containerWidth === 'full' ? 'w-full px-4' : 'container mx-auto px-6' }}">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $section->title }}</h2>
            @if($section->content)
            <p class="text-gray-600 max-w-2xl mx-auto">{{ $section->content }}</p>
            @endif
        </div>

        <div class="grid {{ $mobileClass }} md:grid-cols-2 {{ $desktopClass }} gap-8">
            @foreach($products as $product)
            <div class="group">
                <div class="relative overflow-hidden rounded-lg mb-4">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-64 object-cover transform group-hover:scale-105 transition duration-500">
                    @if($product->sale_price)
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">SALE</span>
                    @endif
                    <!-- Add to Cart Overlay -->
                    <div class="absolute inset-x-0 bottom-0 p-4 translate-y-full group-hover:translate-y-0 transition duration-300">
                        <button onclick="addToCart({{ $product->id }})" class="w-full bg-white text-gray-900 py-2 rounded shadow-lg font-bold hover:bg-indigo-600 hover:text-white transition">Add to Cart</button>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition">
                    <a href="{{ route('storefront.product.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="text-gray-900 font-bold">${{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price)
                        <span class="text-gray-400 line-through text-sm">${{ number_format($product->compare_price, 2) }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
