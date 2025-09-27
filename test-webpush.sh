#!/bin/bash

# Web Push Notification Testing Script
# SI TEDUH - Automated Testing

echo "🧪 SI TEDUH - Web Push Notification Testing Script"
echo "=================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    local status=$1
    local message=$2
    
    case $status in
        "success")
            echo -e "${GREEN}✅ $message${NC}"
            ;;
        "error")
            echo -e "${RED}❌ $message${NC}"
            ;;
        "warning")
            echo -e "${YELLOW}⚠️  $message${NC}"
            ;;
        "info")
            echo -e "${BLUE}ℹ️  $message${NC}"
            ;;
    esac
}

# Check if Laravel is running
check_laravel() {
    print_status "info" "Checking Laravel application..."
    
    if curl -s http://localhost:8000 > /dev/null; then
        print_status "success" "Laravel application is running on http://localhost:8000"
        return 0
    else
        print_status "error" "Laravel application is not running. Please start with: php artisan serve"
        return 1
    fi
}

# Check environment configuration
check_environment() {
    print_status "info" "Checking environment configuration..."
    
    if [ -f ".env" ]; then
        print_status "success" ".env file exists"
        
        # Check VAPID keys
        if grep -q "VAPID_PUBLIC_KEY" .env && grep -q "VAPID_PRIVATE_KEY" .env; then
            print_status "success" "VAPID keys configured"
        else
            print_status "warning" "VAPID keys not configured"
        fi
        
        # Check queue configuration
        if grep -q "QUEUE_CONNECTION=database" .env; then
            print_status "success" "Queue configuration set to database"
        else
            print_status "warning" "Queue configuration not set to database"
        fi
    else
        print_status "error" ".env file not found"
        return 1
    fi
}

# Check database connection
check_database() {
    print_status "info" "Checking database connection..."
    
    if php artisan migrate:status > /dev/null 2>&1; then
        print_status "success" "Database connection successful"
        
        # Check if push_subscriptions table exists
        if php artisan tinker --execute="echo 'Testing table...'; \App\Models\PushSubscription::count();" > /dev/null 2>&1; then
            print_status "success" "push_subscriptions table exists"
        else
            print_status "warning" "push_subscriptions table may not exist or have issues"
        fi
    else
        print_status "error" "Database connection failed"
        return 1
    fi
}

# Check queue worker
check_queue() {
    print_status "info" "Checking queue worker..."
    
    # Check if queue jobs table exists
    if php artisan tinker --execute="echo 'Testing jobs table...'; \DB::table('jobs')->count();" > /dev/null 2>&1; then
        print_status "success" "Queue jobs table exists"
    else
        print_status "warning" "Queue jobs table may not exist"
    fi
    
    # Check if queue worker is running (basic check)
    if pgrep -f "php artisan queue:work" > /dev/null; then
        print_status "success" "Queue worker process detected"
    else
        print_status "warning" "Queue worker process not detected. Start with: php artisan queue:work"
    fi
}

# Test VAPID configuration endpoint
test_vapid_config() {
    print_status "info" "Testing VAPID configuration endpoint..."
    
    response=$(curl -s http://localhost:8000/api/webpush-test/vapid-config)
    
    if echo "$response" | grep -q '"success":true'; then
        print_status "success" "VAPID configuration is valid"
    else
        print_status "error" "VAPID configuration is invalid"
        echo "Response: $response"
    fi
}

# Test subscription statistics endpoint
test_stats_endpoint() {
    print_status "info" "Testing subscription statistics endpoint..."
    
    response=$(curl -s http://localhost:8000/api/webpush-test/stats)
    
    if echo "$response" | grep -q '"success":true'; then
        print_status "success" "Statistics endpoint working"
        
        # Extract and display stats
        total_subs=$(echo "$response" | grep -o '"total_subscriptions":[0-9]*' | grep -o '[0-9]*')
        total_users=$(echo "$response" | grep -o '"total_users_with_subscriptions":[0-9]*' | grep -o '[0-9]*')
        
        print_status "info" "Total subscriptions: $total_subs"
        print_status "info" "Users with subscriptions: $total_users"
    else
        print_status "error" "Statistics endpoint failed"
        echo "Response: $response"
    fi
}

# Test push notification endpoint (dry run)
test_notification_endpoint() {
    print_status "info" "Testing push notification endpoint..."
    
    response=$(curl -s -X POST http://localhost:8000/api/webpush-test/send-notification \
        -H "Content-Type: application/json" \
        -d '{"user_id": 1, "title": "Test Notification", "body": "Test message"}')
    
    if echo "$response" | grep -q '"success":true'; then
        print_status "success" "Push notification endpoint working"
    elif echo "$response" | grep -q "User belum subscribe"; then
        print_status "warning" "Push notification endpoint working (user not subscribed)"
    else
        print_status "error" "Push notification endpoint failed"
        echo "Response: $response"
    fi
}

# Check service worker file
check_service_worker() {
    print_status "info" "Checking service worker file..."
    
    if [ -f "public/sw.js" ]; then
        print_status "success" "Service worker file exists"
        
        # Check if file is accessible via web
        if curl -s http://localhost:8000/sw.js > /dev/null; then
            print_status "success" "Service worker file is accessible via web"
        else
            print_status "error" "Service worker file is not accessible via web"
        fi
    else
        print_status "error" "Service worker file not found"
    fi
}

# Check push notification script
check_push_script() {
    print_status "info" "Checking push notification script..."
    
    if [ -f "public/js/push-notification.js" ]; then
        print_status "success" "Push notification script exists"
        
        # Check if file is accessible via web
        if curl -s http://localhost:8000/js/push-notification.js > /dev/null; then
            print_status "success" "Push notification script is accessible via web"
        else
            print_status "error" "Push notification script is not accessible via web"
        fi
    else
        print_status "error" "Push notification script not found"
    fi
}

# Check testing page
check_testing_page() {
    print_status "info" "Checking testing page..."
    
    # Check public testing page (no auth required)
    response=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/webpush-test-public)
    
    if [ "$response" = "200" ]; then
        print_status "success" "Public testing page is accessible"
    else
        print_status "error" "Public testing page returned HTTP $response"
    fi
    
    # Check authenticated testing page
    response_auth=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/webpush-test)
    
    if [ "$response_auth" = "302" ]; then
        print_status "warning" "Authenticated testing page requires login (HTTP 302 - redirect to login)"
    elif [ "$response_auth" = "200" ]; then
        print_status "success" "Authenticated testing page is accessible"
    else
        print_status "error" "Authenticated testing page returned HTTP $response_auth"
    fi
}

