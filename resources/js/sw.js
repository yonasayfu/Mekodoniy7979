import { cleanupOutdatedCaches, precacheAndRoute } from 'workbox-precaching';
import { NavigationRoute, registerRoute } from 'workbox-routing';
import { NetworkFirst } from 'workbox-strategies';

// The __WB_MANIFEST token will be replaced by the list of assets to precache.
precacheAndRoute(self.__WB_MANIFEST);

// Cleans up old caches created by previous versions of Workbox.
cleanupOutdatedCaches();

// Use a Network First strategy for navigation requests.
// This ensures users always get the latest version of the page if the network is available.
const navigationRoute = new NavigationRoute(
    new NetworkFirst({
        cacheName: 'navigations',
    }),
);

registerRoute(navigationRoute);

// The 'install' and 'activate' events are handled automatically by Workbox
// when using precacheAndRoute and cleanupOutdatedCaches.

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

const DB_NAME = 'offline-queue-db';
const STORE_NAME = 'sync-queue';

function openQueueDb() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);

        request.onupgradeneeded = () => {
            const db = request.result;
            if (! db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, {
                    keyPath: 'id',
                    autoIncrement: true,
                });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

async function syncOfflineEntries() {
    const db = await openQueueDb();
    const tx = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);
    const items = await store.getAll();

    for (const item of items) {
        try {
            const response = await fetch(item.url, {
                method: item.method,
                headers: item.headers,
                body: item.body ?? null,
            });

            if (! response.ok) {
                throw new Error('Sync failed');
            }

            await store.delete(item.id);
        } catch (error) {
            console.error('Sync error', error);
            throw error;
        }
    }

    await tx.done;
    db.close();
}

self.addEventListener('sync', (event) => {
    if (event.tag === 'offline-submission-sync') {
        event.waitUntil(syncOfflineEntries());
    }
});
