// Service Worker for Bizmark.ID PWA
const CACHE_VERSION = 'v2.1.0';
const STATIC_CACHE = `bizmark-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE = `bizmark-dynamic-${CACHE_VERSION}`;
const IMAGE_CACHE = `bizmark-images-${CACHE_VERSION}`;

// Static assets to cache immediately
const STATIC_ASSETS = [
  '/',
  '/offline.html',
  '/favicon.ico',
  '/manifest.json',
  'https://cdn.tailwindcss.com',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
  console.log('[SW] Installing service worker...');
  
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('[SW] Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      })
      .then(() => self.skipWaiting())
  );
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
  console.log('[SW] Activating service worker...');
  
  event.waitUntil(
    caches.keys()
      .then(cacheNames => {
        return Promise.all(
          cacheNames
            .filter(cacheName => {
              return cacheName.startsWith('bizmark-') && 
                     cacheName !== STATIC_CACHE && 
                     cacheName !== DYNAMIC_CACHE &&
                     cacheName !== IMAGE_CACHE;
            })
            .map(cacheName => {
              console.log('[SW] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            })
        );
      })
      .then(() => self.clients.claim())
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
  console.log('[SW] Push notification received');
  
  let data = {};
  
  try {
    data = event.data ? event.data.json() : {};
  } catch (error) {
    console.error('[SW] Error parsing push data:', error);
    data = {
      title: 'Bizmark.ID',
      body: 'Ada update baru dari Bizmark.ID'
    };
  }
  
  const options = {
    body: data.body || 'Ada update baru dari Bizmark.ID',
    icon: data.icon || '/icons/icon-192x192.png',
    badge: data.badge || '/icons/badge-72x72.png',
    vibrate: data.vibrate || [200, 100, 200],
    data: {
      url: data.url || '/client/dashboard',
      application_id: data.application_id,
      application_number: data.application_number,
      status: data.status
    },
    actions: [
      {
        action: 'open',
        title: 'Buka',
        icon: '/icons/icon-192x192.png'
      },
      {
        action: 'close',
        title: 'Tutup'
      }
    ],
    tag: data.tag || 'bizmark-notification',
    requireInteraction: data.requireInteraction || false,
    renotify: true,
    timestamp: Date.now()
  };
  
  event.waitUntil(
    self.registration.showNotification(data.title || 'Bizmark.ID', options)
  );
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

console.log('[SW] Service worker loaded successfully');
