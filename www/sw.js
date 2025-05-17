// Service Worker for Notes PWA

const CACHE_NAME = 'notes-app-v1';
const DYNAMIC_CACHE = 'notes-dynamic-v1';

// Assets to cache on install
const ASSETS_TO_CACHE = [
  '/',
  '/index.php',
  '/edit.php',
  '/css/style.css',
  '/js/index.js',
  '/js/edit.js',
  '/js/noteActions.js',
  '/js/generateList.js',
  '/js/generateGrid.js',
  '/js/utils.js',
  '/js/idb.js',
  '/js/offlineNotes.js',
  '/images/Skeleton.png',
  '/images/default_profile_pic.jpg',
  '/images/icon-192.png',
  '/images/icon-512.png',
  '/images/maskable-icon.png',
  '/offline.html',
  '/offline-fallback.html',
  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css',
  'https://code.jquery.com/jquery-3.7.1.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js',
  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js'
];

// Install event - cache static assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Caching app shell');
        return cache.addAll(ASSETS_TO_CACHE);
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME, DYNAMIC_CACHE];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

// Function to check if user is authenticated
async function isAuthenticated() {
  try {
    const response = await fetch('/api/get_user_info.php', {
      credentials: 'include' // Important for sending cookies
    });
    const data = await response.json();
    return data.code === 0; // User is authenticated if code is 0
  } catch (error) {
    console.error('Auth check failed:', error);
    return false;
  }
}

// Function to clear all caches
async function clearAllCaches() {
  const cacheNames = await caches.keys();
  return Promise.all(
    cacheNames.map(cacheName => caches.delete(cacheName))
  );
}

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
  // Skip non-GET requests and browser extensions
  if (event.request.method !== 'GET' || 
      event.request.url.startsWith('chrome-extension') ||
      event.request.url.includes('extension')) {
    return;
  }

  // API requests - handle differently for offline functionality
  if (event.request.url.includes('/api/')) {
    // For API requests, try network first, then fallback to custom offline handling
    event.respondWith(
      (async () => {
        try {
          // Check authentication for API requests
          const isAuth = await isAuthenticated();
          if (!isAuth && !event.request.url.includes('/login_service.php')) {
            // If not authenticated and not trying to login, return unauthorized
            return new Response(JSON.stringify({ 
              code: 3, 
              message: 'Authentication required'
            }), {
              headers: { 'Content-Type': 'application/json' }
            });
          }

          const response = await fetch(event.request);
          // Cache successful API responses in dynamic cache
          const clonedResponse = response.clone();
          caches.open(DYNAMIC_CACHE).then(cache => {
            cache.put(event.request, clonedResponse);
          });
          return response;
        } catch (error) {
          console.log('API fetch failed, falling back to offline handling', error);
          // Send message to client to handle from IndexedDB
          return new Response(JSON.stringify({ 
            code: -1, 
            message: 'You are offline. Data served from local storage.',
            offline: true
          }), {
            headers: { 'Content-Type': 'application/json' }
          });
        }
      })()
    );
  } else {
    // For non-API requests, use cache-first strategy
    event.respondWith(
      caches.match(event.request)
        .then(response => {
          if (response) {
            return response;
          }
          
          // If not in cache, fetch from network and add to dynamic cache
          return fetch(event.request)
            .then(networkResponse => {
              // Cache a copy of the response
              const responseToCache = networkResponse.clone();
              caches.open(DYNAMIC_CACHE).then(cache => {
                cache.put(event.request, responseToCache);
              });
              return networkResponse;
            })
            .catch(error => {
              console.log('Fetch failed', error);
              // For HTML pages, return appropriate offline page
              if (event.request.headers.get('accept').includes('text/html')) {
                // Use offline.html for the main page and offline-fallback.html for other pages
                if (event.request.url.includes('/index.php') ||
                    event.request.url.endsWith('/') ||
                    event.request.url.endsWith('/index.html')) {
                  return caches.match('/offline.html');
                } else {
                  return caches.match('/offline-fallback.html');
                }
              }
              
              // Return graceful error for other resources
              return new Response('Offline. Resource unavailable.', {
                status: 503,
                statusText: 'Service Unavailable',
                headers: new Headers({
                  'Content-Type': 'text/plain'
                })
              });
            });
        })
    );
  }
});

// Handle background sync for offline data
self.addEventListener('sync', event => {
  if (event.tag === 'sync-notes') {
    event.waitUntil(syncNotes());
  }
});

// Function to sync notes when back online
function syncNotes() {
  // This will be implemented with IndexedDB
  // This is a placeholder for now
  console.log('Syncing notes with server');
  
  // We'll send a message to the client to handle the sync
  return self.clients.matchAll()
    .then(clients => {
      clients.forEach(client => {
        client.postMessage({
          type: 'SYNC_REQUIRED'
        });
      });
    });
}

// Listen for messages from client
self.addEventListener('message', event => {
  if (event.data.action === 'skipWaiting') {
    self.skipWaiting();
  } else if (event.data.action === 'logout') {
    // Clear all caches when user logs out
    event.waitUntil(clearAllCaches());
  }
}); 