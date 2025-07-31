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

  event.waitUntil(
    self.registration.showNotification(notificationData.title, notificationData)
  );
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

self.addEventListener('install', function (event) {
  console.log('Service Worker installed');
  self.skipWaiting();
});

self.addEventListener('activate', function (event) {
  console.log('Service Worker activated');
  event.waitUntil(self.clients.claim());
}); 