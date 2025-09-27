# 🧪 Panduan Lengkap Testing Web Push Notification SI TEDUH

## 📋 Prasyarat Testing

### 1. Environment Variables
Pastikan file `.env` memiliki konfigurasi berikut:

```env
# Web Push Notification Configuration
VAPID_PUBLIC_KEY=BBhjM_UXEHFayvfzrJ3RSV59sgBrAsZ6ETiwj7pE4xCrOuhx0_OV7RR0W9PxMEX6VO3qFZ36kmVoqdkBTJZiPUI
VAPID_PRIVATE_KEY=your_vapid_private_key_here
VAPID_SUBJECT=mailto:admin@siteduh.com

# Queue Configuration untuk Push Notification
QUEUE_CONNECTION=database

# Broadcasting untuk real-time notification
BROADCAST_DRIVER=log
```

### 2. Generate VAPID Keys (jika belum ada)
```bash
# Install web-push library
composer require minishlink/web-push

# Generate VAPID keys
npm install -g web-push
web-push generate-vapid-keys
```

### 3. Setup Database
```bash
# Jalankan migration
php artisan migrate

# Setup queue table
php artisan queue:table
php artisan migrate
```

### 4. Start Services
```bash
# Start Laravel server
php artisan serve

# Start queue worker (di terminal terpisah)
php artisan queue:work

# Atau gunakan script yang sudah ada
./start-queue-worker.sh
```

---

## 🌐 Testing di Browser Console

### 1. Test Dasar - Cek Status Sistem

Buka browser console (F12) dan jalankan perintah berikut:

```javascript
// 1. Cek dukungan browser
console.log('Service Worker Support:', 'serviceWorker' in navigator);
console.log('Push Manager Support:', 'PushManager' in window);
console.log('Notification Support:', 'Notification' in window);

// 2. Cek permission
console.log('Notification Permission:', Notification.permission);

// 3. Cek service worker registrations
navigator.serviceWorker.getRegistrations().then(registrations => {
    console.log('Service Workers:', registrations.length);
    registrations.forEach((reg, index) => {
        console.log(`SW ${index + 1}:`, reg.scope);
    });
});
```

### 2. Test Service Worker Registration

```javascript
// Cek apakah push handler sudah terinisialisasi
if (window.pushHandler) {
    console.log('Push Handler Status:', {
        isSupported: window.pushHandler.isSupported,
        isInitialized: window.pushHandler.isInitialized,
        swRegistration: window.pushHandler.swRegistration ? 'Registered' : 'Not Registered'
    });
} else {
    console.log('Push Handler: Not Available');
}
```

### 3. Test Permission dan Subscription

```javascript
// Request permission dan subscribe
if (window.pushHandler && !window.pushHandler.isInitialized) {
    window.pushHandler.requestPermissionAndSubscribe()
        .then(success => {
            console.log('Subscription Result:', success);
        })
        .catch(error => {
            console.error('Subscription Error:', error);
        });
}
```

### 4. Test Notifikasi Langsung

```javascript
// Test notifikasi sederhana
if (Notification.permission === 'granted') {
    const testNotif = new Notification('Test SI TEDUH', {
        body: 'Ini adalah test notifikasi langsung',
        icon: '/images/logo-puskesmas.png',
        badge: '/images/logo-puskesmas.png'
    });
    
    testNotif.onshow = () => console.log('✅ Notification shown');
    testNotif.onerror = (e) => console.log('❌ Notification error:', e);
    testNotif.onclick = () => {
        console.log('🖱️ Notification clicked');
        testNotif.close();
    };
} else {
    console.log('❌ Permission not granted');
}
```

### 5. Test Push Handler Methods

```javascript
// Test menggunakan push handler
if (window.pushHandler) {
    // Test notifikasi menggunakan handler
    window.pushHandler.testNotification();
    
    // Test push notification ke service worker
    setTimeout(() => {
        window.pushHandler.sendTestPushNotification();
    }, 2000);
}
```

### 6. Debug Lengkap (jika tersedia)

```javascript
// Jika ada fungsi debugNotification
if (typeof debugNotification === 'function') {
    debugNotification();
}
```

---

## 🖥️ Testing dengan Endpoint API

### 1. Test Push Subscription Endpoint

```javascript
// Test menyimpan subscription
fetch('/api/push-subscription', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        endpoint: 'https://fcm.googleapis.com/fcm/send/test-endpoint',
        keys: {
            p256dh: 'test-p256dh-key',
            auth: 'test-auth-key'
        }
    })
})
.then(response => response.json())
.then(data => console.log('Subscription saved:', data))
.catch(error => console.error('Error:', error));
```

### 2. Test Delete Subscription

```javascript
// Test menghapus subscription
fetch('/api/push-subscription', {
    method: 'DELETE',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        endpoint: 'https://fcm.googleapis.com/fcm/send/test-endpoint'
    })
})
.then(response => response.json())
.then(data => console.log('Subscription deleted:', data))
.catch(error => console.error('Error:', error));
```

---

## 🎯 Testing Manual dengan UI

### 1. Test dari Halaman Admin

1. **Login sebagai admin**
2. **Buat jadwal baru**:
   - Masuk ke halaman "Tambah Jadwal"
   - Isi form dengan data pasien
   - Set waktu jadwal
   - Klik "Simpan"

