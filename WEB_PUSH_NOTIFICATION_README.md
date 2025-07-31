# Web Push Notification SI TEDUH

## Deskripsi
Fitur web push notification memungkinkan aplikasi SI TEDUH untuk mengirim notifikasi langsung ke browser pengguna, bahkan ketika aplikasi tidak sedang dibuka. Notifikasi ini menggunakan Web Push API dan Service Worker.

## Komponen yang Sudah Diimplementasikan

### 1. Service Worker
- **File**: `public/sw.js`
- **Fungsi**: Menangani push event dan menampilkan notifikasi di browser
- **Fitur**: 
  - Menampilkan notifikasi dengan icon dan badge
  - Action button untuk "Lihat Detail" dan "Tutup"
  - Click handler untuk membuka URL notifikasi

### 2. JavaScript Handler
- **File**: `public/js/push-notification.js`
- **Fungsi**: Menginisialisasi dan mengelola push notification di sisi client
- **Fitur**:
  - Registrasi Service Worker
  - Request permission untuk notifikasi
  - Subscribe ke push notification
  - Kirim subscription ke server
  - Setup Echo listener untuk real-time notification

### 3. Controller
- **File**: `app/Http/Controllers/PushSubscriptionController.php`
- **Fungsi**: Mengelola subscription push notification
- **Method**:
  - `store()`: Menyimpan subscription baru
  - `destroy()`: Menghapus subscription

### 4. Model
- **File**: `app/Models/PushSubscription.php`
- **Fungsi**: Model untuk menyimpan data subscription
- **Field**: user_id, endpoint, p256dh, auth

### 5. Database Migration
- **File**: `database/migrations/2025_07_29_031602_create_push_subscriptions_table.php`
- **Fungsi**: Membuat tabel untuk menyimpan push subscription

## Cara Kerja

### 1. Inisialisasi
- Saat halaman dimuat, JavaScript akan mengecek dukungan browser
- Registrasi Service Worker
- Request permission untuk notifikasi
- Subscribe ke push notification dan kirim ke server

### 2. Penyimpanan Subscription
- Data subscription (endpoint, p256dh, auth) disimpan di database
- Terhubung dengan user yang sedang login
- Mendukung multiple subscription per user

### 3. Pengiriman Notifikasi
- Server dapat mengirim notifikasi ke semua endpoint user
- Service Worker menerima push event
- Notifikasi ditampilkan dengan format yang sudah ditentukan

## Setup dan Konfigurasi

### 1. VAPID Keys
Ganti `YOUR_VAPID_PUBLIC_KEY` di `public/js/push-notification.js` dengan VAPID public key Anda.

### 2. Generate VAPID Keys
```bash
# Install web-push library
composer require minishlink/web-push

# Generate VAPID keys
php artisan webpush:vapid
```

### 3. Environment Variables
Tambahkan ke file `.env`:
```env
VAPID_PUBLIC_KEY=your_vapid_public_key
VAPID_PRIVATE_KEY=your_vapid_private_key
VAPID_SUBJECT=mailto:your-email@example.com
```

### 4. Include JavaScript
Pastikan file `push-notification.js` di-include di layout:
```html
<script src="{{ asset('js/push-notification.js') }}"></script>
```

## API Endpoints

### POST /api/push-subscription
Menyimpan push subscription baru
```json
{
  "endpoint": "https://fcm.googleapis.com/fcm/send/...",
  "keys": {
    "p256dh": "BNcRd...",
    "auth": "tBHI..."
  }
}
```

### DELETE /api/push-subscription
Menghapus push subscription
```json
{
  "endpoint": "https://fcm.googleapis.com/fcm/send/..."
}
```

## Penggunaan

### 1. Kirim Notifikasi
```php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$subscriptions = PushSubscription::where('user_id', $userId)->get();

foreach ($subscriptions as $subscription) {
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => env('VAPID_SUBJECT'),
            'publicKey' => env('VAPID_PUBLIC_KEY'),
            'privateKey' => env('VAPID_PRIVATE_KEY'),
        ],
    ]);

    $report = $webPush->sendOneNotification(
        Subscription::create([
            'endpoint' => $subscription->endpoint,
            'keys' => [
                'p256dh' => $subscription->p256dh,
                'auth' => $subscription->auth,
            ],
        ]),
        json_encode([
            'title' => 'SI TEDUH',
            'body' => 'Anda memiliki jadwal baru',
            'icon' => '/images/logo-puskesmas.png',
            'data' => [
                'url' => '/user'
            ]
        ])
    );
}
```

### 2. Integrasi dengan Schedule
Tambahkan di `ScheduleController` saat membuat jadwal baru:
```php
// Setelah membuat schedule
$user = User::find($request->input('patient_id'));
if ($user && $user->pushSubscriptions()->count() > 0) {
    // Kirim push notification
    $this->sendPushNotification($user, 'Jadwal Baru', 'Anda memiliki jadwal baru');
}
```

## Keamanan

- ✅ Validasi user authentication
- ✅ CSRF protection untuk API endpoints
- ✅ Validasi data subscription
- ✅ Relasi yang aman antara User dan PushSubscription

## Browser Support

- ✅ Chrome 42+
- ✅ Firefox 44+
- ✅ Safari 16+
- ✅ Edge 17+

## Troubleshooting

### 1. Notifikasi Tidak Muncul
- Periksa permission browser
- Pastikan Service Worker ter-registrasi
- Cek console browser untuk error

### 2. Subscription Gagal
- Periksa VAPID keys
- Pastikan endpoint valid
- Cek koneksi internet

### 3. Service Worker Error
- Periksa path file sw.js
- Pastikan HTTPS (required untuk production)
- Cek browser console

## Status: **READY** ✅

Web push notification sudah siap digunakan dan terintegrasi dengan sistem SI TEDUH! 