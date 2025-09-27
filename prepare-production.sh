#!/bin/bash

# SI TEDUH - Production Preparation Script
# Script untuk mempersiapkan aplikasi untuk deployment production

echo "🚀 SI TEDUH - Production Preparation"
echo "===================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    local status=$1
    local message=$2
    
    case $status in
        "success") echo -e "${GREEN}✅ $message${NC}" ;;
        "error") echo -e "${RED}❌ $message${NC}" ;;
        "warning") echo -e "${YELLOW}⚠️  $message${NC}" ;;
        "info") echo -e "${BLUE}ℹ️  $message${NC}" ;;
    esac
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_status "error" "Please run this script from Laravel root directory"
    exit 1
fi

echo ""
print_status "info" "Step 1: Checking prerequisites..."

# Check PHP version
php_version=$(php -r "echo PHP_VERSION;")
print_status "info" "PHP Version: $php_version"

# Check Composer
if command -v composer &> /dev/null; then
    print_status "success" "Composer is installed"
else
    print_status "error" "Composer is not installed"
    exit 1
fi

# Check Node.js
if command -v node &> /dev/null; then
    node_version=$(node --version)
    print_status "success" "Node.js Version: $node_version"
else
    print_status "error" "Node.js is not installed"
    exit 1
fi

# Check NPM
if command -v npm &> /dev/null; then
    npm_version=$(npm --version)
    print_status "success" "NPM Version: $npm_version"
else
    print_status "error" "NPM is not installed"
    exit 1
fi

echo ""
print_status "info" "Step 2: Installing dependencies..."

# Install PHP dependencies
print_status "info" "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader
if [ $? -eq 0 ]; then
    print_status "success" "PHP dependencies installed"
else
    print_status "error" "Failed to install PHP dependencies"
    exit 1
fi

# Install Node.js dependencies
print_status "info" "Installing Node.js dependencies..."
npm install --production
if [ $? -eq 0 ]; then
    print_status "success" "Node.js dependencies installed"
else
    print_status "error" "Failed to install Node.js dependencies"
    exit 1
fi

echo ""
print_status "info" "Step 3: Building assets for production..."

# Build Vite assets
print_status "info" "Building Vite assets..."
npm run build
if [ $? -eq 0 ]; then
    print_status "success" "Vite assets built successfully"
else
    print_status "error" "Failed to build Vite assets"
    exit 1
fi

# Check if build directory exists
if [ -d "public/build" ]; then
    print_status "success" "Build directory created: public/build/"
    ls -la public/build/
else
    print_status "error" "Build directory not found"
    exit 1
fi

echo ""
print_status "info" "Step 4: Optimizing Laravel for production..."

# Clear and cache config
print_status "info" "Caching configuration..."
php artisan config:cache
if [ $? -eq 0 ]; then
    print_status "success" "Configuration cached"
else
    print_status "error" "Failed to cache configuration"
fi

# Clear and cache routes
print_status "info" "Caching routes..."
php artisan route:cache
if [ $? -eq 0 ]; then
    print_status "success" "Routes cached"
else
    print_status "error" "Failed to cache routes"
fi

# Clear and cache views
print_status "info" "Caching views..."
php artisan view:cache
if [ $? -eq 0 ]; then
    print_status "success" "Views cached"
else
    print_status "error" "Failed to cache views"
fi

# Optimize autoloader
print_status "info" "Optimizing autoloader..."
composer dump-autoload --optimize
if [ $? -eq 0 ]; then
    print_status "success" "Autoloader optimized"
else
    print_status "error" "Failed to optimize autoloader"
fi

echo ""
print_status "info" "Step 5: Generating production keys..."

# Generate application key
print_status "info" "Generating application key..."
app_key=$(php artisan key:generate --show)
print_status "success" "Application key generated: $app_key"

# Generate VAPID keys
print_status "info" "Generating VAPID keys..."
if command -v web-push &> /dev/null; then
    vapid_keys=$(web-push generate-vapid-keys)
    print_status "success" "VAPID keys generated:"
    echo "$vapid_keys"
else
    print_status "warning" "web-push CLI not found. Install with: npm install -g web-push"
    print_status "info" "You can generate VAPID keys manually at: https://vapidkeys.com/"
fi

echo ""
print_status "info" "Step 6: Checking file permissions..."

# Set correct permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env 2>/dev/null || print_status "warning" ".env file not found (this is normal for production)"

print_status "success" "File permissions set"

echo ""
print_status "info" "Step 7: Running final checks..."

# Check if .env.example exists
if [ -f ".env.example" ]; then
    print_status "success" ".env.example found"
else
    print_status "warning" ".env.example not found"
fi

# Check if service worker exists
if [ -f "public/sw.js" ]; then
    print_status "success" "Service worker found: public/sw.js"
else
    print_status "error" "Service worker not found: public/sw.js"
fi

# Check if push notification script exists
if [ -f "public/js/push-notification.js" ]; then
    print_status "success" "Push notification script found: public/js/push-notification.js"
else
    print_status "error" "Push notification script not found: public/js/push-notification.js"
fi

# Check if testing page exists
if [ -f "resources/views/webpush-test.blade.php" ]; then
    print_status "success" "Testing page found: resources/views/webpush-test.blade.php"
else
    print_status "warning" "Testing page not found"
fi

echo ""
print_status "info" "Step 8: Creating production checklist..."

# Create production checklist
cat > production-checklist.md << EOF
# 📋 Production Deployment Checklist

## ✅ Pre-Deployment Checklist
- [ ] Assets built successfully (\`npm run build\`)
- [ ] PHP dependencies installed (\`composer install --no-dev\`)
- [ ] Laravel optimized (config, routes, views cached)
- [ ] Application key generated
- [ ] VAPID keys generated for production
- [ ] File permissions set correctly
- [ ] Service worker file exists (\`public/sw.js\`)
- [ ] Push notification script exists (\`public/js/push-notification.js\`)

## 🔧 Production Environment Variables
\`\`\`env
APP_NAME="SI TEDUH"
APP_ENV=production
APP_KEY=$app_key
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
\`\`\`

## 🚀 Post-Deployment Checklist
- [ ] Application accessible via HTTPS
- [ ] Database connected and migrated
- [ ] Queue worker running
- [ ] Web push notification working
- [ ] Schedule creation working
- [ ] SSL certificate valid
- [ ] Error logs clean

## 🧪 Testing URLs
- Main app: https://yourdomain.com
- Testing page: https://yourdomain.com/webpush-test-public
- API endpoints: https://yourdomain.com/api/webpush-test/stats

## 📞 Support
If you encounter issues, check:
1. Error logs: \`storage/logs/laravel.log\`
2. Queue status: \`php artisan queue:work --once\`
3. Web push config: \`curl https://yourdomain.com/api/webpush-test/vapid-config\`
EOF

print_status "success" "Production checklist created: production-checklist.md"

echo ""
echo "=================================================="
print_status "success" "Production preparation completed!"
echo ""
print_status "info" "Next steps:"
echo "1. Upload files to your production server"
echo "2. Configure .env file with production values"
echo "3. Run database migrations"
echo "4. Setup queue worker"
echo "5. Test web push notifications"
echo ""
print_status "info" "Generated files:"
echo "- production-checklist.md (deployment checklist)"
echo "- public/build/ (compiled assets)"
echo ""
print_status "info" "Important:"
echo "- Update VAPID keys in .env file for production"
echo "- Ensure HTTPS is enabled (required for web push)"
echo "- Test all functionality before going live"
echo ""
print_status "success" "Ready for production deployment! 🚀"
