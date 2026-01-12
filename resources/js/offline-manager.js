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
    async queueRequest(url, options = {}) {
        const db = await dbPromise;
        let body = options.body;
        let isFormData = body instanceof FormData;
        let serializedBody = body;

        if (isFormData) {
            // Convert FormData to an array of entries for storage
            // Note: Blobs/Files are stored directly in IDB (native support)
            serializedBody = [];
            for (const [key, value] of body.entries()) {
                serializedBody.push({ key, value });
            }
        }

        const serializedReq = {
            url: url,
            method: options.method || 'POST',
            headers: options.headers ? Array.from(new Headers(options.headers).entries()) : [],
            body: serializedBody,
            isFormData: isFormData,
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
                let body = reqData.body;
                const headers = new Headers(reqData.headers);
                headers.set('X-Offline-Sync', 'true');

                if (reqData.isFormData) {
                    body = new FormData();
                    reqData.body.forEach(item => {
                        body.append(item.key, item.value);
                    });
                    // Browser will set correct multipart boundary, so remove manual content-type if set
                    headers.delete('Content-Type');
                }

                const response = await fetch(reqData.url, {
                    method: reqData.method,
                    headers: headers,
                    body: body
                });

                if (response.ok) {
                    console.log('✅ Synced:', reqData.url);
                    await db.delete(STORE_NAME, reqData.id);
                } else {
                    console.error('❌ Sync failed for:', reqData.url, response.status);
                    // If it's a validation error (422), we might want to notify user or clear it
                    if (response.status === 422) {
                        await db.delete(STORE_NAME, reqData.id);
                    }
                }
            } catch (err) {
                console.error('❌ Sync network error:', err);
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
