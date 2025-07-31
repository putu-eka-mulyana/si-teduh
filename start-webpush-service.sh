#!/bin/bash

# Script untuk menjalankan Web Push Notification Service SI TEDUH
echo "🚀 Memulai Web Push Notification Service SI TEDUH..."

# Cek apakah composer dependencies sudah terinstall
if [ ! -d "vendor" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install
fi

# Cek apakah node_modules sudah terinstall
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm install
fi

# Generate application key jika belum ada
if [ ! -f ".env" ]; then
    echo "🔑 Copying .env.example to .env..."
    cp .env.example .env
    php artisan key:generate
fi

# Jalankan migration
echo "🗄️ Running database migrations..."
php artisan migrate

# Build assets
echo "🔨 Building frontend assets..."
npm run build

# Jalankan queue worker untuk web push notification
echo "🔄 Starting queue worker for web push notifications..."
php artisan queue:work --queue=default --tries=3 --timeout=90 &

# Jalankan development server
echo "🌐 Starting Laravel development server..."
php artisan serve --host=0.0.0.0 --port=8000 &

# Jalankan Vite development server
echo "⚡ Starting Vite development server..."
npm run dev &

echo "✅ Web Push Notification Service SI TEDUH berhasil dijalankan!"
echo "📱 Aplikasi dapat diakses di: http://localhost:8000"
echo "🔧 Vite dev server: http://localhost:5173"
echo ""
echo "📋 Catatan Penting:"
echo "1. Pastikan VAPID keys sudah dikonfigurasi di .env"
echo "2. Ganti YOUR_VAPID_PUBLIC_KEY di public/js/push-notification.js"
echo "3. Untuk production, gunakan HTTPS"
echo ""
echo "🛑 Untuk menghentikan service, tekan Ctrl+C"

# Tunggu sampai semua proses selesai
wait 