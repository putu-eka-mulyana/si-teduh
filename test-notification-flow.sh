#!/bin/bash

# Test Notification Flow Script
# Untuk menganalisis perbedaan antara direct notification dan server push

echo "🔍 SI TEDUH - Notification Flow Analysis"
echo "========================================"

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

echo ""
print_status "info" "Step 1: Checking current subscription status..."

# Cek subscription stats
response=$(curl -s http://localhost:8000/api/webpush-test/stats)
echo "Response: $response"

total_subs=$(echo "$response" | grep -o '"total_subscriptions":[0-9]*' | grep -o '[0-9]*')
total_users=$(echo "$response" | grep -o '"total_users_with_subscriptions":[0-9]*' | grep -o '[0-9]*')

print_status "info" "Total subscriptions: $total_subs"
print_status "info" "Users with subscriptions: $total_users"

echo ""
print_status "info" "Step 2: Testing VAPID configuration..."

# Test VAPID config
vapid_response=$(curl -s http://localhost:8000/api/webpush-test/vapid-config)
echo "VAPID Response: $vapid_response"

if echo "$vapid_response" | grep -q '"success":true'; then
    print_status "success" "VAPID configuration is valid"
else
    print_status "error" "VAPID configuration is invalid"
fi

echo ""
print_status "info" "Step 3: Testing notification endpoint with user ID 1..."

# Test dengan user ID 1
test_response=$(curl -s -X POST http://localhost:8000/api/webpush-test/send-notification \
    -H "Content-Type: application/json" \
    -d '{"user_id": 1, "title": "Test Notification", "body": "Test message"}')

echo "Test Response: $test_response"

if echo "$test_response" | grep -q '"success":true'; then
    success_count=$(echo "$test_response" | grep -o '"success_count":[0-9]*' | grep -o '[0-9]*')
    error_count=$(echo "$test_response" | grep -o '"error_count":[0-9]*' | grep -o '[0-9]*')
    
    print_status "success" "Notification sent successfully"
    print_status "info" "Success count: $success_count"
    print_status "info" "Error count: $error_count"
    
    # Cek apakah ada error 410
    if echo "$test_response" | grep -q "410\|Gone\|expired"; then
        print_status "warning" "Found expired subscriptions (410 Gone error)"
    fi
else
    print_status "error" "Notification failed"
fi

echo ""
print_status "info" "Step 4: Testing notification endpoint with user ID 2..."

# Test dengan user ID 2
test_response2=$(curl -s -X POST http://localhost:8000/api/webpush-test/send-notification \
    -H "Content-Type: application/json" \
    -d '{"user_id": 2, "title": "Test Notification", "body": "Test message"}')

echo "Test Response 2: $test_response2"

if echo "$test_response2" | grep -q '"success":true'; then
    success_count=$(echo "$test_response2" | grep -o '"success_count":[0-9]*' | grep -o '[0-9]*')
    error_count=$(echo "$test_response2" | grep -o '"error_count":[0-9]*' | grep -o '[0-9]*')
    
    print_status "success" "Notification sent successfully"
    print_status "info" "Success count: $success_count"
    print_status "info" "Error count: $error_count"
    
    # Cek apakah ada error 410
    if echo "$test_response2" | grep -q "410\|Gone\|expired"; then
        print_status "warning" "Found expired subscriptions (410 Gone error)"
    fi
else
    print_status "error" "Notification failed"
fi

echo ""
print_status "info" "Step 5: Checking database subscriptions..."

# Cek subscription di database
php artisan tinker --execute="
echo 'Database subscriptions:' . PHP_EOL;
\$subs = \App\Models\PushSubscription::all();
foreach(\$subs as \$sub) {
    echo 'User ID: ' . \$sub->user_id . ' - Endpoint: ' . substr(\$sub->endpoint, 0, 50) . '...' . PHP_EOL;
}
echo 'Total: ' . \$subs->count() . PHP_EOL;
"

echo ""
print_status "info" "Step 6: Recommendations..."

echo ""
echo "📋 ANALISIS HASIL:"
echo "=================="
echo ""
echo "1. DIRECT NOTIFICATION vs SEND TEST NOTIFICATION:"
echo "   - Direct Notification: Menggunakan browser API langsung ✅"
echo "   - Send Test Notification: Menggunakan server → FCM → browser ❌"
echo ""
echo "2. KENAPA SEND TEST NOTIFICATION GAGAL:"
echo "   - Subscription expired/invalid (410 Gone error)"
echo "   - FCM endpoint tidak valid lagi"
echo "   - Browser sudah unsubscribe secara otomatis"
echo ""
echo "3. SOLUSI:"
echo "   - User perlu subscribe ulang untuk mendapatkan endpoint fresh"
echo "   - Clear expired subscriptions dari database"
echo "   - Test dengan subscription yang baru"
echo ""

print_status "info" "To fix this issue:"
echo "1. Open browser to: http://localhost:8000/webpush-test-public"
echo "2. Click 'Request Permission' button"
echo "3. Allow notification permission"
echo "4. Wait for subscription to be created"
echo "5. Try 'Send Test Notification' again"
echo ""

print_status "info" "Or clear all subscriptions and start fresh:"
echo "curl -X DELETE http://localhost:8000/api/webpush-test/clear-subscriptions"
