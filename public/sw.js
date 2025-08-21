// Service Worker untuk Push Notification
self.addEventListener('push', function (event) {
  console.log('Push event received:', event);

  let notificationData = {
    title: 'SI TEDUH',
    body: 'Anda memiliki notifikasi baru',
    icon: '/images/logo-puskesmas.png',
    badge: '/images/logo-puskesmas.png',
    data: {
      url: '/user'
    }
  };

  if (event.data) {
    try {
      const data = event.data.json();
      console.log('Parsed push data:', data);

      notificationData = {
        title: data.title || notificationData.title,
        body: data.body || notificationData.body,
        icon: data.icon || notificationData.icon,
        badge: data.badge || notificationData.badge,
        data: data.data || notificationData.data,
        requireInteraction: true,
        actions: [
          {
            action: 'view',
            title: 'Lihat Detail'
          },
          {
            action: 'dismiss',
            title: 'Tutup'
          }
        ]
      };
    } catch (error) {
      console.error('Error parsing push data:', error);
    }
  }

  console.log('Final notification data:', notificationData);

  // Validasi data sebelum menampilkan notifikasi
  if (!notificationData.title || !notificationData.body) {
    console.error('Invalid notification data: missing title or body');
    return;
  }

  try {
    // Gunakan event.waitUntil untuk memastikan notifikasi ditampilkan
    event.waitUntil(
      self.registration.showNotification(notificationData.title, {
        body: notificationData.body,
        icon: notificationData.icon,
        badge: notificationData.badge,
        data: notificationData.data,
        requireInteraction: true,
        actions: notificationData.actions,
        tag: 'si-teduh-notification', // Prevent duplicate notifications
        renotify: true
      }).then(() => {
        console.log('Notification displayed successfully');
      }).catch((error) => {
        console.error('Error displaying notification:', error);

        // Fallback: coba buat notifikasi sederhana
        return self.registration.showNotification(notificationData.title, {
          body: notificationData.body,
          icon: notificationData.icon
        });
      })
    );
  } catch (error) {
    console.error('Error in waitUntil:', error);
  }
});

// Listener untuk message dari main thread (untuk testing)
self.addEventListener('message', function (event) {
  console.log('Message received in service worker:', event.data);

  if (event.data && event.data.type === 'TEST_PUSH') {
    console.log('Test push notification received');

    const testData = event.data.data;

    // Tampilkan notifikasi test
    self.registration.showNotification(testData.title, {
      body: testData.body,
      icon: testData.icon,
      badge: testData.icon,
      data: testData.data,
      requireInteraction: true,
      tag: 'si-teduh-test',
      renotify: true
    }).then(() => {
      console.log('Test notification displayed successfully');
    }).catch((error) => {
      console.error('Error displaying test notification:', error);
    });
  }

  // Handle SKIP_WAITING untuk force activate service worker
  if (event.data && event.data.type === 'SKIP_WAITING') {
    console.log('Skip waiting message received');
    self.skipWaiting();
  }
});

self.addEventListener('notificationclick', function (event) {
  console.log('Notification clicked:', event);

  event.notification.close();

  if (event.action === 'view' || !event.action) {
    event.waitUntil(
      clients.openWindow(event.notification.data.url || '/user')
    );
  }
});

self.addEventListener('notificationclose', function (event) {
  console.log('Notification closed:', event);
});

// Handle service worker update
self.addEventListener('install', function (event) {
  console.log('Service Worker installed');
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  console.log('Service Worker activated');
  event.waitUntil(self.clients.claim());
}); 