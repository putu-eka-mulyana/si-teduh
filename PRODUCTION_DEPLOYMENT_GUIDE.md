# 🚀 Panduan Deployment Production - SI TEDUH

## 📋 Prasyarat Production

### 1. **Domain dengan HTTPS**
- ✅ Domain sudah aktif
- ✅ SSL Certificate terpasang (HTTPS)
- ⚠️ **PENTING:** Web Push Notification hanya bekerja di HTTPS

### 2. **Hosting Requirements**
- PHP 8.1+ (recommended PHP 8.2)
- MySQL 5.7+ atau MariaDB 10.3+
- Composer
- Node.js & NPM (untuk build assets)
- cPanel atau akses SSH

---

## 🔧 Persiapan Sebelum Deployment

### Step 1: Optimize untuk Production
```bash
# Di local development
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Step 2: Build Assets untuk Production
```bash
# Build Vite assets untuk production
npm run build
```

### Step 3: Generate Production Key
```bash
# Generate application key
php artisan key:generate --show
# Copy key ini untuk .env production
```

### Step 4: Generate VAPID Keys untuk Production
```bash
# Generate VAPID keys baru untuk production
npm install -g web-push
web-push generate-vapid-keys
# Copy public key dan private key untuk .env production
```

---

## 📁 File yang Perlu Diupload

### **Struktur File untuk Upload:**
```
si-teduh/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── build/          # ← Folder ini harus ada (dari npm run build)
│   ├── images/
│   ├── js/
│   ├── sw.js           # ← Service Worker
│   └── index.php
├── resources/
├── routes/
├── storage/
├── vendor/             # ← Install via composer di server
├── .env                # ← Konfigurasi production
├── .htaccess           # ← Untuk Apache
├── composer.json
├── composer.lock
└── package.json
```

---

## 🗂️ Step-by-Step Deployment ke cPanel/Shared Hosting

### **STEP 1: Upload File ke Server**

#### **Via cPanel File Manager:**
1. **Login ke cPanel**
2. **Buka File Manager**
3. **Navigate ke `public_html` atau folder domain Anda**
4. **Upload semua file** (kecuali `vendor/` dan `node_modules/`)

#### **Via FTP/SFTP:**
```bash
# Upload file ke server
scp -r . user@yourdomain.com:/path/to/public_html/
# Atau gunakan FileZilla/WinSCP
```

### **STEP 2: Setup Environment File**

#### **Buat file `.env` di server:**
```env
APP_NAME="SI TEDUH"
APP_ENV=production
APP_KEY=base64:YOUR_PRODUCTION_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Web Push Configuration (PRODUCTION)
VAPID_PUBLIC_KEY=YOUR_PRODUCTION_VAPID_PUBLIC_KEY
VAPID_PRIVATE_KEY=YOUR_PRODUCTION_VAPID_PRIVATE_KEY
VAPID_SUBJECT=mailto:your-email@yourdomain.com

# Queue Configuration
QUEUE_CONNECTION=database

# Broadcasting
BROADCAST_DRIVER=log

# Mail Configuration (jika diperlukan)
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@yourdomain.com
MAIL_FROM_NAME="SI TEDUH"
```

### **STEP 3: Install Dependencies**

#### **Via cPanel Terminal (jika tersedia):**
```bash
# Navigate ke folder aplikasi
cd /home/username/public_html/

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies (jika diperlukan)
npm install --production

# Build assets untuk production
npm run build
```

#### **Via SSH (jika akses SSH tersedia):**
```bash
# Login ke server via SSH
ssh user@yourdomain.com

# Navigate ke folder aplikasi
cd /path/to/your/app

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install --production
npm run build
```

### **STEP 4: Setup Database**

#### **Buat Database di cPanel:**
1. **MySQL Databases** → **Create Database**
2. **Database Name:** `si_teduh_production`
3. **Create User** dengan password yang kuat
4. **Add User to Database** dengan privileges **ALL PRIVILEGES**

#### **Run Migration:**
```bash
# Via cPanel Terminal atau SSH
php artisan migrate --force

# Seed data jika diperlukan
php artisan db:seed --force
```

### **STEP 5: Setup File Permissions**

#### **Via cPanel File Manager:**
1. **Right-click** folder `storage/`
2. **Change Permissions** → **755**
3. **Right-click** folder `bootstrap/cache/`
4. **Change Permissions** → **755**

#### **Via SSH:**
```bash
# Set permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

### **STEP 6: Setup .htaccess untuk Laravel**

