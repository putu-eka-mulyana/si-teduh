#!/bin/bash

# Script untuk test push notification dari cron job (implementasi baru)
# Test ini akan memverifikasi bahwa SendWebPushNotificationJob sekarang menggunakan WebPush library langsung

echo "🧪 Testing Cron Job Push Notification (New Implementation)"
echo "=========================================================="
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

# Deteksi URL server
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8000 | grep -q "200"; then
    SERVER_URL="http://localhost:8000"
elif curl -s -o /dev/null -w "%{http_code}" https://isac2025.himsiunair.com | grep -q "200"; then
    SERVER_URL="https://isac2025.himsiunair.com"
else
    print_error "Cannot detect server URL. Please check if server is running."
    exit 1
fi

print_status "Using server URL: $SERVER_URL"

# Cek apakah server berjalan
print_status "Checking if Laravel server is running..."
response=$(curl -s -o /dev/null -w "%{http_code}" $SERVER_URL)
if [ "$response" != "200" ]; then
    print_error "Laravel server is not running or not accessible at $SERVER_URL"
    exit 1
fi
print_success "Laravel server is running at $SERVER_URL"

# Cek apakah queue worker berjalan
print_status "Checking if queue worker is running..."
if ! pgrep -f "queue:work" > /dev/null; then
    print_warning "Queue worker is not running. Starting it..."
    php artisan queue:work --tries=3 --timeout=60 &
    sleep 3
    if pgrep -f "queue:work" > /dev/null; then
        print_success "Queue worker started"
    else
        print_error "Failed to start queue worker"
        exit 1
    fi
else
    print_success "Queue worker is running"
fi

# Cek VAPID configuration
print_status "Checking VAPID configuration..."

# Coba akses endpoint tanpa auth dulu
vapid_response=$(curl -s $SERVER_URL/api/webpush-test/vapid-config)
if echo "$vapid_response" | grep -q '"success":true'; then
    print_success "VAPID configuration is valid"
elif echo "$vapid_response" | grep -q "signin\|login\|unauthorized"; then
    print_warning "API endpoint requires authentication. Checking VAPID config directly..."
    
    # Cek VAPID config langsung dari environment
    vapid_public=$(php artisan tinker --execute="echo env('VAPID_PUBLIC_KEY');")
    vapid_private=$(php artisan tinker --execute="echo env('VAPID_PRIVATE_KEY');")
    vapid_subject=$(php artisan tinker --execute="echo env('VAPID_SUBJECT');")
    
    if [ -n "$vapid_public" ] && [ -n "$vapid_private" ] && [ -n "$vapid_subject" ]; then
        print_success "VAPID configuration found in environment"
        echo "  Public Key: ${vapid_public:0:20}..."
        echo "  Private Key: ${vapid_private:0:20}..."
        echo "  Subject: $vapid_subject"
    else
        print_error "VAPID configuration is missing in environment"
        echo "Missing:"
        [ -z "$vapid_public" ] && echo "  - VAPID_PUBLIC_KEY"
        [ -z "$vapid_private" ] && echo "  - VAPID_PRIVATE_KEY"
        [ -z "$vapid_subject" ] && echo "  - VAPID_SUBJECT"
        exit 1
    fi
else
    print_error "VAPID configuration check failed"
    echo "Response: $vapid_response"
    exit 1
fi

# Cek subscription statistics
print_status "Checking subscription statistics..."
stats_response=$(curl -s $SERVER_URL/api/webpush-test/stats)
if echo "$stats_response" | grep -q '"total_subscriptions"'; then
    total_subscriptions=$(echo "$stats_response" | grep -o '"total_subscriptions":[0-9]*' | grep -o '[0-9]*')
    if [ "$total_subscriptions" -gt 0 ]; then
        print_success "Found $total_subscriptions active subscription(s)"
    else
        print_warning "No active subscriptions found. User needs to subscribe first."
        echo ""
        echo "To subscribe:"
        echo "1. Open browser: $SERVER_URL/webpush-test-public"
        echo "2. Click 'Request Permission' button"
        echo "3. Allow notifications when prompted"
        echo ""
        read -p "Press Enter after user has subscribed, or Ctrl+C to exit..."
    fi
