// Service Worker — Pre-caching Strategy
// Cache-First for static assets, Stale-While-Revalidate for navigation,
// Network-First for API calls, with offline fallback.

const CACHE_VERSION = 'v2';
const STATIC_CACHE = 'bizmark-static-' + CACHE_VERSION;
const NAV_CACHE    = 'bizmark-navigation-' + CACHE_VERSION;
const ASSET_CACHE  = 'bizmark-assets-' + CACHE_VERSION;

const PRECACHE_URLS = [
    '/',
    '/offline.html',
    '/manifest.json',
];

// Helper: match by pattern (since we can't glob in SW)
const precachePatterns = [
    /^\/build\/assets\/.*\.css$/,
    /^\/build\/assets\/.*\.js$/,
    /^\/fonts\//,
    /^\/images\/(?!og-).*\.(png|jpg|jpeg|webp|svg|ico)$/,
    /^\/favicon\.ico$/,
];

function shouldPrecache(url) {
    const path = new URL(url).pathname;
    return precachePatterns.some(pattern => pattern.test(path));
}

function isNavigation(request) {
    return request.mode === 'navigate' || request.destination === 'document';
}

function isAsset(request) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|webp|woff2?|ttf|eot|ico)$/i.test(request.url) ||
           request.destination === 'style' ||
           request.destination === 'script' ||
           request.destination === 'image' ||
           request.destination === 'font';
}

function isApiCall(urlString) {
    return /\/api\//.test(urlString) ||
           /\/__clockwork\//.test(urlString) ||
           /(gtag|googletagmanager|google-analytics|cloudflareinsights)/.test(urlString);
}

// ─── Install: Pre-cache critical assets ─────────────────────────────────────
self.addEventListener('install', (event) => {
    console.log('[SW] Installing with pre-caching...');
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            console.log('[SW] Pre-caching static assets');
            return cache.addAll(PRECACHE_URLS).catch(err => {
                console.warn('[SW] Some pre-cache items failed:', err);
            });
        }).then(() => {
            return self.skipWaiting();
        })
    );
});

// ─── Activate: Clean up old caches ─────────────────────────────────────────
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating...');
    const expectedCaches = [STATIC_CACHE, NAV_CACHE, ASSET_CACHE];

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (!expectedCaches.includes(cacheName)) {
                        console.log('[SW] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// ─── Fetch: Strategy-based routing ─────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip unsupported protocols
    if (!request.url.startsWith('http')) return;

    // Skip Chrome extension requests
    if (request.url.startsWith('chrome-extension://')) return;

    // Skip API calls / analytics — network only
    if (isApiCall(request.url)) {
        return;
    }

    // Navigation requests — Network-First to avoid stale HTML referencing old hashed CSS/JS
    if (isNavigation(request)) {
        event.respondWith(navNetworkFirst(request));
        return;
    }

    // Static assets — Cache-First
    if (isAsset(request)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Everything else — Network-First with offline fallback
    event.respondWith(networkFirst(request));
});

// ─── Strategies ────────────────────────────────────────────────────────────

// Network-First for navigation to prevent stale HTML assets mismatch
async function navNetworkFirst(request) {
    const cache = await caches.open(NAV_CACHE);
    try {
        const networkResponse = await fetch(request, { cache: 'no-store' });
        if (networkResponse && networkResponse.ok) {
            try {
                await cache.put(request, networkResponse.clone());
            } catch (_) { /* quota exceeded */ }
        }
        return networkResponse;
    } catch (_) {
        const cachedResponse = await cache.match(request);
        if (cachedResponse) return cachedResponse;
        const fallback = await caches.match('/offline.html');
        if (fallback) return fallback;
        return new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
    }
}

// Cache-First for static assets
async function cacheFirst(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.ok) {
            const cache = await caches.open(ASSET_CACHE);
            try {
                await cache.put(request, networkResponse.clone());
            } catch (_) { /* quota exceeded */ }
        }
        return networkResponse;
    } catch (_) {
        // If the asset is an image, return a placeholder
        if (request.destination === 'image') {
            return new Response(
                '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect fill="#1a2545" width="200" height="200"/><text fill="#475569" font-size="14" x="50%" y="50%" text-anchor="middle" dy=".3em">Offline</text></svg>',
                { headers: { 'Content-Type': 'image/svg+xml' } }
            );
        }
        throw _;
    }
}

// Network-First with offline fallback
async function networkFirst(request) {
    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.ok) {
            const cache = await caches.open(NAV_CACHE);
            try {
                await cache.put(request, networkResponse.clone());
            } catch (_) { /* quota exceeded */ }
        }
        return networkResponse;
    } catch (_) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        // Offline fallback for navigation
        if (request.mode === 'navigate' || request.destination === 'document') {
            const fallback = await caches.match('/offline.html');
            if (fallback) return fallback;
        }
        // Graceful fallback for non-critical third-party resources (analytics, beacons, etc.)
        console.warn('[SW] Network request failed, returning empty response:', request.url);
        return new Response(null, { status: 204, statusText: 'No Content' });
    }
}

// ─── Push notification handler (preserved for future use) ─────────────────
self.addEventListener('push', (event) => {
    if (!event.data) return;
    try {
        const data = event.data.json();
        const options = {
            body: data.body || '',
            icon: data.icon || '/images/icon-192.png',
            badge: '/images/icon-192.png',
            data: { url: data.url || '/' }
        };
        event.waitUntil(
            self.registration.showNotification(
                data.title || 'Bizmark.ID',
                options
            )
        );
    } catch (_) {
        // Silent fail for malformed push payloads
    }
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then((windowClients) => {
            const matching = windowClients.find(c => c.url === url);
            if (matching) {
                matching.focus();
            } else {
                clients.openWindow(url);
            }
        })
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

console.log('[SW] Pre-caching service worker loaded');
