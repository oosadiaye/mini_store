importScripts('https://storage.googleapis.com/workbox-cdn/releases/6.4.1/workbox-sw.js');

if (workbox) {
    console.log(`Workbox is loaded`);

    // Precache Manifest (if any)
    workbox.precaching.precacheAndRoute(self.__WB_MANIFEST || []);

    // ---------------------------------------------------------
    // STRATEGY 1: STOREFRONT (Network Only - Strict Online)
    // ---------------------------------------------------------
    // Match any URL that does NOT start with /admin and is not a static asset
    // We want to force fresh content for customers (Orders, Stock, etc.)
    workbox.routing.registerRoute(
        ({ url, request }) => !url.pathname.startsWith('/admin') && request.destination === 'document',
        new workbox.strategies.NetworkOnly()
    );

    // ---------------------------------------------------------
    // STRATEGY 2: ADMIN (Network First - Offline Capable)
    // ---------------------------------------------------------
    // Admin pages should try network, but fall back to cache if offline
    workbox.routing.registerRoute(
        ({ url }) => url.pathname.startsWith('/admin'),
        new workbox.strategies.NetworkFirst({
            cacheName: 'admin-pages-cache',
            plugins: [
                new workbox.expiration.ExpirationPlugin({
                    maxEntries: 50,
                    maxAgeSeconds: 7 * 24 * 60 * 60, // 7 Days
                }),
            ],
        })
    );

    // Admin Static Assets (CSS, JS, Images) - Cache First
    // (Common for both, but critical for Admin offline shell)
    workbox.routing.registerRoute(
        ({ request }) => request.destination === 'style' || request.destination === 'script' || request.destination === 'image',
        new workbox.strategies.StaleWhileRevalidate({
            cacheName: 'static-resources',
        })
    );

    // ---------------------------------------------------------
    // STRATEGY 3: BACKGROUND SYNC (Admin Actions)
    // ---------------------------------------------------------
    // Queue failed POST/PUT requests for Admin (e.g. Save Product)
    const bgSyncPlugin = new workbox.backgroundSync.BackgroundSyncPlugin('admin-actions-queue', {
        maxRetentionTime: 24 * 60 // Retry for up to 24 hours (in minutes)
    });

    workbox.routing.registerRoute(
        ({ url, request }) => url.pathname.startsWith('/admin') && (request.method === 'POST' || request.method === 'PUT' || request.method === 'PATCH'),
        new workbox.strategies.NetworkOnly({
            plugins: [bgSyncPlugin]
        }),
        'POST'
    );
    workbox.routing.registerRoute(
        ({ url, request }) => url.pathname.startsWith('/admin') && (request.method === 'POST' || request.method === 'PUT' || request.method === 'PATCH'),
        new workbox.strategies.NetworkOnly({
            plugins: [bgSyncPlugin]
        }),
        'PUT'
    );
    workbox.routing.registerRoute(
        ({ url, request }) => url.pathname.startsWith('/admin') && (request.method === 'POST' || request.method === 'PUT' || request.method === 'PATCH'),
        new workbox.strategies.NetworkOnly({
            plugins: [bgSyncPlugin]
        }),
        'PATCH'
    );

    // Offline Fallback for Storefront
    // If Storefront (NetworkOnly) fails, show offline page
    workbox.routing.setCatchHandler(({ event }) => {
        if (event.request.destination === 'document') {
            // For Admin, it might have fallen back to cache (NetworkFirst), but if that failed too:
            if (event.request.url.includes('/admin')) {
                return caches.match('/offline.html'); // Or specific admin offline page
            }
            // For Storefront, always show offline page
            return caches.match('/offline.html');
        }
        return Response.error();
    });

} else {
    console.log(`Workbox didn't load`);
}
