// sw.js - سرویس ورکر ساده برای کش کردن صفحات اصلی
const CACHE_NAME = 'ketabkhan-cache-v1';
const urlsToCache = [
  '/',
  '/assets/css/all.css',
  '/build/assets/style.css',
  '/build/assets/script.js',
  '/assets/img/icon.png'
];

// نصب سرویس ورکر و کش کردن فایل‌ها
// self.addEventListener('install', event => {
//   event.waitUntil(
//     caches.open(CACHE_NAME)
//       .then(cache => cache.addAll(urlsToCache))
//   );
// });

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(urlsToCache);
        })
    );
});

// استراتژی Cache First سپس Network
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});

// حذف کش‌های قدیمی هنگام فعال شدن
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) return caches.delete(cache);
        })
      );
    })
  );
});