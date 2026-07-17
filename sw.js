/**
 * Service Worker for Urji Beri School Website
 * Enables offline functionality and PWA features
 */

const CACHE_NAME = 'urji-beri-v5';
const OFFLINE_URL = '/offline.html';

// Static assets to cache (not dynamic PHP pages)
const STATIC_ASSETS = [
    '/offline.html',
    '/assets/css/style.css',
    '/assets/js/main.js',
    '/assets/images/logo.png',
    '/assets/images/logo-white.png',
    '/manifest.json'
];

// Install event - cache static assets only
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Check if request is for a static asset
function isStaticAsset(url) {
    return url.includes('/assets/') || 
           url.includes('/uploads/') ||
           url.endsWith('.css') || 
           url.endsWith('.js') || 
           url.endsWith('.png') || 
           url.endsWith('.jpg') || 
           url.endsWith('.jpeg') || 
           url.endsWith('.gif') || 
           url.endsWith('.svg') ||
           url.endsWith('.woff') ||
           url.endsWith('.woff2') ||
           url.endsWith('.ico') ||
           url.includes('manifest.json');
}

// Fetch event - Network-first for PHP, Cache-first for static assets
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;
    
    // Skip admin routes entirely
    if (event.request.url.includes('/admin/')) return;
    
    // Skip external requests
    if (!event.request.url.startsWith(self.location.origin)) return;

    // For static assets - use cache-first strategy
    if (isStaticAsset(event.request.url)) {
        event.respondWith(
            caches.match(event.request)
                .then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return fetch(event.request)
                        .then((response) => {
                            if (!response || response.status !== 200) {
                                return response;
                            }
                            const responseToCache = response.clone();
                            caches.open(CACHE_NAME)
                                .then((cache) => cache.put(event.request, responseToCache));
                            return response;
                        });
                })
        );
        return;
    }

    // For PHP pages (dynamic content) - use network-first strategy
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Check if valid response
                if (!response || response.status !== 200) {
                    return response;
                }
                
                // Clone response and cache it for offline use
                const responseToCache = response.clone();
                caches.open(CACHE_NAME)
                    .then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                
                return response;
            })
            .catch(() => {
                // Network failed - try cache, then offline page
                return caches.match(event.request)
                    .then((cachedResponse) => {
                        if (cachedResponse) {
                            return cachedResponse;
                        }
                        // Return offline page for navigation requests
                        if (event.request.mode === 'navigate') {
                            return caches.match(OFFLINE_URL);
                        }
                    });
            })
    );
});

// Handle messages from the app
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    // Allow clearing cache from the app
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        caches.delete(CACHE_NAME).then(() => {
            console.log('Cache cleared');
        });
    }
});
