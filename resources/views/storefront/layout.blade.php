<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    @php
        $settings = tenant()->data ?? [];
        $titleSuffix = $settings['seo_title_suffix'] ?? ' | Online Store';
        $metaDesc = $settings['seo_meta_description'] ?? 'Welcome to ' . tenant('name') . ' - your premier destination for quality products.';
        $metaKeywords = $settings['seo_meta_keywords'] ?? '';

        // Build Schema Org Data
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Store',
            'name' => tenant('name'),
            'url' => route('storefront.home'),
            'description' => $metaDesc,
        ];
        
        $sameAs = [];
        if (!empty($settings['social_facebook'])) $sameAs[] = $settings['social_facebook'];
        if (!empty($settings['social_twitter'])) $sameAs[] = $settings['social_twitter'];
        if (!empty($settings['social_instagram'])) $sameAs[] = $settings['social_instagram'];
        
        if (!empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }
    @endphp

    <title>@yield('title', tenant('name')){{ $titleSuffix }}</title>
    <meta name="description" content="@yield('meta_description', $metaDesc)">
    @if($metaKeywords)
    <meta name="keywords" content="{{ $metaKeywords }}">
    @endif

    <!-- JSON-LD Schema -->
    <script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700&family=Oswald:wght@400;500;700&family=Playfair+Display:wght@400;600;700&family=Roboto:wght@300;400;500;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Fonts -->

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Favicon (custom or auto-generated) --}}
    @php
        $faviconUrl = \App\Helpers\LogoHelper::getFavicon();
    @endphp
    <link rel="icon" href="{{ $faviconUrl }}">
    
    <!-- Swiper.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <link rel="manifest" href="/manifest-store.json">
    <meta name="theme-color" content="#4f46e5">
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                })
                .catch(err => {
                    console.log('ServiceWorker registration failed: ', err);
                });
        }
    </script>

    <!-- Facebook Pixel Code -->
    @if(!empty($settings['facebook_pixel_id']))
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $settings['facebook_pixel_id'] }}');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id={{ $settings['facebook_pixel_id'] }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
    @endif
    
    @php
        // Check for Preview Mode
        $previewTemplateId = request()->get('preview_template_id');
        $isAdmin = auth()->guard('web')->check(); // Ensure only admins can preview
        
        $themeSettings = null;
        $colors = ['primary' => '#6366F1', 'secondary' => '#8B5CF6', 'accent' => '#F59E0B'];
        $fonts = ['heading' => 'Inter', 'body' => 'Inter'];
        $customCss = '';

        if ($previewTemplateId && $isAdmin) {
            $template = \App\Models\StorefrontTemplate::find($previewTemplateId);
            if ($template) {
                $colors = $template->default_settings['colors'] ?? $colors;
                $fonts = $template->default_settings['fonts'] ?? $fonts;
                // Preview mode doesn't load custom CSS from settings, or could load empty
            }
        } else {
            $themeSettings = \App\Models\ThemeSetting::where('is_active', true)->first();
            if ($themeSettings) {
                $colors = $themeSettings->colors ?? $colors;
                $fonts = $themeSettings->fonts ?? $fonts;
                $customCss = $themeSettings->custom_css ?? '';
            }
        }
    @endphp

    @php
       $layout = $themeSettings->layout_settings ?? [];
       $radius = $layout['visuals']['radius'] ?? 8;
       $shadow = $layout['visuals']['shadow'] ?? 'md';
       $headerStyle = $layout['header_style'] ?? 'sticky';
       
       $shadowMap = [
           'none' => 'none',
           'sm' => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
           'md' => '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
           'lg' => '0 10px 15px -3px rgb(0 0 0 / 0.2), 0 4px 6px -4px rgb(0 0 0 / 0.2)',
           'xl' => '0 20px 25px -5px rgb(0 0 0 / 0.2), 0 8px 10px -6px rgb(0 0 0 / 0.2)',
       ];
       $shadowVal = $shadowMap[$shadow] ?? $shadowMap['md'];
    @endphp

    <style>
        :root {
            --color-primary: {{ $colors['primary'] ?? '#6366F1' }};
            --color-secondary: {{ $colors['secondary'] ?? '#8B5CF6' }};
            --color-accent: {{ $colors['accent'] ?? '#F59E0B' }};
            
            --font-heading: '{{ $fonts['heading'] ?? 'Inter' }}';
            --font-body: '{{ $fonts['body'] ?? 'Inter' }}';

            --btn-radius: {{ $radius }}px;
            --shadow-val: {{ $shadowVal }};
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading), serif;
        }
        body {
            font-family: var(--font-body), sans-serif;
            scroll-behavior: smooth;
        }

        /* Dynamic Visuals */
        button, .btn, a.inline-block {
            border-radius: var(--btn-radius) !important;
            box-shadow: var(--shadow-val);
        }
        .card, .group.relative {
            box-shadow: var(--shadow-val);
            border-radius: var(--btn-radius);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        /* Mobile Menu Touch Targets */
        @media (max-width: 768px) {
            nav a, nav button {
                min-height: 44px;
                min-width: 44px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        /* Focus States for Accessibility */
        a:focus, button:focus, input:focus, select:focus {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }
        
        /* Smooth Transitions */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Hide scrollbar for mobile filters */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Mobile Footer Menu */
        @media (max-width: 768px) {
            body {
                padding-bottom: 64px; /* Height of mobile footer menu */
            }
        }
        
        /* Safe area for notched devices */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            .safe-area-inset-bottom {
                padding-bottom: env(safe-area-inset-bottom);
            }
        }
        
        {{ $customCss }}
    </style>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: 'var(--color-primary)',
                        secondary: 'var(--color-secondary)',
                        accent: 'var(--color-accent)',
                    },
                    fontFamily: {
                        sans: ['var(--font-body)', 'sans-serif'],
                        serif: ['var(--font-heading)', 'serif'],
                    },
                    aspectRatio: {
                        '3/4': '3 / 4',
                    }
                }
            }
        }

        // Live Preview Listener
        window.addEventListener('message', function(event) {
            const data = event.data;
            if (data.type === 'updateTheme') {
                const root = document.documentElement;
                
                if (data.setting.includes('colors[')) {
                    // Extract color name: colors[primary] -> primary
                    const colorName = data.setting.match(/colors\[(.*?)\]/)[1];
                    root.style.setProperty(`--color-${colorName}`, data.value);
                }
                
                if (data.setting.includes('fonts[')) {
                    const fontType = data.setting.match(/fonts\[(.*?)\]/)[1];
                    root.style.setProperty(`--font-${fontType}`, `'${data.value}'`);
                }
                
                if (data.setting === 'layout_settings[visuals][radius]') {
                    root.style.setProperty('--btn-radius', `${data.value}px`);
                }

                if (data.setting === 'layout_settings[visuals][shadow]') {
                   const shadowMap = {
                       'none': 'none',
                       'sm': '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                       'md': '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                       'lg': '0 10px 15px -3px rgb(0 0 0 / 0.2), 0 4px 6px -4px rgb(0 0 0 / 0.2)',
                       'xl': '0 20px 25px -5px rgb(0 0 0 / 0.2), 0 8px 10px -6px rgb(0 0 0 / 0.2)',
                   };
                   root.style.setProperty('--shadow-val', shadowMap[data.value] || shadowMap['md']);
                }
                if (data.type === 'updateOrder') {
                    const order = data.order;
                    const container = document.getElementById('home-sections');
                    
                    if(container) {
                         const sections = {};
                         container.querySelectorAll('.theme-section').forEach(el => {
                             const key = el.id.replace('section-', '');
                             sections[key] = el;
                         });
                         
                         const frag = document.createDocumentFragment();
                         order.forEach(key => {
                             if(sections[key]) {
                                 frag.appendChild(sections[key]);
                             }
                         });
                         
                         container.appendChild(frag);
                    }
                }

                if (data.type === 'updateFooter') {
                   // Generic text updates
                   if (data.key === 'about') {
                       const el = document.getElementById('footer-about');
                       if(el) el.innerText = data.value;
                   }
                   if (data.key === 'copyright') {
                       const el = document.getElementById('footer-copyright');
                       if (el) {
                           const year = new Date().getFullYear();
                           el.innerText = `© ${year} {{ tenant('name') }}. ${data.value}`; 
                       }
                   }
                }
            }
        });
    </script>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50">
    <!-- Top Announcement Bar with Gradient -->
    <div class="bg-gradient-to-r from-purple-600 via-pink-500 to-orange-500 text-white py-2 text-center text-sm font-medium">
        <p>🎉 Free Shipping on Orders Over {{ tenant('data')['currency_symbol'] ?? '₦' }}50 | Use Code: <span class="font-bold">FREESHIP</span></p>
    </div>

    <!-- Navigation (Premium with Gradient) -->
    @php
        $containerClass = 'container mx-auto px-4 max-w-7xl relative z-10';
        $headerStyle = $themeSettings->layout_settings['header_style'] ?? 'modern';
    @endphp

    {{-- Dynamic Header Inclusion --}}
    @includeIf('storefront.layouts.headers.' . $headerStyle)
    @if(!view()->exists('storefront.layouts.headers.' . $headerStyle))
        @include('storefront.layouts.headers.modern')
    @endif

    @yield('content')



    <!-- Mobile Footer Menu (Sticky Bottom Navigation) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40 safe-area-inset-bottom">
        <div class="grid grid-cols-4 h-16">
            <!-- Home -->
            <a href="{{ route('storefront.home') }}" class="flex flex-col items-center justify-center gap-1 text-gray-600 hover:text-primary transition-colors {{ request()->routeIs('storefront.home') ? 'text-primary' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="text-[10px] font-medium">Home</span>
            </a>

            <!-- Shop -->
            <a href="{{ route('storefront.products') }}" class="flex flex-col items-center justify-center gap-1 text-gray-600 hover:text-primary transition-colors {{ request()->routeIs('storefront.products') ? 'text-primary' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="text-[10px] font-medium">Shop</span>
            </a>

            <!-- Cart -->
            <a href="{{ route('storefront.cart.index') }}" class="flex flex-col items-center justify-center gap-1 text-gray-600 hover:text-primary transition-colors relative {{ request()->routeIs('storefront.cart.*') ? 'text-primary' : '' }}">
                <div class="relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    @php
                        $cartCount = \App\Models\Cart::where('session_id', session()->getId())->first()?->items->sum('quantity') ?? 0;
                    @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-primary text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full ring-2 ring-white">{{ $cartCount }}</span>
                    @endif
                </div>
                <span class="text-[10px] font-medium">Cart</span>
            </a>

            <!-- Account -->
            @if(Auth::guard('customer')->check())
                <a href="#" class="flex flex-col items-center justify-center gap-1 text-gray-600 hover:text-primary transition-colors">
                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="text-xs font-bold text-primary">{{ substr(Auth::guard('customer')->user()->name, 0, 1) }}</span>
                    </div>
                    <span class="text-[10px] font-medium">Account</span>
                </a>
            @else
                <a href="{{ route('storefront.login') }}" class="flex flex-col items-center justify-center gap-1 text-gray-600 hover:text-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-[10px] font-medium">Account</span>
                </a>
            @endif
        </div>
    </nav>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-20">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ tenant('name') }}</h3>
                    <p class="text-gray-400" id="footer-about">{{ $layout['footer']['about'] ?? 'Your trusted online store for quality products.' }}</p>
                    <!-- Social Links -->
                    <!-- Social Links -->
                    <div class="flex space-x-4 mt-6">
                        @if(!empty($settings['social_facebook']))
                        <a href="{{ $settings['social_facebook'] }}" target="_blank" class="text-gray-400 hover:text-white transition"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                        @endif
                        @if(!empty($settings['social_twitter']))
                        <a href="{{ $settings['social_twitter'] }}" target="_blank" class="text-gray-400 hover:text-white transition"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>
                        @endif
                        @if(!empty($settings['social_instagram']))
                        <a href="{{ $settings['social_instagram'] }}" target="_blank" class="text-gray-400 hover:text-white transition"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                        @endif
                        @if(!empty($settings['social_youtube']))
                        <a href="{{ $settings['social_youtube'] }}" target="_blank" class="text-gray-400 hover:text-white transition"><i class="fab fa-youtube text-lg"></i></a>
                        @endif
                        @if(!empty($settings['social_tiktok']))
                        <a href="{{ $settings['social_tiktok'] }}" target="_blank" class="text-gray-400 hover:text-white transition"><i class="fab fa-tiktok text-lg"></i></a>
                        @endif
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('storefront.home') }}" class="hover:text-white transition">Home</a></li>
                        <li><a href="{{ route('storefront.products') }}" class="hover:text-white transition">Shop</a></li>
                        <li><a href="{{ route('storefront.page', 'about-us') }}" class="hover:text-white transition">About Us</a></li>
                        <li><a href="{{ route('storefront.page', 'contact') }}" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Customer Service</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('storefront.page', 'shipping-policy') }}" class="hover:text-white transition">Shipping Info</a></li>
                        <li><a href="{{ route('storefront.page', 'refund-policy') }}" class="hover:text-white transition">Returns</a></li>
                        <li><a href="{{ route('storefront.page', 'faq') }}" class="hover:text-white transition">FAQ</a></li>
                        <li><a href="{{ route('storefront.page', 'privacy-policy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Newsletter</h4>
                    <p class="text-gray-400 mb-4">Subscribe for updates and exclusive offers</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="px-4 py-2 rounded-l-lg w-full text-gray-900 border-none focus:ring-2 focus:ring-primary">
                        <button class="bg-primary px-4 py-2 rounded-r-lg hover:bg-opacity-90 transition font-bold">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p id="footer-copyright">&copy; {{ date('Y') }} {{ tenant('name') }}. {{ $layout['footer']['copyright'] ?? 'All rights reserved.' }}</p>
            </div>
        </div>
    </footer>

    <!-- Toast Notification -->


    <!-- Global Scripts -->
    <script>
        // Global Add to Cart Function
        window.addToCart = function(productId, quantity = 1) {
            fetch(`/cart/add/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update header cart count
                    const cartCountEls = document.querySelectorAll('.cart-count-badge');
                    cartCountEls.forEach(el => {
                        el.innerText = data.cart_count;
                        el.classList.remove('hidden');
                        // Animate
                        el.classList.add('scale-125');
                        setTimeout(() => el.classList.remove('scale-125'), 200);
                    });

                    // Show Toast
                    window.dispatchEvent(new CustomEvent('toast', { 
                        detail: { message: 'Product added to cart!', type: 'success' } 
                    }));
                } else {
                     window.dispatchEvent(new CustomEvent('toast', { 
                        detail: { message: data.message || 'Something went wrong', type: 'error' } 
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: 'Network error. Please try again.', type: 'error' } 
                }));
            });
        }
    </script>
    
    <!-- Swiper.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @if(request()->has('editor_mode'))
        <script src="{{ asset('assets/js/page-builder-client.js') }}"></script>
    @endif
</body>
</html>
