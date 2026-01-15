/**
 * CacheWarmer.js
 * Automatically pre-fetches critical routes in the background to ensure they are available offline.
 * This effectively "warms" the Service Worker cache.
 */

export const CacheWarmer = {
    init() {
        // Only run if service worker is active
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            // Delay slightly to prioritize initial page load
            setTimeout(() => {
                this.warmRoutes();
            }, 3000);
        }
    },

    async warmRoutes() {
        console.log('🔥 CacheWarmer: Starting background fetch...');

        // Get tenant slug from URL or meta tag
        // Assuming URL structure /:tenant_slug/admin/dashboard
        const pathSegments = window.location.pathname.split('/');
        // segment[0] is empty, segment[1] is tenant slug
        const tenantSlug = pathSegments[1];

        if (!tenantSlug) {
            console.warn('🔥 CacheWarmer: Could not determine tenant slug.');
            return;
        }

        const routes = [
            `/${tenantSlug}/admin/pos`,
            `/${tenantSlug}/admin/products`,
            `/${tenantSlug}/admin/purchase-orders`,
            `/${tenantSlug}/admin/incomes`,
            `/${tenantSlug}/admin/expenses`,
            `/${tenantSlug}/admin/reports/inventory`, // Assuming this is the inventory report
            // Add other critical routes here
        ];

        for (const url of routes) {
            try {
                // Fetch with 'cache: reload' to force SW to update or at least ensure it's in cache
                // Actually, simple fetch is enough if SW is configured NetworkFirst or StaleWhileRevalidate
                // Our Admin pages are NetworkFirst, so a fetch adds them to cache.
                await fetch(url, { priority: 'low' });
                console.log(`🔥 Cached: ${url}`);
            } catch (err) {
                // Ignore errors (offline or network issues), this is just an enhancement
                console.debug(`🔥 Failed to warm: ${url}`, err);
            }
        }

        console.log('🔥 CacheWarmer: Complete.');
    }
};
