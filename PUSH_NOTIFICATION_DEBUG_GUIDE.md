# 🔍 Panduan Debug Push Notification dari Cron Job

## 📍 Lokasi File Log

Debug log untuk push notification disimpan di:

```
storage/logs/laravel.log
```

**Path lengkap di server:**
```
/Volumes/MacSpace/LEARN/backend/laravel/si-teduh/storage/logs/laravel.log
```

## 🔎 Cara Melihat Log Debug

### 1. **Melihat Log Real-time (Recommended)**

Jalankan command ini untuk melihat log secara real-time:

```bash
tail -f storage/logs/laravel.log
```

Atau dengan filter khusus untuk push notification:

```bash
tail -f storage/logs/laravel.log | grep "PUSH NOTIFICATION DEBUG"
```

### 2. **Melihat Log Terakhir (Last 100 Lines)**

```bash
tail -n 100 storage/logs/laravel.log
```

### 3. **Melihat Log dengan Filter Spesifik**

#### Filter berdasarkan Schedule ID:
```bash
grep "schedule_id.*: 1" storage/logs/laravel.log | tail -n 50
```

#### Filter berdasarkan Step tertentu:
```bash
grep "step.*: JOB_HANDLE_START" storage/logs/laravel.log
```

#### Filter hanya ERROR:
```bash
grep "PUSH NOTIFICATION DEBUG.*ERROR" storage/logs/laravel.log | tail -n 50
```

### 4. **Melihat Semua Log Push Notification untuk Schedule Tertentu**

```bash
grep -A 5 -B 5 "PUSH NOTIFICATION DEBUG.*schedule_id.*: 1" storage/logs/laravel.log
```

## 📊 Format Debug Log

Setiap log entry dimulai dengan `=== PUSH NOTIFICATION DEBUG: [STEP] ===` untuk memudahkan filtering.

### Alur Debug dari Awal hingga Akhir:

1. **SCHEDULE_CREATE_START** - Schedule creation dimulai
2. **VALIDATION_PASSED** - Validasi request berhasil
3. **SCHEDULE_CREATED** - Schedule berhasil dibuat di database
4. **JOB_DISPATCH_START** - Mulai dispatch job ke queue
5. **JOB_DISPATCHED** - Job berhasil di-dispatch
6. **JOB_CONSTRUCT** - Job instance dibuat
7. **JOB_HANDLE_START** - Job mulai diproses
8. **LOAD_RELATIONSHIPS** - Loading schedule relationships
9. **FETCH_USER** - Mencari user dari patient
10. **USER_FOUND** - User ditemukan
11. **CHECK_SUBSCRIPTIONS** - Mengecek push subscriptions
12. **SUBSCRIPTIONS_RETRIEVED** - Subscriptions berhasil diambil
13. **SETUP_WEBPUSH** - Setup konfigurasi WebPush
14. **WEBPUSH_CREATED** - WebPush instance dibuat
15. **FORMAT_TIME** - Format waktu jadwal
16. **TIME_FORMATTED** - Waktu berhasil di-format
17. **CREATE_PAYLOAD** - Membuat payload notification
18. **PAYLOAD_CREATED** - Payload berhasil dibuat
19. **QUEUE_START** - Mulai queue notifications
20. **QUEUE_SUBSCRIPTION_0** - Queue subscription pertama
21. **WEBPUSH_SUB_OBJECT_0** - WebPushSubscription object dibuat
22. **QUEUE_SUCCESS_0** - Subscription berhasil di-queue
23. **QUEUE_COMPLETE** - Semua queue selesai
24. **FLUSH_START** - Mulai mengirim notifications
25. **FLUSH_EXECUTED** - Flush executed
26. **PROCESS_REPORT_1** - Memproses report pengiriman
27. **SEND_SUCCESS_1** - Notification berhasil dikirim
28. **FLUSH_COMPLETE** - Semua report diproses
29. **JOB_COMPLETE** - Job selesai
30. **JOB_END** - Akhir job dengan status final

## 📝 Informasi yang Dicatat di Setiap Step

### Step: SCHEDULE_CREATE_START
- Request data lengkap
- Timestamp

### Step: SCHEDULE_CREATED
- Schedule ID
- Data schedule lengkap (patient_id, officer_id, datetime, type, status, message)
- Queue connection info

### Step: JOB_DISPATCHED
- Schedule ID
- Job ID
- Queue connection
- Timestamp

