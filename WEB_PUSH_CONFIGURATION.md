# Konfigurasi Web Push Notification SI TEDUH

## Environment Variables yang Diperlukan

Tambahkan konfigurasi berikut ke file `.env`:

### 1. VAPID Keys Configuration
```env
VAPID_PUBLIC_KEY=your_vapid_public_key_here
VAPID_PRIVATE_KEY=your_vapid_private_key_here
VAPID_SUBJECT=mailto:your-email@example.com
```

### 2. Queue Configuration (Opsional)
```env
QUEUE_CONNECTION=database
```

## Setup VAPID Keys

### 1. Install Web Push Library
```bash
composer require minishlink/web-push
```

### 2. Generate VAPID Keys
```bash
# Menggunakan web-push CLI
npm install -g web-push
web-push generate-vapid-keys

# Atau menggunakan PHP
php artisan webpush:vapid
```

### 3. Update JavaScript
Ganti `YOUR_VAPID_PUBLIC_KEY` di `public/js/push-notification.js` dengan VAPID public key yang di-generate.

## Setup Database

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Pastikan Tabel Push Subscriptions Ada
Tabel `push_subscriptions` akan dibuat otomatis oleh migration.

## Setup Development

### 1. Jalankan Service
```bash
./start-webpush-service.sh
```

### 2. Atau Manual Setup
```bash
# Install dependencies
composer install
npm install

# Build assets
npm run build

# Jalankan server
php artisan serve
```

## Testing Configuration

### 1. Test Service Worker
```javascript
// Di browser console
navigator.serviceWorker.getRegistrations().then(registrations => {
    console.log('Service Workers:', registrations);
});
```

### 2. Test Permission
```javascript
// Di browser console
console.log('Notification Permission:', Notification.permission);
```

### 3. Test Subscription
```javascript
// Di browser console
navigator.serviceWorker.ready.then(registration => {
    registration.pushManager.getSubscription().then(subscription => {
        console.log('Subscription:', subscription);
    });
});
```

## Troubleshooting

### 1. Notifikasi Tidak Muncul
- Periksa permission browser
- Pastikan Service Worker ter-registrasi
- Cek console browser untuk error
- Pastikan VAPID keys sudah benar

### 2. Service Worker Error
- Clear browser cache dan cookies
- Unregister service worker lama
- Refresh halaman
- Pastikan HTTPS connection (untuk production)

### 3. Subscription Gagal
- Periksa network connection
- Pastikan endpoint valid
- Cek database connection
- Periksa CSRF token

## Monitoring

### 1. Cek Subscription di Database
```sql
SELECT * FROM push_subscriptions WHERE user_id = 1;
```

### 2. Cek Service Worker Status
```javascript
// Di browser console
navigator.serviceWorker.getRegistrations().then(registrations => {
    console.log('Service Workers:', registrations);
});
```

### 3. Cek Notification Permission
```javascript
// Di browser console
console.log('Notification Permission:', Notification.permission);
```

## Production Setup

### 1. HTTPS Required
Service worker hanya bekerja di HTTPS. Pastikan domain menggunakan SSL certificate.

### 2. Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
VAPID_PUBLIC_KEY=your_production_vapid_public_key
VAPID_PRIVATE_KEY=your_production_vapid_private_key
VAPID_SUBJECT=mailto:your-production-email@example.com
```

### 3. Queue Worker
```bash
# Jalankan queue worker di production
php artisan queue:work --daemon
```

## Status: **READY** ✅

Web push notification sudah siap digunakan dengan konfigurasi yang lengkap! 