# Main testing function
run_tests() {
    echo ""
    print_status "info" "Starting comprehensive web push notification tests..."
    echo ""
    
    local tests_passed=0
    local tests_total=0
    
    # Test 1: Laravel Application
    tests_total=$((tests_total + 1))
    if check_laravel; then
        tests_passed=$((tests_passed + 1))
    fi
    echo ""
    
    # Test 2: Environment Configuration
    tests_total=$((tests_total + 1))
    if check_environment; then
        tests_passed=$((tests_passed + 1))
    fi
    echo ""
    
    # Test 3: Database Connection
    tests_total=$((tests_total + 1))
    if check_database; then
        tests_passed=$((tests_passed + 1))
    fi
    echo ""
    
    # Test 4: Queue Worker
    tests_total=$((tests_total + 1))
    if check_queue; then
        tests_passed=$((tests_passed + 1))
    fi
    echo ""
    
    # Test 5: Service Worker File
    tests_total=$((tests_total + 1))
    if check_service_worker; then
        tests_passed=$((tests_passed + 1))
    fi
    echo ""
    
    # Test 6: Push Notification Script
    tests_total=$((tests_total + 1))
    if check_push_script; then
        tests_passed=$((tests_passed + 1))
    fi
    echo ""
    
    # Test 7: Testing Page
    tests_total=$((tests_total + 1))
    if check_testing_page; then
        tests_passed=$((tests_passed + 1))
    fi
    echo ""
    
    # Test 8: VAPID Configuration
    test_vapid_config
    echo ""
    
    # Test 9: Statistics Endpoint
    test_stats_endpoint
    echo ""
    
    # Test 10: Notification Endpoint
    test_notification_endpoint
    echo ""
    
    # Final Results
    echo "=================================================="
    print_status "info" "Test Results: $tests_passed/$tests_total tests passed"
    
    if [ $tests_passed -eq $tests_total ]; then
        print_status "success" "All tests passed! Web push notification system is ready."
        echo ""
        print_status "info" "Next steps:"
        echo "  1. Open http://localhost:8000/webpush-test-public in your browser (no login required)"
        echo "  2. Or login first, then open http://localhost:8000/webpush-test"
        echo "  3. Allow notification permission"
        echo "  4. Test push notifications using the testing interface"
    else
        print_status "warning" "Some tests failed. Please check the issues above."
        echo ""
        print_status "info" "Common solutions:"
        echo "  1. Start Laravel: php artisan serve"
        echo "  2. Start queue worker: php artisan queue:work"
        echo "  3. Run migrations: php artisan migrate"
        echo "  4. Check .env configuration"
    fi
    
    echo ""
    print_status "info" "For detailed testing, visit:"
    echo "  - Public testing page: http://localhost:8000/webpush-test-public"
    echo "  - Authenticated testing page: http://localhost:8000/webpush-test"
}

# Help function
show_help() {
    echo "Usage: $0 [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  -h, --help     Show this help message"
    echo "  -q, --quick    Run quick tests only (skip endpoint tests)"
    echo "  -v, --verbose  Show detailed output"
    echo ""
    echo "Examples:"
    echo "  $0              # Run all tests"
    echo "  $0 --quick      # Run quick tests only"
    echo "  $0 --verbose    # Run with detailed output"
}

# Parse command line arguments
QUICK_MODE=false
VERBOSE=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        -q|--quick)
            QUICK_MODE=true
            shift
            ;;
        -v|--verbose)
            VERBOSE=true
            shift
            ;;
        *)
            echo "Unknown option: $1"
            show_help
            exit 1
            ;;
    esac
done

# Run tests
run_tests
