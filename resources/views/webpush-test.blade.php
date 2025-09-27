<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Web Push Notification Test - SI TEDUH</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .status-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            font-weight: 600;
            color: #495057;
        }

        .status-value {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-warning {
            background: #fff3cd;
            color: #856404;
        }

        .status-error {
            background: #f8d7da;
            color: #721c24;
        }

        .test-section {
            margin: 30px 0;
        }

        .test-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #1e7e34;
        }

        .btn-warning {
            background: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
        }

        .log-area {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .log-item {
            margin-bottom: 5px;
            padding: 2px 0;
        }

        .log-success {
            color: #28a745;
        }

        .log-error {
            color: #dc3545;
        }

        .log-info {
            color: #17a2b8;
        }

        .log-warning {
            color: #ffc107;
        }

        .hidden {
            display: none;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 600;
            color: #007bff;
        }

        .stat-label {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Web Push Notification Test</h1>
            <p>SI TEDUH - Testing Dashboard</p>
        </div>

        <!-- Status Check Section -->
        <div class="status-card">
            <h3>📊 Status Sistem</h3>
            <div class="status-item">
                <span class="status-label">Browser Support:</span>
                <span id="browser-support" class="status-value">Checking...</span>
            </div>
            <div class="status-item">
                <span class="status-label">Notification Permission:</span>
                <span id="notification-permission" class="status-value">Checking...</span>
            </div>
            <div class="status-item">
                <span class="status-label">Service Worker:</span>
                <span id="service-worker" class="status-value">Checking...</span>
            </div>
            <div class="status-item">
                <span class="status-label">Push Subscription:</span>
                <span id="push-subscription" class="status-value">Checking...</span>
            </div>
            <div class="status-item">
                <span class="status-label">VAPID Config:</span>
                <span id="vapid-config" class="status-value">Checking...</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="test-section">
            <div class="test-title">🚀 Quick Actions</div>
            <button class="btn btn-primary" onclick="requestPermission()">Request Permission</button>
            <button class="btn btn-success" onclick="testDirectNotification()">Test Direct Notification</button>
            <button class="btn btn-warning" onclick="testServiceWorkerNotification()">Test Service Worker</button>
            <button class="btn btn-info" onclick="refreshStatus()">Refresh Status</button>
            <button class="btn btn-danger" onclick="clearLogs()">Clear Logs</button>

            <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>📝 Penjelasan Testing:</strong><br>
                • <strong>Direct Notification:</strong> Test notifikasi langsung dari browser (tidak butuh
                subscription)<br>
                • <strong>Service Worker:</strong> Test notifikasi melalui service worker (tidak butuh subscription)<br>
                • <strong>Send Test Notification:</strong> Test notifikasi real-world dari server (butuh subscription
                valid)<br>
                <em>💡 Jika "Send Test Notification" gagal, klik "Request Permission" dulu untuk subscribe!</em>
            </div>
        </div>

        <!-- Test Notification Form -->
        <div class="test-section">
            <div class="test-title">📤 Test Push Notification</div>
            <div class="form-group">
                <label class="form-label">User ID:</label>
                <input type="number" id="test-user-id" class="form-control" value="1"
                    placeholder="Masukkan User ID">
            </div>
            <div class="form-group">
                <label class="form-label">Title:</label>
                <input type="text" id="test-title" class="form-control" value="Test Notification SI TEDUH"
                    placeholder="Judul notifikasi">
            </div>
            <div class="form-group">
                <label class="form-label">Body:</label>
                <input type="text" id="test-body" class="form-control"
                    value="Ini adalah test notification untuk memastikan sistem berfungsi dengan baik."
                    placeholder="Isi notifikasi">
            </div>
            <div class="form-group">
                <label class="form-label">URL:</label>
                <input type="text" id="test-url" class="form-control" value="/user"
                    placeholder="URL yang akan dibuka saat diklik">
            </div>
            <button class="btn btn-success" onclick="sendTestNotification()">Send Test Notification</button>
            <button class="btn btn-info" onclick="sendBroadcastTest()">Send Broadcast Test</button>
        </div>

        <!-- Statistics -->
        <div class="test-section">
            <div class="test-title">📈 Subscription Statistics</div>
            <button class="btn btn-info" onclick="loadStats()">Load Statistics</button>
            <div id="stats-container" class="stats-grid hidden"></div>
        </div>

        <!-- Logs -->
        <div class="test-section">
            <div class="test-title">📝 Logs</div>
            <div id="log-area" class="log-area">
                <div class="log-item log-info">[INFO] Web Push Test Dashboard loaded</div>
            </div>
        </div>
    </div>

    <!-- Include push notification script -->
    <script src="{{ asset('js/push-notification.js') }}"></script>

    <script>
        let logCount = 0;

        function log(message, type = 'info') {
            const logArea = document.getElementById('log-area');
            const timestamp = new Date().toLocaleTimeString();
            const logItem = document.createElement('div');
            logItem.className = `log-item log-${type}`;
            logItem.textContent = `[${timestamp}] [${type.toUpperCase()}] ${message}`;
            logArea.appendChild(logItem);
            logArea.scrollTop = logArea.scrollHeight;
            logCount++;

            if (logCount > 100) {
                logArea.removeChild(logArea.firstChild);
                logCount--;
            }
        }

        function clearLogs() {
            document.getElementById('log-area').innerHTML = '';
            logCount = 0;
            log('Logs cleared');
        }

        async function checkBrowserSupport() {
            const support = {
                serviceWorker: 'serviceWorker' in navigator,
                pushManager: 'PushManager' in window,
                notification: 'Notification' in window
            };

            const allSupported = support.serviceWorker && support.pushManager && support.notification;
            const statusElement = document.getElementById('browser-support');

            if (allSupported) {
                statusElement.textContent = 'Supported';
                statusElement.className = 'status-value status-success';
                log('Browser support: ✅ All features supported');
            } else {
                statusElement.textContent = 'Not Supported';
                statusElement.className = 'status-value status-error';
                log('Browser support: ❌ Missing features', 'error');
                log(`Service Worker: ${support.serviceWorker ? '✅' : '❌'}`, 'error');
                log(`Push Manager: ${support.pushManager ? '✅' : '❌'}`, 'error');
                log(`Notification: ${support.notification ? '✅' : '❌'}`, 'error');
            }
        }

        async function checkNotificationPermission() {
            const permission = Notification.permission;
            const statusElement = document.getElementById('notification-permission');

            statusElement.textContent = permission.charAt(0).toUpperCase() + permission.slice(1);

            switch (permission) {
                case 'granted':
                    statusElement.className = 'status-value status-success';
                    log('Notification permission: ✅ Granted');
                    break;
                case 'denied':
                    statusElement.className = 'status-value status-error';
                    log('Notification permission: ❌ Denied', 'error');
                    break;
                default:
                    statusElement.className = 'status-value status-warning';
                    log('Notification permission: ⚠️ Default (not requested yet)');
                    break;
            }
        }

        async function checkServiceWorker() {
            try {
                const registrations = await navigator.serviceWorker.getRegistrations();
                const statusElement = document.getElementById('service-worker');

                if (registrations.length > 0) {
                    statusElement.textContent = 'Registered';
                    statusElement.className = 'status-value status-success';
                    log(`Service Worker: ✅ ${registrations.length} registration(s) found`);
                } else {
                    statusElement.textContent = 'Not Registered';
                    statusElement.className = 'status-value status-warning';
                    log('Service Worker: ⚠️ No registrations found');
                }
            } catch (error) {
                const statusElement = document.getElementById('service-worker');
                statusElement.textContent = 'Error';
                statusElement.className = 'status-value status-error';
                log('Service Worker: ❌ Error checking registrations', 'error');
            }
        }

        async function checkPushSubscription() {
            try {
                if (window.pushHandler && window.pushHandler.swRegistration) {
                    const subscription = await window.pushHandler.swRegistration.pushManager.getSubscription();
                    const statusElement = document.getElementById('push-subscription');

                    if (subscription) {
                        statusElement.textContent = 'Subscribed';
                        statusElement.className = 'status-value status-success';
                        log('Push Subscription: ✅ Active subscription found');
                    } else {
                        statusElement.textContent = 'Not Subscribed';
                        statusElement.className = 'status-value status-warning';
                        log('Push Subscription: ⚠️ No active subscription');
                    }
                } else {
                    const statusElement = document.getElementById('push-subscription');
                    statusElement.textContent = 'Not Available';
                    statusElement.className = 'status-value status-error';
                    log('Push Subscription: ❌ Push handler not available');
                }
            } catch (error) {
                const statusElement = document.getElementById('push-subscription');
                statusElement.textContent = 'Error';
                statusElement.className = 'status-value status-error';
                log('Push Subscription: ❌ Error checking subscription', 'error');
            }
        }

        async function checkVapidConfig() {
            try {
                const response = await fetch('/api/webpush-test/vapid-config');
                const data = await response.json();
                const statusElement = document.getElementById('vapid-config');

                if (data.success) {
                    statusElement.textContent = 'Valid';
                    statusElement.className = 'status-value status-success';
                    log('VAPID Config: ✅ Valid configuration');
                } else {
                    statusElement.textContent = 'Invalid';
                    statusElement.className = 'status-value status-error';
                    log('VAPID Config: ❌ Invalid configuration', 'error');
                }
            } catch (error) {
                const statusElement = document.getElementById('vapid-config');
                statusElement.textContent = 'Error';
                statusElement.className = 'status-value status-error';
                log('VAPID Config: ❌ Error checking configuration', 'error');
            }
        }

        async function refreshStatus() {
            log('Refreshing system status...');
            await checkBrowserSupport();
            await checkNotificationPermission();
            await checkServiceWorker();
            await checkPushSubscription();
            await checkVapidConfig();
            log('Status refresh completed');
        }

        async function requestPermission() {
            try {
                log('Requesting notification permission...');
                const permission = await Notification.requestPermission();
                log(`Permission result: ${permission}`);
                await checkNotificationPermission();

                if (permission === 'granted' && window.pushHandler) {
                    await window.pushHandler.init();
                    log('Push notification initialized successfully');
                    await checkPushSubscription();
                }
            } catch (error) {
                log(`Error requesting permission: ${error.message}`, 'error');
            }
        }

        function testDirectNotification() {
            if (Notification.permission !== 'granted') {
                log('Permission not granted for direct notification', 'error');
                return;
            }

            try {
                const notification = new Notification('Test Direct Notification', {
                    body: 'Ini adalah test notification langsung dari browser',
                    icon: '/images/logo-puskesmas.png',
                    badge: '/images/logo-puskesmas.png'
                });

                notification.onshow = () => log('Direct notification shown successfully');
                notification.onerror = (e) => log(`Direct notification error: ${e}`, 'error');
                notification.onclick = () => {
                    log('Direct notification clicked');
                    notification.close();
                };
            } catch (error) {
                log(`Error creating direct notification: ${error.message}`, 'error');
            }
        }

        function testServiceWorkerNotification() {
            if (window.pushHandler) {
                window.pushHandler.testNotification();
                log('Service worker notification test triggered');
            } else {
                log('Push handler not available', 'error');
            }
        }

        async function sendTestNotification() {
            const userId = document.getElementById('test-user-id').value;
            const title = document.getElementById('test-title').value;
            const body = document.getElementById('test-body').value;
            const url = document.getElementById('test-url').value;

            try {
                log(`Sending test notification to user ${userId}...`);

                const response = await fetch('/api/webpush-test/send-notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        title: title,
                        body: body,
                        url: url
                    })
                });

                const data = await response.json();

                if (data.success) {
                    log(`✅ Test notification sent successfully`);
                    log(`User: ${data.data.user_name || 'Unknown'}`);
                    log(`Subscriptions: ${data.data.total_subscriptions}`);
                    log(`Success: ${data.data.success_count}, Errors: ${data.data.error_count}`);

                    // Cek jika ada error 410 (subscription expired)
                    if (data.data.errors && data.data.errors.length > 0) {
                        data.data.errors.forEach(error => {
                            if (error.error && error.error.includes('410')) {
                                log(`⚠️ Subscription expired (410 Gone) - User needs to subscribe again`,
                                    'warning');
                                log(`💡 Solution: Click 'Request Permission' button to get fresh subscription`,
                                    'info');
                            } else {
                                log(`❌ Error: ${error.error}`, 'error');
                            }
                        });
                    }

                    // Jika success_count > 0, notifikasi seharusnya muncul
                    if (data.data.success_count > 0) {
                        log(`🎉 Notification should appear on your device!`, 'success');
                    }
                } else {
                    log(`❌ Test notification failed: ${data.message}`, 'error');

                    // Berikan solusi jika user belum subscribe
                    if (data.message.includes('belum subscribe')) {
                        log(`💡 Solution: Click 'Request Permission' button first to subscribe`, 'info');
                        log(`💡 Or try 'Direct Notification' or 'Service Worker' buttons for local testing`, 'info');
                    }
                }
            } catch (error) {
                log(`Error sending test notification: ${error.message}`, 'error');
            }
        }

        async function sendBroadcastTest() {
            const title = document.getElementById('test-title').value;
            const body = document.getElementById('test-body').value;

            try {
                log('Sending broadcast test notification...');

                const response = await fetch('/api/webpush-test/send-broadcast', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        title: title,
                        body: body
                    })
                });

                const data = await response.json();

                if (data.success) {
                    log(`✅ Broadcast test sent successfully`);
                    log(`Users: ${data.data.total_users}`);
                } else {
                    log(`❌ Broadcast test failed: ${data.message}`, 'error');
                }
            } catch (error) {
                log(`Error sending broadcast test: ${error.message}`, 'error');
            }
        }

        async function loadStats() {
            try {
                log('Loading subscription statistics...');

                const response = await fetch('/api/webpush-test/stats');
                const data = await response.json();

                if (data.success) {
                    const container = document.getElementById('stats-container');
                    container.innerHTML = `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.total_subscriptions}</div>
                            <div class="stat-label">Total Subscriptions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">${data.data.total_users_with_subscriptions}</div>
                            <div class="stat-label">Users with Subscriptions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">${data.data.users_with_multiple_subscriptions}</div>
                            <div class="stat-label">Multiple Device Users</div>
                        </div>
                    `;
                    container.classList.remove('hidden');
                    log('Statistics loaded successfully');
                } else {
                    log(`Failed to load statistics: ${data.message}`, 'error');
                }
            } catch (error) {
                log(`Error loading statistics: ${error.message}`, 'error');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', async function() {
            log('Initializing Web Push Test Dashboard...');
            await refreshStatus();
            log('Dashboard initialized successfully');
        });
    </script>
</body>

</html>
