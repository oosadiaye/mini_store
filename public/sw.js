importScripts('https://storage.googleapis.com/workbox-cdn/releases/6.4.1/workbox-sw.js');

if (workbox) {
    console.log(`Workbox is loaded`);

    // ---------------------------------------------------------
    // CACHE VERSIONING & AUTO-CLEANUP
    // ---------------------------------------------------------
    const CACHE_VERSION = 'v2'; // Increment when making breaking changes
    const CACHE_NAMES = {
        admin: `admin-pages-${CACHE_VERSION}`,
        static: `static-resources-${CACHE_VERSION}`,
    };

    // Auto-delete old caches on activation
    self.addEventListener('activate', (event) => {
        event.waitUntil(
            caches.keys().then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        // Delete any cache not in our current version
                        if (!Object.values(CACHE_NAMES).includes(cacheName) &&
                            !cacheName.includes('workbox-precache')) {
                            console.log('🗑️ Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            }).then(() => {
                console.log('✅ Cache cleanup complete');
                return self.clients.claim(); // Take control immediately
            })
        );
    });

    // Precache Manifest (if any)
    workbox.precaching.precacheAndRoute(self.__WB_MANIFEST || []);

    // ---------------------------------------------------------
    // ROUTE PRIORITY (Most specific first)
    // ---------------------------------------------------------

    // ---------------------------------------------------------
    // 1. PAGE BUILDER & THEME CUSTOMIZER - NEVER CACHE
    // ---------------------------------------------------------
    // These routes must ALWAYS hit the network to prevent cache conflicts
    // Works across all tenants and themes due to path matching
    workbox.routing.registerRoute(
        ({ url }) => {
            const path = url.pathname;
            return path.includes('/admin/page-builder/') ||
                path.includes('/admin/theme/customizer') ||
                path.includes('/admin/theme/save');
        },
        new workbox.strategies.NetworkOnly(),
        'GET'
    );

    // ---------------------------------------------------------
    // 2. API ROUTES - NEVER CACHE
    // ---------------------------------------------------------
    // All API endpoints should always fetch fresh data
    workbox.routing.registerRoute(
        ({ url }) => url.pathname.startsWith('/api/'),
        new workbox.strategies.NetworkOnly()
    );

    // ---------------------------------------------------------
    // 3. POST/PUT/PATCH/DELETE - NEVER CACHE (All Admin Actions)
    // ---------------------------------------------------------
    // Dynamic requests must never be cached, regardless of tenant
    // Removed background sync to provide immediate feedback
    const dynamicMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    dynamicMethods.forEach(method => {
        workbox.routing.registerRoute(
            ({ url, request }) => {
                const path = url.pathname;
                return request.method === method &&
                    (path.startsWith('/admin') || path.startsWith('/api/'));
            },
            new workbox.strategies.NetworkOnly({
                plugins: [{
                    // Log errors without caching
                    fetchDidFail: async ({ request, error }) => {
                        console.error(`❌ ${method} request failed:`, request.url, error);
                    }
                }]
            }),
            method
        );
    });

    // ---------------------------------------------------------
    // 4. ADMIN PAGES - NETWORK FIRST (Exclude dynamic routes)
    // ---------------------------------------------------------
    // Cache admin pages for offline access, but exclude page builder
    // This works across tenants because we're matching path patterns
    workbox.routing.registerRoute(
        ({ url, request }) => {
            const path = url.pathname;
            return request.destination === 'document' &&
                path.startsWith('/admin') &&
                !path.includes('/admin/page-builder/') &&
                !path.includes('/admin/theme/customizer') &&
                !path.includes('/admin/theme/save') &&
                !path.startsWith('/api/');
        },
        new workbox.strategies.NetworkFirst({
            cacheName: CACHE_NAMES.admin,
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 50,
                    maxAgeSeconds: 7 * 24 * 60 * 60, // 7 Days
                }),
            ],
        })
    );

    // ---------------------------------------------------------
    // 5. STATIC ASSETS - STALE WHILE REVALIDATE
    // ---------------------------------------------------------
    // Cache CSS, JS, images for performance (tenant-agnostic)
    workbox.routing.registerRoute(
        ({ request }) => {
            return request.destination === 'style' ||
                request.destination === 'script' ||
                request.destination === 'image' ||
                request.destination === 'font';
        },
        new workbox.strategies.StaleWhileRevalidate({
            cacheName: CACHE_NAMES.static,
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 100,
                    maxAgeSeconds: 30 * 24 * 60 * 60, // 30 Days
                }),
            ],
        })
    );

    // ---------------------------------------------------------
    // 6. STOREFRONT - NETWORK ONLY (Fresh content for customers)
    // ---------------------------------------------------------
    // Storefront must always show fresh data (orders, stock, etc.)
    // Works across all tenants
    workbox.routing.registerRoute(
        ({ url, request }) => {
            return !url.pathname.startsWith('/admin') &&
                request.destination === 'document';
        },
        new workbox.strategies.NetworkOnly()
    );

    // ---------------------------------------------------------
    // OFFLINE FALLBACK
    // ---------------------------------------------------------
    // Explicitly cache offline page during install
    self.addEventListener('install', (event) => {
        event.waitUntil(
            caches.open(CACHE_NAMES.static).then((cache) => {
                return cache.add('/offline.html').catch(err => {
                    console.log('⚠️ Could not cache offline.html:', err);
                });
            })
        );
    });

    // ---------------------------------------------------------
    // OFFLINE FALLBACK
    // ---------------------------------------------------------
    // ---------------------------------------------------------
    // OFFLINE FALLBACK
    // ---------------------------------------------------------
    workbox.routing.setCatchHandler(async ({ event }) => {
        // Only provide fallback for navigation/documents
        if (event.request.destination === 'document') {
            const cache = await caches.open(CACHE_NAMES.static);
            const cachedResponse = await cache.match('/offline.html');
            if (cachedResponse) {
                return cachedResponse;
            }
        }

        // Return a generic error response for non-GET requests if offline
        // (Though our interceptors should usually handle this before it gets here)
        if (event.request.method !== 'GET') {
            return new Response(JSON.stringify({
                success: false,
                offline: true,
                message: 'You are offline. Your request has been queued.'
            }), {
                headers: { 'Content-Type': 'application/json' },
                status: 503
            });
        }

        return Response.error();
    });

    // ---------------------------------------------------------
    // DEV MODE BYPASS (Optional)
    // ---------------------------------------------------------
    // Skip service worker when ?no-cache=1 is in URL
    self.addEventListener('fetch', (event) => {
        if (event.request.url.includes('no-cache=1')) {
            event.respondWith(fetch(event.request));
        }
    });

    console.log('✅ Service Worker configured with cache version:', CACHE_VERSION);
    console.log('📋 Cache names:', CACHE_NAMES);
    console.log('🚫 Excluded from cache: /admin/page-builder/, /admin/theme/customizer, /api/');

} else {
    console.log(`Workbox didn't load`);
}