#### **Buat file `.htaccess` di root folder:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to public folder
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L,QSA]
</IfModule>
```

#### **Buat file `.htaccess` di folder `public/`:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    
    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]
    
    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### **STEP 7: Setup Cron Job untuk Queue**

#### **Via cPanel Cron Jobs:**
1. **Cron Jobs** → **Add New Cron Job**
2. **Command:** `php /home/username/public_html/artisan queue:work --daemon`
3. **Frequency:** `* * * * *` (setiap menit)

#### **Atau gunakan scheduled cron:**
```bash
# Edit crontab
crontab -e

# Add line:
* * * * * cd /path/to/your/app && php artisan schedule:run >> /dev/null 2>&1
```

### **STEP 8: Update JavaScript untuk Production**

#### **Update `public/js/push-notification.js`:**
```javascript
// Ganti VAPID public key dengan production key
this.applicationServerKey = this.urlBase64ToUint8Array('YOUR_PRODUCTION_VAPID_PUBLIC_KEY');
```

---

## 🔍 Testing Production Deployment

### **Step 1: Basic Functionality Test**
```bash
# Test aplikasi bisa diakses
curl -I https://yourdomain.com

# Test database connection
php artisan tinker --execute="echo 'DB Connected: ' . \DB::connection()->getPdo() ? 'Yes' : 'No';"
```

### **Step 2: Web Push Notification Test**
1. **Buka:** `https://yourdomain.com/webpush-test-public`
2. **Allow notification permission**
3. **Test semua fitur notification**
4. **Verifikasi notifikasi muncul**

### **Step 3: Schedule Creation Test**
1. **Login sebagai admin**
2. **Buat jadwal baru**
3. **Verifikasi notifikasi terkirim ke user**
4. **Cek queue job berjalan**

---

## 🚨 Troubleshooting Production

### **Error: 500 Internal Server Error**
```bash
# Cek error log
tail -f storage/logs/laravel.log

# Common fixes:
composer install --no-dev --optimize-autoloader
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### **Error: Web Push Not Working**
1. **Cek HTTPS:** Pastikan domain menggunakan HTTPS
2. **Cek VAPID Keys:** Pastikan production keys sudah benar
3. **Cek Service Worker:** Pastikan `sw.js` bisa diakses
4. **Cek Console:** Lihat error di browser console

### **Error: Queue Jobs Not Running**
```bash
# Start queue worker
php artisan queue:work --daemon

# Cek failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## 🔒 Security Checklist

### **Production Security:**
- ✅ `APP_DEBUG=false`
- ✅ `APP_ENV=production`
- ✅ Strong database passwords
- ✅ Secure VAPID keys
- ✅ HTTPS enabled
- ✅ File permissions correct
- ✅ `.env` file protected
- ✅ `vendor/` folder protected

### **File yang TIDAK boleh diakses public:**
- `.env`
- `composer.json` (optional)
- `package.json` (optional)
- `storage/` (kecuali storage/app/public)
- `vendor/`

---

## 📊 Monitoring Production

### **Setup Monitoring:**
1. **Error Logs:** Monitor `storage/logs/laravel.log`
2. **Queue Jobs:** Monitor failed jobs
3. **Database:** Monitor performance
4. **Web Push:** Monitor notification delivery

### **Health Check Script:**
```bash
#!/bin/bash
# production-health-check.sh

echo "🔍 Production Health Check"
echo "=========================="

# Check application
curl -f https://yourdomain.com > /dev/null && echo "✅ App accessible" || echo "❌ App not accessible"

# Check database
php artisan tinker --execute="echo \DB::connection()->getPdo() ? '✅ DB connected' : '❌ DB error';"

# Check queue
php artisan queue:work --once > /dev/null && echo "✅ Queue working" || echo "❌ Queue error"

# Check web push
curl -f https://yourdomain.com/api/webpush-test/vapid-config > /dev/null && echo "✅ Web Push config OK" || echo "❌ Web Push config error"
```

---

## 🎯 Final Checklist

### **Pre-Deployment:**
- [ ] Assets built (`npm run build`)
- [ ] Environment configured
- [ ] VAPID keys generated
- [ ] Database prepared
- [ ] File permissions set

### **Post-Deployment:**
- [ ] Application accessible
- [ ] Database connected
- [ ] Queue worker running
- [ ] Web push working
- [ ] Schedule creation working
- [ ] Notifications delivered
- [ ] SSL certificate valid
- [ ] Error logs clean

---

## 🚀 Go Live!

Setelah semua checklist terpenuhi:

1. **Update DNS** ke server baru
2. **Test semua fitur** di production
3. **Monitor logs** untuk error
4. **Setup backup** database
5. **Document production setup**

**SI TEDUH siap production dengan Web Push Notification yang berfungsi sempurna!** 🎉
