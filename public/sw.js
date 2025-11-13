// Force unregister - this SW immediately unregisters itself
self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    return caches.delete(cacheName);
                })
            );
        }).then(function() {
            return self.registration.unregister();
        }).then(function() {
            return clients.matchAll();
        }).then(function(clients) {
            clients.forEach(client => client.navigate(client.url));
        })
    );
});
