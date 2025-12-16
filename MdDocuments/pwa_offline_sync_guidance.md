# PWA Offline Queue and Sync Mechanism Guidance

Implementing a robust "Queue and Sync" mechanism for Progressive Web Apps (PWAs) to handle offline data submissions requires careful coordination between the Service Worker and the main application's frontend logic. This document provides conceptual guidance and implementation steps for a developer.

---

## 1. Overview of the Mechanism

The core idea is to:
1.  **Detect Offline Status**: The application recognizes when it's offline.
2.  **Queue Failed Requests**: When a user performs an action (e.g., creating an elder, pledging support) while offline, and the network request fails, the request's data is stored locally.
3.  **Background Sync**: Once network connectivity is restored, a Service Worker triggers a sync event to resend the queued requests to the server.
4.  **Backend Processing**: The server receives and processes these delayed requests as if they were live submissions.

## 2. Key Technologies Involved

*   **Service Worker**: A JavaScript file that runs in the background, separate from the main web page. It can intercept network requests and manage caching.
*   **IndexedDB**: A low-level API for client-side storage of large amounts of structured data, ideal for queuing requests.
*   **Background Sync API**: Allows your Service Worker to defer tasks (like sending a request to the server) until the user has stable connectivity.

---

## 3. Implementation Steps (Frontend Focus)

### A. Service Worker (`public/service-worker.js` - or similar, managed by VitePWA)

Your `vite-plugin-pwa` setup automatically generates a Service Worker. You'll need to extend its capabilities or interact with it from your main application.

**Conceptual Service Worker Logic (Managed by VitePWA):**

*   **Network Interception**: The Service Worker will intercept `POST`, `PUT`, `DELETE` requests.
*   **Queueing**: If a request fails due to network issues (e.g., `TypeError: Failed to fetch` or `status: 503`), the Service Worker should:
    1.  Read the request's method, URL, headers, and body.
    2.  Store this information in IndexedDB.
    3.  Register a `background-sync` event.

### B. Main Application JavaScript (`resources/js/app.ts` or a dedicated module)

This is where your Vue.js application detects offline status and interacts with the Service Worker.

**1. Detect Offline Status:**

Use browser events to track online/offline state:

```javascript
// In resources/js/app.ts or a composable
import { ref } from 'vue';

const isOnline = ref(navigator.onLine);

window.addEventListener('online', () => {
    isOnline.value = true;
    // Potentially trigger a sync attempt here if not relying solely on Background Sync API
    console.log('App is online!');
});

window.addEventListener('offline', () => {
    isOnline.value = false;
    console.log('App is offline!');
});
```

**2. Modify Axios/Fetch Interceptors to Queue Requests:**

You'll need an interceptor for your HTTP requests (e.g., using `axios` or native `fetch`) that:
*   Tries to send the request normally.
*   If it fails due to network error and `isOnline.value` is `false`:
    1.  Saves the request data (URL, method, headers, body) to IndexedDB.
    2.  Registers a `background-sync` tag via the Service Worker.

```javascript
// Example using Axios (assuming axios is configured globally or imported)
import axios from 'axios';
import { openDB } from 'idb'; // A library like 'idb' simplifies IndexedDB usage

const DB_NAME = 'offline-queue-db';
const STORE_NAME = 'sync-queue';

async function getDb() {
    return openDB(DB_NAME, 1, {
        upgrade(db) {
            db.createObjectStore(STORE_NAME, { autoIncrement: true, keyPath: 'id' });
        },
    });
}

axios.interceptors.response.use(
    response => response,
    async error => {
        if (!navigator.onLine && error.response === undefined) { // Network error
            console.warn('Offline request detected. Queuing request.');
            const db = await getDb();
            const requestData = {
                url: error.config.url,
                method: error.config.method,
                headers: error.config.headers,
                data: error.config.data, // POST/PUT body
                timestamp: new Date().toISOString(),
            };
            await db.add(STORE_NAME, requestData);

            // Register for background sync (requires Service Worker registration)
            if ('serviceWorker' in navigator && 'SyncManager' in window) {
                navigator.serviceWorker.ready.then(reg => {
                    reg.sync.register('offline-submission-sync')
                        .then(() => console.log('Background sync registered'))
                        .catch(err => console.error('Background sync registration failed:', err));
                });
            } else {
                console.warn('Background Sync API not supported. Requests will remain in queue until manually synced or page refresh.');
            }

            // Prevent further error propagation for offline requests
            return new Promise(() => {}); // Keep the promise pending
        }
        return Promise.reject(error);
    }
);
```

**3. Implement Service Worker Sync Event Listener:**

You'll need to modify the generated Service Worker (`public/service-worker.js`) to listen for the `sync` event:

```javascript
// In public/service-worker.js (or wherever VitePWA generates it)
// You might need to inject this into VitePWA's configuration.

// --- Example of how to add to a PWA Service Worker ---
// Assuming your service worker file is accessible and can be modified/extended.
// Check VitePWA's documentation for extending generated SW.

self.addEventListener('sync', event => {
    if (event.tag === 'offline-submission-sync') {
        event.waitUntil(syncOfflineSubmissions());
    }
});

async function syncOfflineSubmissions() {
    console.log('Service Worker: Syncing offline submissions...');
    const db = await openDB(DB_NAME, 1); // Use openDB from 'idb' in SW as well
    const tx = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);
    const queuedRequests = await store.getAll();

    for (const request of queuedRequests) {
        try {
            console.log('Service Worker: Retrying queued request:', request.url);
            const response = await fetch(request.url, {
                method: request.method,
                headers: request.headers,
                body: request.data, // Assuming data is already stringified JSON or FormData
            });

            if (response.ok) {
                console.log('Service Worker: Queued request successfully synced:', request.url);
                await store.delete(request.id); // Remove from queue
            } else {
                console.error('Service Worker: Failed to sync queued request:', request.url, response.statusText);
                // Optionally, handle specific non-network errors (e.g., validation errors)
            }
        } catch (error) {
            console.error('Service Worker: Network error during sync:', request.url, error);
            // Re-throw to make the sync event retry later if it's a network issue
            throw error;
        }
    }
    await tx.done;
    console.log('Service Worker: Offline submissions sync complete.');
}
```

## 4. Considerations

*   **Error Handling**: What happens if a queued request fails permanently (e.g., validation error on the server)? The current approach simply retries. You might need a way for users to review/discard failed offline submissions.
*   **Conflict Resolution**: If the same data is modified offline and online, how are conflicts resolved? (e.g., last-write-wins, user intervention). For simple form submissions, last-write-wins is often sufficient.
*   **User Feedback**: Provide clear UI feedback (e.g., "You are offline, actions will sync later," "Offline changes synced successfully").
*   **VitePWA Configuration**: Check VitePWA documentation for how to effectively inject custom Service Worker logic or use Workbox (which VitePWA is built upon) directly for background sync. You might need to adjust `vite.config.ts` PWA options to configure Workbox's `runtimeCaching` or `workbox.strategies`.

---

This provides a detailed conceptual plan. Actual implementation would involve writing the JavaScript code for the Service Worker and application logic, and thoroughly testing the offline behavior.

Now I will update the `tasklist.md` to reflect that guidance for "Queue and Sync Mechanism for Offline PWA" has been provided.