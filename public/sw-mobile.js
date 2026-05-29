/**
 * Bizmark Mobile PWA Service Worker
 * Version: 1.1.0
 * Date: April 14, 2026
 * 
 * Features:
 * - Offline support with cache-first strategy
 * - Background sync for form submissions (IndexedDB)
 * - Push notification handling
 * - Smart caching for API responses
 * - Automatic retry on reconnection
 */

const CACHE_VERSION = 'v1.1.1';
const STATIC_CACHE = `bizmark-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE = `bizmark-dynamic-${CACHE_VERSION}`;
const API_CACHE = `bizmark-api-${CACHE_VERSION}`;

// IndexedDB Configuration (matching indexeddb.js)
const DB_NAME = 'bizmark-mobile-db';
const DB_VERSION = 1;
const PENDING_STORE = 'pending-requests';

// Static assets to cache immediately
const STATIC_ASSETS = [
    '/m/offline',
    '/manifest.json',
    '/images/logo-bizmark.svg',
    '/images/favicon.svg',
    '/favicon.ico',
    '/js/pwa/indexeddb.js',
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js'
];

// Pages to cache on first visit
const MOBILE_PAGES = [
    '/m',
    '/m/projects',
    '/m/tasks',
    '/m/approvals',
    '/m/financial',
    '/m/notifications',
    '/m/profile',
    '/m/settings/preferences'
];

// API endpoints to cache (short TTL)
const API_ENDPOINTS = [
    '/m/dashboard/refresh',
    '/m/notifications/unread-count'
];

/**
 * Install event - cache static assets
 */
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Bizmark Mobile PWA...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('[SW] Caching static assets...');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('[SW] Static assets cached');
                return self.skipWaiting();
            })
            .catch(err => {
                console.error('[SW] Failed to cache static assets:', err);
            })
    );
});

/**
 * Activate event - cleanup old caches
 */
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Bizmark Mobile PWA...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames
                        .filter(name => {
                            return name.startsWith('bizmark-') && 
                                   !name.includes(CACHE_VERSION);
                        })
                        .map(name => {
                            console.log('[SW] Deleting old cache:', name);
                            return caches.delete(name);
                        })
                );
            })
            .then(() => {
                console.log('[SW] Old caches cleared');
                return self.clients.claim();
            })
    );
});

/**
 * Fetch event - smart caching strategies
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests and external resources we don't control
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip certain paths
    if (url.pathname.startsWith('/livewire') || 
        url.pathname.startsWith('/_debugbar') ||
        url.pathname.startsWith('/telescope')) {
        return;
    }
    
    // Strategy: Cache First for static assets
    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request));
        return;
    }
    
    // Strategy: Network First for API calls
    if (isApiCall(url)) {
        event.respondWith(networkFirst(request));
        return;
    }
    
    // Strategy: Network First for mobile pages to prevent stale HTML/CSS references
    if (isMobilePage(url)) {
        event.respondWith(pageNetworkFirst(request));
        return;
    }
    
    // Default: Network First with fallback
    event.respondWith(networkFirstWithFallback(request));
});

/**
 * Push notification event
 */
self.addEventListener('push', (event) => {
    console.log('[SW] Push received:', event);
    
    let data = {
        title: 'Bizmark Notification',
        body: 'Ada update baru di dashboard Anda',
        icon: '/images/logo-bizmark.svg',
        badge: '/images/favicon.svg',
        tag: 'bizmark-notification',
        data: { url: '/m' }
    };
    
    try {
        if (event.data) {
            data = { ...data, ...event.data.json() };
        }
    } catch (e) {
        console.error('[SW] Failed to parse push data:', e);
    }
    
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            badge: data.badge,
            tag: data.tag,
            data: data.data,
            vibrate: [100, 50, 100],
            requireInteraction: data.requireInteraction || false,
            actions: data.actions || [
                { action: 'open', title: 'Buka' },
                { action: 'dismiss', title: 'Tutup' }
            ]
        })
    );
});

/**
 * Notification click event
 */
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked:', event);
    
    event.notification.close();
    
    const action = event.action;
    const url = event.notification.data?.url || '/m';
    
    if (action === 'dismiss') {
        return;
    }
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Try to focus existing window
                for (const client of clientList) {
                    if (client.url.includes('/m') && 'focus' in client) {
                        client.navigate(url);
                        return client.focus();
                    }
                }
                // Open new window if none exists
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            })
    );
});

/**
 * Background sync event
 */
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);
    
    if (event.tag === 'sync-pending-data') {
        event.waitUntil(syncPendingData());
    }
});

/**
 * Message event (from main thread)
 */
self.addEventListener('message', (event) => {
    console.log('[SW] Message received:', event.data);
    
    if (event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data.type === 'CACHE_PAGE') {
        caches.open(DYNAMIC_CACHE).then(cache => {
            cache.add(event.data.url);
        });
    }
    
    if (event.data.type === 'CLEAR_CACHE') {
        caches.keys().then(names => {
            names.forEach(name => caches.delete(name));
        });
    }
});

// ============= Helper Functions =============

function isStaticAsset(url) {
    const staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf'];
    return staticExtensions.some(ext => url.pathname.endsWith(ext)) ||
           STATIC_ASSETS.some(asset => url.href.includes(asset));
}

function isApiCall(url) {
    return url.pathname.includes('/api/') || 
           url.pathname.includes('/refresh') ||
           url.pathname.includes('/unread-count');
}

function isMobilePage(url) {
    return url.pathname.startsWith('/m/') || url.pathname === '/m';
}

/**
 * Cache First strategy - best for static assets
 */
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) {
        return cached;
    }
    
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        console.error('[SW] Cache first failed:', error);
        return caches.match('/m/offline');
    }
}

/**
 * Network First strategy - best for API calls
 */
async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(API_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }
        return new Response(JSON.stringify({ error: 'Offline', cached: false }), {
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

/**
 * Network First - best for pages to avoid stale asset references after deploy
 */
async function pageNetworkFirst(request) {
    const cache = await caches.open(DYNAMIC_CACHE);
    try {
        const response = await fetch(request, { cache: 'no-store' });
        if (response.ok) {
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await cache.match(request);
        if (cached) {
            return cached;
        }
        return caches.match('/m/offline');
    }
}

/**
 * Network First with offline fallback
 */
async function networkFirstWithFallback(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        const cached = await caches.match(request);
        if (cached) {
            return cached;
        }
        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match('/m/offline');
        }
        throw error;
    }
}

/**
 * Sync pending data when back online
 */
async function syncPendingData() {
    console.log('[SW] Starting background sync...');
    
    try {
        const pendingRequests = await getIDBPendingRequests();
        console.log(`[SW] Found ${pendingRequests.length} pending requests`);
        
        for (const request of pendingRequests) {
            if (request.retryCount >= request.maxRetries) {
                console.log(`[SW] Max retries reached for request ${request.id}, marking as failed`);
                await updateIDBRequest(request.id, { status: 'failed' });
                continue;
            }
            
            try {
                const response = await fetch(request.url, {
                    method: request.method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': request.csrfToken || '',
                        'X-Background-Sync': 'true'
                    },
                    body: request.body ? JSON.stringify(request.body) : null
                });
                
                if (response.ok) {
                    console.log(`[SW] Successfully synced request ${request.id}`);
                    await removeIDBRequest(request.id);
                    
                    // Notify client of successful sync
                    const clients = await self.clients.matchAll();
                    clients.forEach(client => {
                        client.postMessage({
                            type: 'SYNC_SUCCESS',
                            requestId: request.id,
                            requestType: request.type
                        });
                    });
                } else {
                    throw new Error(`Server responded with ${response.status}`);
                }
            } catch (error) {
                console.error(`[SW] Failed to sync request ${request.id}:`, error);
                await updateIDBRequest(request.id, { 
                    retryCount: request.retryCount + 1,
                    lastError: error.message 
                });
            }
        }
        
        console.log('[SW] Background sync complete');
    } catch (error) {
        console.error('[SW] Background sync failed:', error);
    }
}

// ============= IndexedDB Helper Functions (Service Worker Context) =============

/**
 * Open IndexedDB connection
 */
function openIDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        
        // Handle upgrade if needed (first time in SW)
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(PENDING_STORE)) {
                const store = db.createObjectStore(PENDING_STORE, { keyPath: 'id', autoIncrement: true });
                store.createIndex('status', 'status', { unique: false });
            }
        };
    });
}

/**
 * Get pending requests from IndexedDB
 */
async function getIDBPendingRequests() {
    const db = await openIDB();
    
    return new Promise((resolve, reject) => {
        const tx = db.transaction([PENDING_STORE], 'readonly');
        const store = tx.objectStore(PENDING_STORE);
        const index = store.index('status');
        const request = index.getAll('pending');
        
        request.onsuccess = () => resolve(request.result || []);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Update request in IndexedDB
 */
async function updateIDBRequest(id, updates) {
    const db = await openIDB();
    
    return new Promise((resolve, reject) => {
        const tx = db.transaction([PENDING_STORE], 'readwrite');
        const store = tx.objectStore(PENDING_STORE);
        const getReq = store.get(id);
        
        getReq.onsuccess = () => {
            const item = getReq.result;
            if (item) {
                Object.assign(item, updates);
                const putReq = store.put(item);
                putReq.onsuccess = () => resolve();
                putReq.onerror = () => reject(putReq.error);
            } else {
                reject(new Error('Item not found'));
            }
        };
        getReq.onerror = () => reject(getReq.error);
    });
}

/**
 * Remove request from IndexedDB
 */
async function removeIDBRequest(id) {
    const db = await openIDB();
    
    return new Promise((resolve, reject) => {
        const tx = db.transaction([PENDING_STORE], 'readwrite');
        const store = tx.objectStore(PENDING_STORE);
        const request = store.delete(id);
        
        request.onsuccess = () => resolve();
        request.onerror = () => reject(request.error);
    });
}

console.log('[SW] Bizmark Mobile PWA Service Worker v1.1.0 loaded');