### Step: JOB_HANDLE_START
- Schedule ID
- Job UUID
- Attempts
- Memory usage
- Data schedule lengkap

### Step: USER_FOUND
- User ID
- User role
- User owner_id

### Step: SUBSCRIPTIONS_RETRIEVED
- User ID
- Jumlah subscriptions
- Detail setiap subscription (ID, endpoint (short), has keys, created_at)

### Step: SETUP_WEBPUSH
- Status VAPID config (subject, public_key, private_key)
- Preview public key

### Step: PAYLOAD_CREATED
- User ID
- Schedule ID
- Payload data lengkap (JSON)
- Payload length

### Step: QUEUE_SUBSCRIPTION_X
- Subscription ID
- Subscription index
- Endpoint (short)
- Endpoint length

### Step: SEND_SUCCESS_X / SEND_FAILED_X
- Report index
- Endpoint
- Status code (untuk failed)
- Error reason (untuk failed)
- Response headers (untuk failed)

### Step: JOB_COMPLETE
- Summary lengkap (total_subscriptions, queued, success, failed, removed)
- Errors detail
- Memory usage final
- Memory peak
- Final status (SUCCESS / PARTIAL_FAILURE / COMPLETE_FAILURE)

## 🚨 Troubleshooting dengan Debug Log

### Jika Job Tidak Diproses

1. **Cek apakah job di-dispatch:**
```bash
grep "JOB_DISPATCHED" storage/logs/laravel.log | tail -n 10
```

2. **Cek apakah job di-construct:**
```bash
grep "JOB_CONSTRUCT" storage/logs/laravel.log | tail -n 10
```

3. **Cek apakah job handle dijalankan:**
```bash
grep "JOB_HANDLE_START" storage/logs/laravel.log | tail -n 10
```

### Jika User Tidak Ditemukan

```bash
grep "USER_NOT_FOUND" storage/logs/laravel.log | tail -n 10
```

Lihat detail patient_id dan schedule_id di log untuk debugging lebih lanjut.

### Jika Tidak Ada Subscriptions

```bash
grep "NO_SUBSCRIPTIONS" storage/logs/laravel.log | tail -n 10
```

Cek user_id dan schedule_id, kemudian verifikasi apakah user sudah subscribe.

### Jika VAPID Config Invalid

```bash
grep "SETUP_WEBPUSH" storage/logs/laravel.log | tail -n 5
```

Cek apakah semua VAPID keys sudah di-set dengan benar.

### Jika Pengiriman Gagal

```bash
grep "SEND_FAILED" storage/logs/laravel.log | tail -n 20
```

Cek status_code dan error reason untuk mengetahui penyebabnya.

### Jika Subscription Expired (410 Gone)

```bash
grep "EXPIRED_SUBSCRIPTION" storage/logs/laravel.log | tail -n 10
```

Log ini menunjukkan subscription yang expired dan akan dihapus otomatis.

## 📈 Monitoring Log dengan Script

### Script untuk Monitor Real-time

Buat file `monitor-push-notification.sh`:

```bash
#!/bin/bash
echo "🔍 Monitoring Push Notification Debug Logs..."
echo "Press Ctrl+C to stop"
echo ""
tail -f storage/logs/laravel.log | grep --line-buffered "PUSH NOTIFICATION DEBUG"
```

Jalankan:
```bash
chmod +x monitor-push-notification.sh
./monitor-push-notification.sh
```

### Script untuk Summary Job Terakhir

Buat file `summary-last-job.sh`:

```bash
#!/bin/bash
echo "📊 Summary Last Push Notification Job:"
echo ""
grep -A 2 "JOB_COMPLETE" storage/logs/laravel.log | tail -n 10
```

## 💡 Tips

1. **Gunakan `tail -f` untuk real-time monitoring**
2. **Filter dengan `grep` untuk fokus pada step tertentu**
3. **Gunakan `-A` dan `-B` untuk melihat context sekitar**
4. **Simpan log penting dengan redirect: `grep "PUSH NOTIFICATION DEBUG" storage/logs/laravel.log > push-debug-$(date +%Y%m%d).log`**

## 🔗 File-file Terkait

- `app/Http/Controllers/ScheduleController.php` - Dispatch job
- `app/Jobs/SendWebPushNotificationJob.php` - Job handler dengan debug
- `storage/logs/laravel.log` - File log utama

---

**Catatan**: Semua log menggunakan prefix `=== PUSH NOTIFICATION DEBUG:` untuk memudahkan filtering.

