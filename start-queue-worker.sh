#!/bin/bash

# Script untuk menjalankan Laravel Queue Worker
# Digunakan untuk memproses web push notification jobs

echo "🚀 Starting Laravel Queue Worker for Web Push Notifications..."
echo "📁 Working directory: $(pwd)"
echo "⏰ Started at: $(date)"
echo ""

# Pastikan kita berada di direktori yang benar
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from Laravel root directory."
    exit 1
fi

# Jalankan queue worker
echo "🔄 Starting queue worker..."
php artisan queue:work --verbose --tries=3 --timeout=60

echo ""
echo "⏹️  Queue worker stopped at: $(date)"
