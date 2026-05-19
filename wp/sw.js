// Studio pSEO — Service Worker v1.0
const CACHE_NAME = 'studio-pseo-v1';
const OFFLINE_URL = '/wp/studio-admin.php';

// Assets to pre-cache for offline shell
const PRECACHE_ASSETS = [
  '/wp/studio-admin.php',
  '/wp/pwa-install.php',
  'https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&family=Syne:wght@400;600;700&display=swap',
];

// ─── INSTALL ──────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      // Pre-cache fonts and the app shell
      return cache.addAll(PRECACHE_ASSETS).catch(() => {
        // Silently fail — network-first strategy handles the rest
      });
    })
  );
  self.skipWaiting();
});

// ─── ACTIVATE ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME)
          .map((name) => caches.delete(name))
      )
    )
  );
  self.clients.claim();
});

// ─── FETCH — Network-first, cache fallback ────────────────────────────────────
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET and cross-origin API calls (Gemini, etc.)
  if (request.method !== 'GET') return;
  if (url.hostname.includes('generativelanguage.googleapis.com')) return;
  if (url.hostname.includes('googleapis.com') && url.pathname.includes('/v1beta')) return;

  // For navigation requests (page loads) — network first, offline fallback
  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => {
          // Cache fresh page on the fly
          if (response && response.status === 200) {
            const clone = response.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
          }
          return response;
        })
        .catch(() => caches.match(OFFLINE_URL))
    );
    return;
  }

  // For static assets (fonts, CDN scripts) — stale-while-revalidate
  if (
    url.hostname.includes('fonts.googleapis.com') ||
    url.hostname.includes('fonts.gstatic.com') ||
    url.hostname.includes('cdn.tailwindcss.com') ||
    url.hostname.includes('unpkg.com')
  ) {
    event.respondWith(
      caches.open(CACHE_NAME).then(async (cache) => {
        const cached = await cache.match(request);
        const fetchPromise = fetch(request).then((response) => {
          if (response && response.status === 200) {
            cache.put(request, response.clone());
          }
          return response;
        });
        return cached || fetchPromise;
      })
    );
    return;
  }

  // All other requests — network first
  event.respondWith(
    fetch(request).catch(() => caches.match(request))
  );
});

// ─── PUSH NOTIFICATIONS (future use) ─────────────────────────────────────────
self.addEventListener('push', (event) => {
  const data = event.data?.json() ?? {};
  event.waitUntil(
    self.registration.showNotification(data.title || 'Studio pSEO', {
      body: data.body || 'New update available.',
      icon: 'https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg',
      badge: 'https://getonlinestudio.com/insights/wp-content/uploads/2026/02/GetOnline_Studio_Logo.jpg',
      data: { url: data.url || '/wp/studio-admin.php' },
    })
  );
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(clients.openWindow(event.notification.data?.url || '/wp/studio-admin.php'));
});
