import { openDB } from 'idb';

const DB_NAME = 'mini-store-db';
const STORE_NAME = 'sync-queue';

// Initialize DB
const dbPromise = openDB(DB_NAME, 1, {
    upgrade(db) {
        db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
    },
});

export const OfflineManager = {
    // Queue a request for later processing
    async queueRequest(request) {
        const db = await dbPromise;
        const serializedReq = {
            url: request.url,
            method: request.method,
            headers: Array.from(request.headers.entries()),
            body: await request.clone().text(),
            timestamp: Date.now()
        };

        await db.add(STORE_NAME, serializedReq);
        console.log('🔌 Request queued for offline sync:', serializedReq.url);

        // Dispatch event to update UI
        window.dispatchEvent(new CustomEvent('offline-queue-updated'));
    },

    // Process the queue
    async processQueue() {
        const db = await dbPromise;
        const queue = await db.getAll(STORE_NAME);

        if (queue.length === 0) return;

        console.log(`🔄 Processing ${queue.length} offline requests...`);

        for (const reqData of queue) {
            try {
                const headers = new Headers(reqData.headers);
                headers.set('X-Offline-Sync', 'true'); // Tag regular requests

                const response = await fetch(reqData.url, {
                    method: reqData.method,
                    headers: headers,
                    body: reqData.body
                });

                if (response.ok) {
                    console.log('✅ Synced:', reqData.url);
                    await db.delete(STORE_NAME, reqData.id);
                } else {
                    console.error('❌ Sync failed for:', reqData.url, response.status);
                    // Optionally keep in queue or move to a 'failed' store
                }
            } catch (err) {
                console.error('❌ Sync network error:', err);
                // Stop processing if network is down again
                break;
            }
        }

        window.dispatchEvent(new CustomEvent('offline-queue-updated'));
    },

    async getQueueCount() {
        const db = await dbPromise;
        return await db.count(STORE_NAME);
    }
};

// Auto-register network listeners
window.addEventListener('online', () => {
    console.log('🌐 Online - Attempting sync...');
    OfflineManager.processQueue();
});
