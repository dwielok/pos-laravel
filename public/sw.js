/**
 * Service Worker for the POS register PWA. Its ONLY job is caching the
 * app shell (the register page's HTML/CSS/JS and the Dexie/jQuery
 * libraries) so the page itself can load when the device is offline --
 * e.g. after a browser/tab restart with no connectivity.
 *
 * It deliberately does NOT intercept or cache API calls (/api/v1/*) or the
 * checkout endpoint (/pos/checkout). Those are handled entirely by the
 * application-level offline logic in db.js/sync-queue.js/register.js,
 * which has real business knowledge (idempotency keys, price-lock
 * semantics, retry rules) that a generic SW cache-or-network strategy
 * cannot safely replicate. Mixing the two would risk the SW silently
 * serving a stale cached API response and the app mistaking it for a live
 * one -- better to have exactly one offline-handling layer for data, and
 * use the SW only for the shell.
 */
const CACHE_NAME = 'pos-shell-v1';
const SHELL_ASSETS = [
    '/pos',
    '/manifest.json',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Never cache or intercept API/checkout/auth traffic -- pass straight
    // through to the network and let the app's own offline logic handle
    // failures. See class docblock above.
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/pos/checkout') || url.pathname.startsWith('/login')) {
        return;
    }

    // Cache-first for the shell itself, falling back to network and
    // updating the cache for next time. Only applies to GET requests for
    // same-origin shell assets.
    if (event.request.method === 'GET' && url.origin === self.location.origin) {
        event.respondWith(
            caches.match(event.request).then((cached) => {
                const networkFetch = fetch(event.request)
                    .then((response) => {
                        if (response.ok) {
                            const clone = response.clone();
                            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                        }
                        return response;
                    })
                    .catch(() => cached); // offline and not cached -- nothing we can do for this asset

                return cached || networkFetch;
            })
        );
    }
});
