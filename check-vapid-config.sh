#!/bin/bash

# Script untuk cek VAPID configuration dan subscription status
# Script ini tidak bergantung pada API endpoints yang memerlukan autentikasi

echo "🔍 VAPID Configuration & Subscription Checker"
echo "=============================================="
echo ""

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fungsi untuk print dengan warna
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Cek VAPID configuration
print_status "Checking VAPID configuration..."
echo ""

vapid_public=$(php artisan tinker --execute="echo env('VAPID_PUBLIC_KEY');")
vapid_private=$(php artisan tinker --execute="echo env('VAPID_PRIVATE_KEY');")
vapid_subject=$(php artisan tinker --execute="echo env('VAPID_SUBJECT');")

echo "VAPID Configuration:"
echo "==================="
echo "Public Key:  ${vapid_public:-'NOT SET'}"
echo "Private Key: ${vapid_private:-'NOT SET'}"
echo "Subject:     ${vapid_subject:-'NOT SET'}"
echo ""

if [ -n "$vapid_public" ] && [ -n "$vapid_private" ] && [ -n "$vapid_subject" ]; then
    print_success "VAPID configuration is complete"
else
    print_error "VAPID configuration is incomplete"
    echo ""
    echo "Missing configuration:"
    [ -z "$vapid_public" ] && echo "  ❌ VAPID_PUBLIC_KEY"
    [ -z "$vapid_private" ] && echo "  ❌ VAPID_PRIVATE_KEY"
    [ -z "$vapid_subject" ] && echo "  ❌ VAPID_SUBJECT"
    echo ""
    echo "Please add these to your .env file:"
    echo "VAPID_PUBLIC_KEY=BFZphZCNYOF_TeaMByns2sc1dKyLLDkkZCojA5tsCXd1_JDj-JX7l2VfTMg_0qU7RoYRO89uTRUtyAmxAgEWkhY"
    echo "VAPID_PRIVATE_KEY=MdTo9hA61q5F-FIw4o1UXEqbpk5s8aqPdZvIfgLJTXo"
    echo "VAPID_SUBJECT=mailto:admin@yourdomain.com"
    exit 1
fi

# Cek subscriptions
print_status "Checking push subscriptions..."
echo ""

subscription_count=$(php artisan tinker --execute="echo \App\Models\PushSubscription::count();")
echo "Total Subscriptions: $subscription_count"
echo ""

if [ "$subscription_count" -gt 0 ]; then
    print_success "Found $subscription_count active subscription(s)"
    echo ""
    echo "Subscription Details:"
    echo "===================="
    php artisan tinker --execute="
    \$subscriptions = \App\Models\PushSubscription::with('user')->get();
    foreach (\$subscriptions as \$index => \$sub) {
        echo 'Subscription #' . (\$index + 1) . ':' . PHP_EOL;
        echo '  User ID: ' . \$sub->user_id . PHP_EOL;
        echo '  User Name: ' . (\$sub->user->name ?? \$sub->user->email ?? 'Unknown') . PHP_EOL;
        echo '  Endpoint: ' . substr(\$sub->endpoint, 0, 60) . '...' . PHP_EOL;
        echo '  Created: ' . \$sub->created_at . PHP_EOL;
        echo '' . PHP_EOL;
    }
    "
else
    print_warning "No active subscriptions found"
    echo ""
    echo "To create subscriptions:"
    echo "1. Open browser: https://isac2025.himsiunair.com/webpush-test-public"
    echo "2. Click 'Request Permission' button"
    echo "3. Allow notifications when prompted"
    echo "4. Run this script again to verify"
fi

# Cek queue status
print_status "Checking queue status..."
echo ""

if pgrep -f "queue:work" > /dev/null; then
    print_success "Queue worker is running"
    echo "Process ID: $(pgrep -f "queue:work")"
else
    print_warning "Queue worker is not running"
    echo ""
    echo "To start queue worker:"
    echo "php artisan queue:work --tries=3 --timeout=60"
fi

echo ""
echo "🎯 Summary:"
echo "==========="
if [ -n "$vapid_public" ] && [ -n "$vapid_private" ] && [ -n "$vapid_subject" ] && [ "$subscription_count" -gt 0 ] && pgrep -f "queue:work" > /dev/null; then
    print_success "All systems ready for push notifications!"
    echo ""
    echo "✅ VAPID configuration: OK"
    echo "✅ Subscriptions: $subscription_count active"
    echo "✅ Queue worker: Running"
    echo ""
    echo "You can now test push notifications by:"
    echo "1. Creating a schedule via admin panel"
    echo "2. Running: ./test-cron-job-simple.sh"
else
    print_warning "Some components need attention:"
    [ -z "$vapid_public" ] || [ -z "$vapid_private" ] || [ -z "$vapid_subject" ] && echo "❌ VAPID configuration incomplete"
    [ "$subscription_count" -eq 0 ] && echo "❌ No subscriptions found"
    ! pgrep -f "queue:work" > /dev/null && echo "❌ Queue worker not running"
fi
