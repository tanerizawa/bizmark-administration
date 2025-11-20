// Service Worker - Unregister Only
// This service worker clears all caches and unregisters itself

self.addEventListener('install', (event) => {
    console.log('[SW] Installing cleanup service worker...');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('[SW] Activating cleanup service worker...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    console.log('[SW] Deleting cache:', cacheName);
                    return caches.delete(cacheName);
                })
            );
        }).then(() => {
            console.log('[SW] All caches cleared');
            return self.clients.claim();
        }).then(() => {
            return self.registration.unregister();
        }).then(() => {
            console.log('[SW] Service worker unregistered');
        })
    );
});

// Don't intercept fetch requests
self.addEventListener('fetch', (event) => {
    return;
});

console.log('[SW] Cleanup service worker loaded');
