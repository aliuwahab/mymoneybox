const CACHE = 'mpb-v3';

// ── Install: pre-cache the offline fallback ───────────────────────────────────
self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE).then((c) => c.add('/offline').catch(() => {}))
    );
});

// ── Activate: evict stale caches ─────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

// ── Fetch ─────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const req = event.request;
    const url = new URL(req.url);

    // Only intercept same-origin GET requests
    if (req.method !== 'GET' || url.origin !== location.origin) return;

    // Never intercept webhooks, API, or Livewire endpoints
    if (/^\/(webhooks|api|livewire)\//.test(url.pathname)) return;

    // Vite build assets (/build/) — cache-first (content-hashed, never stale)
    if (url.pathname.startsWith('/build/')) {
        event.respondWith(
            caches.match(req).then((hit) => {
                if (hit) return hit;
                return fetch(req).then((res) => {
                    if (res.ok) {
                        const clone = res.clone();
                        caches.open(CACHE).then((c) => c.put(req, clone));
                    }
                    return res;
                });
            })
        );
        return;
    }

    // Images and fonts — stale-while-revalidate
    if (req.destination === 'image' || req.destination === 'font') {
        event.respondWith(
            caches.open(CACHE).then((cache) =>
                cache.match(req).then((cached) => {
                    const fresh = fetch(req).then((res) => {
                        if (res.ok) cache.put(req, res.clone());
                        return res;
                    }).catch(() => cached);
                    return cached || fresh;
                })
            )
        );
        return;
    }

    // HTML navigation — network-first, fall back to /offline
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req).catch(() =>
                caches.match('/offline').then((r) => r || new Response('Offline', { status: 503 }))
            )
        );
        return;
    }
});