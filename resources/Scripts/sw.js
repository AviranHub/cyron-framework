self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('my-cache').then((cache) => {
            return cache.addAll([
                '/',
                '/index.html',
                '/style.css',
                '/script.js',
                '/icon.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});



if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then((registration) => {
            console.log('Service Worker registered with scope:', registration.scope);
        }).catch((error) => {
            console.error('Service Worker registration failed:', error);
        });
    });
}






let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault(); // جلوگیری از نمایش پیش‌فرض
    deferredPrompt = e; // ذخیره رویداد
    // نمایش دکمه نصب
    const installButton = document.getElementById('installButton');
    installButton.style.display = 'block';

    installButton.addEventListener('click', () => {
        installButton.style.display = 'none'; // پنهان کردن دکمه
        deferredPrompt.prompt(); // نمایش پنجره نصب
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User  accepted the A2HS prompt');
            } else {
                console.log('User  dismissed the A2HS prompt');
            }
            deferredPrompt = null; // پاک کردن رویداد بعد از انتخاب کاربر
        });
    });
});


// <!DOCTYPE html>
// <html lang="fa">
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <link rel="stylesheet" href="style.css">
//     <link rel="manifest" href="manifest.json">
//     <title>وب اپلیکیشن من</title>
// </head>
// <body>
//     <h1>سلام، وب اپلیکیشن من!</h1>
//     <button id="installButton" style="display: none;">نصب وب اپلیکیشن</button>

//     <script>
//         // ثبت Service Worker
//         if ('serviceWorker' in navigator) {
//             window.addEventListener('load', () => {
//                 navigator.serviceWorker.register('/sw.js').then((registration) => {
//                     console.log('Service Worker registered with scope:', registration.scope);
//                 }).catch((error) => {
//                     console.error('Service Worker registration failed:', error);
//                 });
//             });
//         }

//         // مدیریت اعلان نصب
//         let deferredPrompt;

//         window.addEventListener('beforeinstallprompt', (e) => {
//             e.preventDefault(); // جلوگیری از نمایش پیش‌فرض
//             deferredPrompt = e; // ذخیره رویداد
//             const installButton = document.getElementById('installButton');
//             installButton.style.display = 'block'; // نمایش دکمه نصب

//             installButton.addEventListener('click', () => {
//                 installButton.style.display = 'none'; // پنهان کردن دکمه
//                 deferredPrompt.prompt(); // نمایش پنجره نصب
//                 deferredPrompt.userChoice.then((choiceResult) => {
//                     if (choiceResult.outcome === 'accepted') {
//                         console.log('User  accepted the A2HS prompt');
//                     } else {
//                         console.log('User  dismissed the A2HS prompt');
//                     }
//                     deferredPrompt = null; // پاک کردن رویداد بعد از انتخاب کاربر
//                 });
//             });
//         });
//     </script>
// </body>
// </html>