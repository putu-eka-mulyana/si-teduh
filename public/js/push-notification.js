// Web Push Notification Handler
class PushNotificationHandler {
  constructor() {
    this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
    this.swRegistration = null;
    this.applicationServerKey = this.urlBase64ToUint8Array('BIChEW_RHFX8MDiVsQrSFOBE_yOGFxRkrjlBX_pquRBXWMmH1pL2q94Srp_vxRdF9HEWx1yYPetLsEovODx9oIQ');
    this.isInitialized = false;
  }

  async init() {
    console.log('Inisialisasi push notification...');
    if (!this.isSupported) {
      console.log('Push notification tidak didukung di browser ini');
      return false;
    }

    if (this.isInitialized) {
      console.log('Push notification sudah diinisialisasi');
      return true;
    }

    try {
      // Register service worker
      this.swRegistration = await navigator.serviceWorker.register('/sw.js');
      console.log('Service Worker registered');

      // Check current permission status
      const permission = Notification.permission;

      if (permission === 'granted') {
        await this.subscribeUser();
        this.setupEchoListener();
        this.isInitialized = true;
        console.log('Push notification initialized successfully');
        return true;
      } else if (permission === 'denied') {
        console.log('Notification permission denied');
        return false;
      } else {
        // Permission is 'default' - show modal
        console.log('Requesting notification permission via modal');
        return false;
      }
    } catch (error) {
      console.error('Error initializing push notification:', error);
      return false;
    }
  }

  async requestPermissionAndSubscribe() {
    try {
      const permission = await Notification.requestPermission();
      console.log('Permission result:', permission);

      if (permission === 'granted') {
        await this.subscribeUser();
        this.setupEchoListener();
        this.isInitialized = true;
        console.log('User subscribed to push notifications');
        return true;
      } else {
        console.log('Permission denied by user');
        return false;
      }
    } catch (error) {
      console.error('Error requesting permission:', error);
      return false;
    }
  }

  async subscribeUser() {
    try {
      const subscription = await this.swRegistration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: this.applicationServerKey
      });

      // Send subscription to server
      await this.sendSubscriptionToServer(subscription);
      console.log('User subscribed to push notifications');
    } catch (error) {
      console.error('Error subscribing user:', error);
    }
  }

  async sendSubscriptionToServer(subscription) {
    try {
      const response = await fetch('/api/push-subscription', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(subscription)
      });

      if (!response.ok) {
        throw new Error('Failed to save subscription');
      }
    } catch (error) {
      console.error('Error saving subscription:', error);
    }
  }

  setupEchoListener() {
    // Listen for Echo events (Laravel WebSockets)
    if (typeof Echo !== 'undefined') {
      Echo.private(`App.Models.User.${window.userId}`)
        .notification((notification) => {
          this.showNotification(notification);
        });
    }
  }

  showNotification(notification) {
    if (Notification.permission === 'granted') {
      console.log('Showing notification:', notification);

      const options = {
        body: notification.body || 'Anda memiliki notifikasi baru',
        icon: notification.icon || '/images/logo-puskesmas.png',
        badge: notification.badge || '/images/logo-puskesmas.png',
        data: notification.data || { url: '/user' },
        requireInteraction: true,
        tag: 'si-teduh-notification',
        renotify: true,
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

      console.log('Notification options:', options);

      try {
        const pushNotification = new Notification(notification.title || 'SI TEDUH', options);
        console.log('Notification created successfully');

        pushNotification.onclick = function (event) {
          event.preventDefault();
          console.log('Notification clicked');
          if (notification.data && notification.data.url) {
            window.open(notification.data.url, '_blank');
          }
          pushNotification.close();
        };

        pushNotification.onaction = function (event) {
          console.log('Notification action clicked:', event.action);
          if (event.action === 'view' && notification.data && notification.data.url) {
            window.open(notification.data.url, '_blank');
          }
          pushNotification.close();
        };

        pushNotification.onshow = function () {
          console.log('Notification shown');
        };

        pushNotification.onerror = function (error) {
          console.error('Notification error:', error);
        };

      } catch (error) {
        console.error('Error creating notification:', error);
        // Fallback: coba buat notifikasi sederhana
        try {
          new Notification(notification.title || 'SI TEDUH', {
            body: notification.body || 'Anda memiliki notifikasi baru',
            icon: '/images/logo-puskesmas.png'
          });
        } catch (fallbackError) {
          console.error('Fallback notification also failed:', fallbackError);
        }
      }
    } else {
      console.log('Notification permission not granted:', Notification.permission);
    }
  }

  // Method untuk testing notifikasi
  testNotification() {
    console.log('Testing notification...');
    const testData = {
      title: 'Test Notifikasi SI TEDUH',
      body: 'Ini adalah notifikasi test untuk memastikan sistem berfungsi dengan baik.',
      icon: '/images/logo-puskesmas.png',
      data: {
        url: '/user'
      }
    };

    this.showNotification(testData);
  }

  // Method untuk mengirim notifikasi test ke service worker
  async sendTestPushNotification() {
    if (!this.swRegistration) {
      console.error('Service Worker not registered');
      return false;
    }

    try {
      // Simulasi push notification
      const testData = {
        title: 'Test Push Notification',
        body: 'Ini adalah test push notification dari service worker',
        icon: '/images/logo-puskesmas.png',
        data: {
          url: '/user'
        }
      };

      console.log('Sending test data to service worker:', testData);

      // Kirim message ke service worker
      if (this.swRegistration.active) {
        this.swRegistration.active.postMessage({
          type: 'TEST_PUSH',
          data: testData
        });
        console.log('Test message sent to service worker');
      } else {
        console.error('Service worker not active');
        return false;
      }

      return true;
    } catch (error) {
      console.error('Error sending test push notification:', error);
      return false;
    }
  }

  // Method untuk force refresh service worker
  async forceRefreshServiceWorker() {
    try {
      if (this.swRegistration) {
        await this.swRegistration.update();
        console.log('Service worker updated');

        // Wait for new service worker to activate
        await new Promise((resolve) => {
          if (this.swRegistration.waiting) {
            this.swRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });
          }
          resolve();
        });

        return true;
      }
      return false;
    } catch (error) {
      console.error('Error refreshing service worker:', error);
      return false;
    }
  }

  urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
      .replace(/-/g, '+')
      .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }
}

// Initialize push notification handler when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
  window.pushHandler = new PushNotificationHandler();

  // Try to initialize if permission already granted
  if (Notification.permission === 'granted') {
    window.pushHandler.init();
  }
}); 
