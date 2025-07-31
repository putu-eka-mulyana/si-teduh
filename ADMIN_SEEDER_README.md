# Admin Seeder Documentation

## Deskripsi
Seeder ini digunakan untuk membuat data admin default beserta user account yang terhubung.

## Data Admin yang Dibuat
Seeder akan membuat 3 admin dengan data berikut:

1. **Dr. Ahmad Supriadi**
   - Job Title: Kepala Puskesmas
   - Phone: 081234567890
   - Password: password123

2. **Siti Nurhaliza**
   - Job Title: Admin Puskesmas
   - Phone: 081234567891
   - Password: password123

3. **Budi Santoso**
   - Job Title: Staff Admin
   - Phone: 081234567892
   - Password: password123

## Cara Menjalankan Seeder

### Menjalankan Semua Seeder
```bash
php artisan db:seed
```

### Menjalankan Admin Seeder Saja
```bash
php artisan db:seed --class=AdminSeeder
```

### Menjalankan Seeder dengan Fresh Migration
```bash
php artisan migrate:fresh --seed
```

## Struktur Data
Seeder akan membuat:
1. Record di tabel `admins` dengan field `fullname` dan `jobtitle`
2. Record di tabel `users` dengan role 'ADMIN' yang terhubung ke admin melalui `owner_id`

## Catatan Keamanan
- Password default adalah 'password123' - pastikan untuk mengubah password setelah deployment
- Nomor telepon yang digunakan adalah dummy, sesuaikan dengan kebutuhan
- Seeder ini hanya untuk development/testing, jangan gunakan di production tanpa modifikasi

## Modifikasi Data
Untuk mengubah data admin, edit file `database/seeders/AdminSeeder.php` dan sesuaikan array `$admins`. 