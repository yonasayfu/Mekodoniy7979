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
