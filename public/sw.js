// Service Worker for Bizmark.ID PWA
// IMPORTANT: Increment this version when you make ANY changes to the app
const CACHE_VERSION = 'v2.5.3'; // Removed connection check - cleaner UX
const BUILD_TIMESTAMP = '2025-11-18T22:05:00Z'; // Auto-generated on build
const STATIC_CACHE = `bizmark-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE = `bizmark-dynamic-${CACHE_VERSION}`;
const IMAGE_CACHE = `bizmark-images-${CACHE_VERSION}`;
const MOBILE_CACHE = `bizmark-mobile-${CACHE_VERSION}`; // New cache for mobile routes
const DEFAULT_VIBRATION_PATTERN = [200, 100, 200, 100, 200];
const DEFAULT_NOTIFICATION_ACTIONS = [
  { action: 'open', title: 'Buka Detail', icon: '/icons/icon-192x192.png' },
  { action: 'mark-read', title: 'Tandai Dibaca' }
];

function normalizePushPayload(raw) {
  if (!raw) {
    return {};
  }

  // Support payloads that wrap notification data
  if (raw.notification) {
    const merged = { ...raw.notification };
    const mergedData = {
      ...(raw.notification.data || {}),
      ...(raw.data || {})
    };
    if (Object.keys(mergedData).length) {
      merged.data = mergedData;
    }
    return merged;
  }

  return raw;
}

function buildNotificationOptions(payload) {
  const data = payload.data || {};
  const targetUrl = payload.url || data.url || '/client/dashboard';

  return {
    body: payload.body || data.body || 'Ada update baru dari Bizmark.ID',
    icon: payload.icon || data.icon || '/icons/icon-192x192.png',
    badge: payload.badge || data.badge || '/icons/badge-72x72.png',
    image: payload.image || data.image,
    vibrate: payload.vibrate || data.vibrate || DEFAULT_VIBRATION_PATTERN,
    tag: payload.tag || data.tag || `bizmark-${Date.now()}`,
    requireInteraction: payload.requireInteraction ?? data.requireInteraction ?? true,
    renotify: payload.renotify ?? data.renotify ?? true,
    timestamp: payload.timestamp || data.timestamp || Date.now(),
    dir: payload.dir || 'auto',
    lang: payload.lang || 'id-ID',
    silent: payload.silent ?? false,
    actions: payload.actions || data.actions || DEFAULT_NOTIFICATION_ACTIONS,
    data: {
      ...data,
      url: targetUrl,
      deliveredAt: Date.now(),
      origin: data.origin || 'push'
    }
  };
}

async function broadcastMessage(message) {
  const windowClients = await self.clients.matchAll({
    type: 'window',
    includeUncontrolled: true
  });

  windowClients.forEach((client) => {
    try {
      client.postMessage(message);
    } catch (error) {
      console.warn('[SW] Failed to broadcast message to client:', error);
    }
  });
}

// Static assets to cache immediately (only essential files that definitely exist)
const STATIC_ASSETS = [
  '/favicon.ico',
  '/manifest.json'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
  console.log(`[SW] Installing service worker ${CACHE_VERSION}...`);
  
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('[SW] Attempting to cache static assets');
        // Cache assets individually to avoid failing if one doesn't exist
        return Promise.allSettled(
          STATIC_ASSETS.map(url => {
            return cache.add(url).catch(err => {
              console.warn(`[SW] Failed to cache ${url}:`, err);
              return null;
            });
          })
        );
      })
      .then(() => {
        console.log('[SW] Static assets cached (with some possible failures)');
        console.log('[SW] Skip waiting - force activation');
        return self.skipWaiting(); // Force the waiting service worker to become active
      })
      .catch(error => {
        console.error('[SW] Install failed:', error);
        // Don't fail installation even if caching fails
        return self.skipWaiting();
      })
  );
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
  console.log(`[SW] Activating service worker ${CACHE_VERSION}...`);
  
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames
            .filter(cacheName => {
              return cacheName.startsWith('bizmark-') && 
                     cacheName !== STATIC_CACHE && 
                     cacheName !== DYNAMIC_CACHE &&
                     cacheName !== IMAGE_CACHE &&
                     cacheName !== MOBILE_CACHE; // Keep mobile cache
            })
            .map(cacheName => {
              console.log('[SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            })
        );
      })
      .then(() => {
        console.log('[SW] Claiming all clients');
        return self.clients.claim(); // Take control of all pages immediately
      })
      .then(() => {
        // Notify all clients about the update
        return self.clients.matchAll().then(clients => {
          clients.forEach(client => {
            client.postMessage({
              type: 'SW_UPDATED',
              version: CACHE_VERSION,
              timestamp: BUILD_TIMESTAMP
            });
          });
        });
      })
  );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }
  
  // Skip admin panel routes (requires authentication)
  if (url.pathname.startsWith('/admin') || 
      url.pathname.startsWith('/hadez') ||
      url.pathname.startsWith('/api/')) {
    return;
  }
  
  // Handle different types of requests
  if (request.destination === 'image') {
    event.respondWith(cacheFirstImage(request));
  } else if (url.pathname.startsWith('/m/')) {
    // Mobile routes - aggressive caching for offline support
    event.respondWith(networkFirstMobile(request));
  } else if (url.pathname.startsWith('/client/')) {
    event.respondWith(networkFirstClient(request));
  } else {
    event.respondWith(cacheFirstStatic(request));
  }
});

// Cache-first strategy for static assets
async function cacheFirstStatic(request) {
  const cache = await caches.open(STATIC_CACHE);
  const cached = await cache.match(request);
  
  if (cached) {
    console.log('[SW] Serving from cache:', request.url);
    return cached;
  }
  
  try {
    const response = await fetch(request);
    
    if (response.ok) {
      cache.put(request, response.clone());
    }
    
    return response;
  } catch (error) {
    console.error('[SW] Fetch failed:', error);
    
    // Return offline page for navigation requests
    if (request.mode === 'navigate') {
      const offlinePage = await cache.match('/offline.html');
      if (offlinePage) return offlinePage;
    }
    
    return new Response('Offline', {
      status: 503,
      statusText: 'Service Unavailable',
      headers: new Headers({ 'Content-Type': 'text/plain' })
    });
  }
}

// Network-first strategy for client portal (dynamic content)
async function networkFirstClient(request) {
  const cache = await caches.open(DYNAMIC_CACHE);
  
  try {
    const response = await fetch(request);
    
    if (response.ok) {
      cache.put(request, response.clone());
    }
    
    return response;
  } catch (error) {
    console.log('[SW] Network failed, trying cache:', request.url);
    
    const cached = await cache.match(request);
    if (cached) {
      return cached;
    }
    
    // Return offline page for navigation
    if (request.mode === 'navigate') {
      const offlinePage = await cache.match('/offline.html');
      if (offlinePage) return offlinePage;
    }
    
    return new Response('Offline', {
      status: 503,
      statusText: 'Service Unavailable'
    });
  }
}

// Network-first for mobile admin with aggressive caching
async function networkFirstMobile(request) {
  const cache = await caches.open(MOBILE_CACHE);
  
  try {
    const response = await fetch(request, {
      // Mobile optimizations
      headers: {
        'X-Mobile-Request': 'true'
      }
    });
    
    if (response.ok) {
      // Cache all successful mobile responses
      cache.put(request, response.clone());
    }
    
    return response;
  } catch (error) {
    console.log('[SW] Mobile network failed, trying cache:', request.url);
    
    const cached = await cache.match(request);
    if (cached) {
      // Add offline indicator header
      const headers = new Headers(cached.headers);
      headers.set('X-Served-From-Cache', 'true');
      
      return new Response(cached.body, {
        status: cached.status,
        statusText: cached.statusText,
        headers: headers
      });
    }
    
    // Return mobile offline page
    if (request.mode === 'navigate') {
      const offlinePage = await cache.match('/mobile-offline.html');
      if (offlinePage) return offlinePage;
      
      // Fallback to generic offline page
      const genericOffline = await cache.match('/offline.html');
      if (genericOffline) return genericOffline;
    }
    
    return new Response('Offline - Data akan disinkronkan saat online', {
      status: 503,
      statusText: 'Service Unavailable',
      headers: new Headers({ 
        'Content-Type': 'text/plain',
        'X-Offline-Mode': 'true' 
      })
    });
  }
}

// Cache-first strategy for images
async function cacheFirstImage(request) {
  const cache = await caches.open(IMAGE_CACHE);
  const cached = await cache.match(request);
  
  if (cached) {
    return cached;
  }
  
  try {
    const response = await fetch(request);
    
    if (response.ok) {
      cache.put(request, response.clone());
    }
    
    return response;
  } catch (error) {
    // Return placeholder image or error
    return new Response('', {
      status: 404,
      statusText: 'Image Not Found'
    });
  }
}

// Background sync for form submissions (future enhancement)
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-forms') {
    event.waitUntil(syncForms());
  }
});

async function syncForms() {
  // Implementation for syncing offline form submissions
  console.log('[SW] Syncing offline forms...');
}

// Push notification handler
self.addEventListener('push', (event) => {
  event.waitUntil((async () => {
    console.group('[SW] Push Event');
    console.log('[SW] Raw event:', event);

    let rawPayload = {};

    try {
      rawPayload = event.data ? event.data.json() : {};
    } catch (error) {
      console.error('[SW] Error parsing push payload:', error);
      rawPayload = {
        title: 'Bizmark.ID',
        body: 'Ada update baru dari Bizmark.ID'
      };
    }

    const payload = normalizePushPayload(rawPayload);
    const title = payload.title || payload.data?.title || 'Bizmark.ID';
    const options = buildNotificationOptions(payload);

    console.log('[SW] Normalized payload:', payload);
    console.log('[SW] Notification options:', options);

    await self.registration.showNotification(title, options);
    console.log('[SW] Notification displayed:', title);

    await broadcastMessage({
      type: 'PUSH_NOTIFICATION_DELIVERED',
      title,
      body: options.body,
      data: options.data,
      tag: options.tag,
      timestamp: options.timestamp
    });

    console.groupEnd();
  })().catch((error) => {
    console.error('[SW] Failed to display push notification:', error);
  }));
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
  console.log('[SW] Notification clicked:', event.action);
  
  event.notification.close();
  
  // Handle different actions
  if (event.action === 'close') {
    return;
  }
  
  // Default action or 'open' action
  const urlToOpen = event.notification.data.url || '/client/dashboard';
  
  event.waitUntil(
    clients.matchAll({
      type: 'window',
      includeUncontrolled: true
    }).then(windowClients => {
      // Check if there's already a window open
      for (let client of windowClients) {
        if (client.url.includes('/client') && 'focus' in client) {
          // Focus existing window and navigate to URL
          return client.focus().then(client => {
            return client.navigate(urlToOpen);
          });
        }
      }
      
      // No window open, open new one
      if (clients.openWindow) {
        return clients.openWindow(urlToOpen);
      }
    })
  );
});

// Message handler - receive commands from client
self.addEventListener('message', (event) => {
  console.log('[SW] Message received:', event.data);
  
  if (event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data.type === 'CHECK_VERSION') {
    event.ports[0].postMessage({
      version: CACHE_VERSION,
      timestamp: BUILD_TIMESTAMP
    });
  }
});

console.log(`[SW] Service worker ${CACHE_VERSION} loaded successfully`);