3. **Cek hasil**:
   - Lihat console browser untuk log
   - Cek apakah notifikasi muncul
   - Cek database untuk subscription

### 2. Test dari Halaman User

1. **Login sebagai user/pasien**
2. **Allow notification permission** saat diminta
3. **Cek subscription** di database:
   ```sql
   SELECT * FROM push_subscriptions WHERE user_id = [USER_ID];
   ```
4. **Tunggu jadwal dibuat** oleh admin
5. **Lihat notifikasi** yang muncul

---

## 🔧 Testing dengan Command Line

### 1. Test Queue Job

```bash
# Cek queue jobs
php artisan queue:work --once

# Cek failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### 2. Test Database

```bash
# Cek tabel push_subscriptions
php artisan tinker
>>> App\Models\PushSubscription::all();

# Cek subscriptions user tertentu
>>> $user = App\Models\User::find(1);
>>> $user->pushSubscriptions;
```

---

## 🚨 Troubleshooting

### 1. Notifikasi Tidak Muncul

**Cek di Console Browser:**
```javascript
// 1. Cek permission
console.log('Permission:', Notification.permission);

// 2. Cek service worker
navigator.serviceWorker.getRegistrations().then(regs => {
    console.log('SW Count:', regs.length);
});

// 3. Cek subscription
navigator.serviceWorker.ready.then(reg => {
    reg.pushManager.getSubscription().then(sub => {
        console.log('Subscription:', sub ? 'Exists' : 'None');
    });
});
```

**Solusi:**
- Clear browser cache
- Unregister service worker lama
- Refresh halaman
- Request permission ulang

### 2. Service Worker Error

**Cek di Application Tab (Chrome DevTools):**
- Service Workers → Cek status
- Storage → Clear storage
- Console → Cek error messages

**Solusi:**
- Pastikan file `sw.js` accessible
- Cek path file di browser
- Pastikan HTTPS (untuk production)

### 3. Subscription Gagal

**Cek Network Tab:**
- Lihat request ke `/api/push-subscription`
- Cek response status
- Cek CSRF token

**Solusi:**
- Cek VAPID keys
- Pastikan endpoint valid
- Cek database connection

### 4. Queue Job Gagal

```bash
# Cek failed jobs
php artisan queue:failed

# Lihat detail error
php artisan queue:failed [job_id]

# Retry job
php artisan queue:retry [job_id]
```

---

## ✅ Checklist Testing

### Pre-Testing Setup
- [ ] VAPID keys sudah dikonfigurasi
- [ ] Database migration sudah dijalankan
- [ ] Queue worker sudah running
- [ ] Service worker file accessible
- [ ] Browser support push notification

### Browser Testing
- [ ] Permission granted
- [ ] Service worker registered
- [ ] Subscription created
- [ ] Notifikasi test berhasil
- [ ] Click action berfungsi

### Server Testing
- [ ] API endpoint berfungsi
- [ ] Database menyimpan subscription
- [ ] Queue job berjalan
- [ ] Notification terkirim

### Integration Testing
- [ ] Admin buat jadwal → User terima notifikasi
- [ ] Multiple device support
- [ ] Error handling
- [ ] Performance test

---

## 📊 Monitoring dan Logging

### 1. Browser Console Logs
```javascript
// Enable verbose logging
localStorage.setItem('debug', 'push-notification');
```

### 2. Server Logs
```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log

# Monitor queue logs
php artisan queue:work --verbose
```

### 3. Database Monitoring
```sql
-- Cek subscription count
SELECT COUNT(*) as total_subscriptions FROM push_subscriptions;

-- Cek subscriptions per user
SELECT user_id, COUNT(*) as subscription_count 
FROM push_subscriptions 
GROUP BY user_id;
```

---

## 🎯 Test Scenarios

### Scenario 1: First Time User
1. User login pertama kali
2. Allow notification permission
3. Subscription tersimpan
4. Admin buat jadwal
5. User terima notifikasi

### Scenario 2: Multiple Devices
1. User login di 2 device berbeda
2. Kedua device allow permission
3. Kedua device ter-subscribe
4. Admin buat jadwal
5. Kedua device terima notifikasi

### Scenario 3: Permission Denied
1. User deny permission
2. Subscription tidak tersimpan
3. Admin buat jadwal
4. User tidak terima notifikasi

### Scenario 4: Network Issues
1. User subscribe saat online
2. Network putus
3. Admin buat jadwal
4. Notifikasi pending sampai online

---

## 📝 Hasil Testing yang Diharapkan

### ✅ Success Indicators
- Permission granted di browser
- Service worker registered
- Subscription tersimpan di database
- Notifikasi muncul saat jadwal dibuat
- Click notifikasi membuka aplikasi
- Queue job berjalan tanpa error

### ❌ Failure Indicators
- Permission denied
- Service worker error
- Subscription gagal tersimpan
- Notifikasi tidak muncul
- Queue job failed
- Database error

---

## 🔄 Iterasi Testing

1. **Fix issues** yang ditemukan
2. **Retest** semua scenarios
3. **Document** hasil testing
4. **Update** konfigurasi jika perlu
5. **Deploy** ke production setelah semua test passed

---

**Status: READY FOR TESTING** ✅

Web push notification SI TEDUH siap untuk ditest dengan panduan lengkap di atas!