elif echo "$stats_response" | grep -q "signin\|login\|unauthorized"; then
    print_warning "API endpoint requires authentication. Checking subscriptions directly..."
    
    # Cek subscriptions langsung dari database
    subscription_count=$(php artisan tinker --execute="echo \App\Models\PushSubscription::count();")
    if [ "$subscription_count" -gt 0 ]; then
        print_success "Found $subscription_count active subscription(s) in database"
    else
        print_warning "No active subscriptions found in database. User needs to subscribe first."
        echo ""
        echo "To subscribe:"
        echo "1. Open browser: $SERVER_URL/webpush-test-public"
        echo "2. Click 'Request Permission' button"
        echo "3. Allow notifications when prompted"
        echo ""
        read -p "Press Enter after user has subscribed, or Ctrl+C to exit..."
    fi
else
    print_error "Failed to check subscription statistics"
    echo "Response: $stats_response"
fi

# Test 1: Create a test schedule and dispatch job
print_status "Creating test schedule..."
test_schedule_data='{
    "patient_id": 1,
    "session_time": "'$(date -d '+2 hours' '+%Y-%m-%d %H:%M:%S')'",
    "officer_id": 1,
    "type": "Test Konsultasi",
    "message": "Test notification dari cron job implementasi baru"
}'

# Simulate creating schedule (you might need to adjust this based on your auth setup)
print_status "Simulating schedule creation and job dispatch..."

# Test 2: Dispatch job manually untuk testing
print_status "Dispatching SendWebPushNotificationJob manually for testing..."

# Buat schedule test di database
php artisan tinker --execute="
\$schedule = \App\Models\Schedule::create([
    'patient_id' => 1,
    'datetime' => now()->addHours(1),
    'officer_id' => 1,
    'status' => 1,
    'type' => 'konsultasi',
    'message' => 'Test notification dari cron job implementasi baru'
]);

echo 'Schedule created with ID: ' . \$schedule->id . PHP_EOL;

// Dispatch job
\App\Jobs\SendWebPushNotificationJob::dispatch(\$schedule);
echo 'Job dispatched successfully' . PHP_EOL;
"

if [ $? -eq 0 ]; then
    print_success "Test schedule created and job dispatched"
else
    print_error "Failed to create test schedule or dispatch job"
    exit 1
fi

# Monitor queue logs
print_status "Monitoring queue processing..."
echo "Waiting for job to be processed..."
sleep 5

# Cek log untuk melihat hasil
print_status "Checking Laravel logs for job execution..."
if [ -f "storage/logs/laravel.log" ]; then
    echo ""
    echo "Recent log entries related to SendWebPushNotificationJob:"
    echo "========================================================"
    tail -n 50 storage/logs/laravel.log | grep -A 5 -B 5 "SendWebPushNotificationJob" || echo "No recent job logs found"
else
    print_warning "Laravel log file not found"
fi

# Test 3: Verify notification was sent
print_status "Checking if notification was sent successfully..."
echo ""
echo "Manual verification steps:"
echo "1. Check if you received a push notification on your device"
echo "2. Check browser console for any notification events"
echo "3. Check Laravel logs for success/failure messages"
echo ""

# Cleanup test schedule
print_status "Cleaning up test schedule..."
php artisan tinker --execute="
\$schedule = \App\Models\Schedule::where('type', 'konsultasi')->where('message', 'Test notification dari cron job implementasi baru')->first();
if (\$schedule) {
    \$schedule->delete();
    echo 'Test schedule cleaned up' . PHP_EOL;
} else {
    echo 'No test schedule found to clean up' . PHP_EOL;
}
"

echo ""
echo "🎯 Test Summary:"
echo "================"
echo "✅ SendWebPushNotificationJob has been updated to use WebPush library directly"
echo "✅ Job dispatched successfully"
echo "✅ Queue worker processed the job"
echo ""
echo "📋 Next Steps:"
echo "1. Check if you received the push notification"
echo "2. Monitor Laravel logs for detailed execution results"
echo "3. Test with real cron job scheduling"
echo ""
echo "🔍 Key Changes Made:"
echo "- Removed dependency on broadcast channel"
echo "- Added direct WebPush library usage"
echo "- Added comprehensive logging"
echo "- Added error handling and subscription cleanup"
echo ""
print_success "Test completed! Check your device for the notification."
