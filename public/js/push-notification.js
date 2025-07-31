// Web Push Notification Handler
class PushNotificationHandler {
  constructor() {
    this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
    this.swRegistration = null;
    this.applicationServerKey = this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY'); // Ganti dengan VAPID key Anda
  }

  async init() {
    if (!this.isSupported) {
      console.log('Push notification tidak didukung di browser ini');
      return false;
    }

    try {
      // Register service worker
      this.swRegistration = await navigator.serviceWorker.register('/sw.js');
      console.log('Service Worker registered');

      // Request notification permission
      const permission = await this.requestNotificationPermission();
      if (permission === 'granted') {
        await this.subscribeUser();
        this.setupEchoListener();
      }

      return true;
    } catch (error) {
      console.error('Error initializing push notification:', error);
      return false;
    }
  }

  async requestNotificationPermission() {
    const permission = await Notification.requestPermission();
    return permission;
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
      const options = {
        body: notification.body,
        icon: notification.icon || '/images/logo-puskesmas.png',
        badge: notification.badge || '/images/logo-puskesmas.png',
        data: notification.data,
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

      const pushNotification = new Notification(notification.title, options);

      pushNotification.onclick = function (event) {
        event.preventDefault();
        if (notification.data && notification.data.url) {
          window.open(notification.data.url, '_blank');
        }
        pushNotification.close();
      };

      pushNotification.onaction = function (event) {
        if (event.action === 'view' && notification.data && notification.data.url) {
          window.open(notification.data.url, '_blank');
        }
        pushNotification.close();
      };
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

// Initialize push notification when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
  const pushHandler = new PushNotificationHandler();
  pushHandler.init();
}); 