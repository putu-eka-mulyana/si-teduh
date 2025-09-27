# 🚀 Panduan Deployment ke cPanel/Shared Hosting - SI TEDUH

## 📋 **STEP-BY-STEP DEPLOYMENT**

### **STEP 1: Persiapan File untuk Upload**

#### **File yang HARUS diupload:**
```
✅ app/
✅ bootstrap/
✅ config/
✅ database/
✅ public/
   ✅ build/          # ← Folder ini HARUS ada (dari npm run build)
   ✅ images/
   ✅ js/
   ✅ sw.js           # ← Service Worker
   ✅ index.php
✅ resources/
✅ routes/
✅ storage/
✅ vendor/            # ← Install via composer di server
✅ .env               # ← Konfigurasi production
✅ .htaccess          # ← Untuk Apache
✅ composer.json
✅ composer.lock
✅ package.json
```

#### **File yang TIDAK perlu diupload:**
```
❌ node_modules/
❌ .git/
❌ .env.example
❌ *.md files
❌ test files
```

---

### **STEP 2: Upload File ke Server**

#### **Via cPanel File Manager:**
1. **Login ke cPanel**
2. **Buka File Manager**
3. **Navigate ke `public_html`**
4. **Upload semua file** (drag & drop atau zip extract)

#### **Via FTP/SFTP:**
```bash
# Upload ke server
scp -r . user@yourdomain.com:/home/username/public_html/
```

---

### **STEP 3: Setup Environment (.env)**

#### **Buat file `.env` di server:**
```env
APP_NAME="SI TEDUH"
APP_ENV=production
APP_KEY=base64:YpQsfs2fwdu6eEA8NxiHDKdAiPEc84P12JRmMUS9+O4=
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
VAPID_PUBLIC_KEY=BFZphZCNYOF_TeaMByns2sc1dKyLLDkkZCojA5tsCXd1_JDj-JX7l2VfTMg_0qU7RoYRO89uTRUtyAmxAgEWkhY
VAPID_PRIVATE_KEY=MdTo9hA61q5F-FIw4o1UXEqbpk5s8aqPdZvIfgLJTXo
VAPID_SUBJECT=mailto:admin@yourdomain.com

# Queue Configuration
QUEUE_CONNECTION=database

# Broadcasting
BROADCAST_DRIVER=log
```

---

### **STEP 4: Setup Database**

#### **Buat Database di cPanel:**
1. **MySQL Databases** → **Create Database**
2. **Database Name:** `si_teduh_production`
3. **Create User** dengan password yang kuat
4. **Add User to Database** dengan privileges **ALL PRIVILEGES**

#### **Run Migration:**
```bash
# Via cPanel Terminal (jika tersedia)
php artisan migrate --force

# Atau via SSH
ssh user@yourdomain.com
cd /home/username/public_html/
php artisan migrate --force
```

---

### **STEP 5: Install Dependencies**

#### **Via cPanel Terminal:**
```bash
# Navigate ke folder aplikasi
cd /home/username/public_html/

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies (jika diperlukan)
npm install --production
```

#### **Via SSH:**
```bash
# Login ke server
ssh user@yourdomain.com
cd /home/username/public_html/

# Install dependencies
composer install --no-dev --optimize-autoloader
```

---

### **STEP 6: Setup File Permissions**

#### **Via cPanel File Manager:**
1. **Right-click** folder `storage/` → **Change Permissions** → **755**
2. **Right-click** folder `bootstrap/cache/` → **Change Permissions** → **755**
3. **Right-click** file `.env` → **Change Permissions** → **644**

#### **Via SSH:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

---

### **STEP 7: Setup .htaccess untuk Laravel**

#### **Buat file `.htaccess` di root folder:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to public folder
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ /public/$1 [L,QSA]
</IfModule>
```

#### **File `.htaccess` di folder `public/` sudah ada:**
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

---

### **STEP 8: Setup Queue Worker (PENTING untuk Web Push)**

#### **Via cPanel Cron Jobs:**
1. **Cron Jobs** → **Add New Cron Job**
2. **Command:** `php /home/username/public_html/artisan queue:work --daemon`
3. **Frequency:** `* * * * *` (setiap menit)

#### **Atau gunakan scheduled cron:**
```bash
# Edit crontab
crontab -e

# Add line:
* * * * * cd /home/username/public_html && php artisan schedule:run >> /dev/null 2>&1
```

---

### **STEP 9: Testing Production**

#### **Test Basic Functionality:**
1. **Buka:** `https://yourdomain.com`
2. **Login** dengan kredensial admin
3. **Buat jadwal baru**
4. **Verifikasi** notifikasi terkirim

#### **Test Web Push Notification:**
1. **Buka:** `https://yourdomain.com/webpush-test-public`
2. **Allow notification permission**
3. **Test semua fitur notification**
4. **Verifikasi** notifikasi muncul

---

## 🔍 **TROUBLESHOOTING**

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
4. **Cek Queue Worker:** Pastikan queue worker berjalan

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

## ✅ **CHECKLIST DEPLOYMENT**

### **Pre-Deployment:**
- [ ] Assets built (`npm run build`)
- [ ] Environment configured
- [ ] VAPID keys generated
- [ ] Database prepared
- [ ] File permissions set

### **Post-Deployment:**
- [ ] Application accessible via HTTPS
- [ ] Database connected and migrated
- [ ] Queue worker running
- [ ] Web push notification working
- [ ] Schedule creation working
- [ ] SSL certificate valid
- [ ] Error logs clean

---

## 🎯 **IMPORTANT NOTES**

### **Web Push Notification Requirements:**
- ✅ **HTTPS Required** - Web push hanya bekerja di HTTPS
- ✅ **Queue Worker** - Harus running untuk kirim notifikasi
- ✅ **VAPID Keys** - Harus menggunakan production keys
- ✅ **Service Worker** - File `sw.js` harus accessible

### **Production Security:**
- ✅ `APP_DEBUG=false`
- ✅ `APP_ENV=production`
- ✅ Strong database passwords
- ✅ Secure VAPID keys
- ✅ HTTPS enabled
- ✅ File permissions correct

---

## 🚀 **GO LIVE!**

Setelah semua checklist terpenuhi:

1. **Test semua fitur** di production
2. **Monitor logs** untuk error
3. **Setup backup** database
4. **Document production setup**

**SI TEDUH siap production dengan Web Push Notification yang berfungsi sempurna!** 🎉

---

## 📞 **SUPPORT**

Jika mengalami masalah:
1. **Cek error logs:** `storage/logs/laravel.log`
2. **Cek queue status:** `php artisan queue:work --once`
3. **Cek web push config:** `curl https://yourdomain.com/api/webpush-test/vapid-config`
4. **Cek file permissions:** `ls -la storage/ bootstrap/cache/`
