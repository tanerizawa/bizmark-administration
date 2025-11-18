<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Push Notification Test Tool</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">🔔 Push Notification Test Tool</h1>
        
        <!-- VAPID Config -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-4">
            <h2 class="text-xl font-semibold mb-4">1. VAPID Configuration</h2>
            <div id="vapid-status"></div>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded mt-2 text-xs overflow-x-auto" id="vapid-key"></pre>
        </div>
        
        <!-- Service Worker -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-4">
            <h2 class="text-xl font-semibold mb-4">2. Service Worker Status</h2>
            <div id="sw-status" class="mb-4"></div>
            <button onclick="registerServiceWorker()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Register Service Worker
            </button>
        </div>
        
        <!-- Permission -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-4">
            <h2 class="text-xl font-semibold mb-4">3. Notification Permission</h2>
            <div id="permission-status" class="mb-4"></div>
            <button onclick="requestPermission()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Request Permission
            </button>
        </div>
        
        <!-- Subscription -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-4">
            <h2 class="text-xl font-semibold mb-4">4. Push Subscription</h2>
            <div id="subscription-status" class="mb-4"></div>
            <div class="flex gap-2 mb-4">
                <button onclick="subscribe()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-bell mr-2"></i>Subscribe to Push
                </button>
                <button onclick="unsubscribe()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    <i class="fas fa-bell-slash mr-2"></i>Unsubscribe
                </button>
                <button onclick="checkSubscription()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    <i class="fas fa-sync mr-2"></i>Refresh Status
                </button>
            </div>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded text-xs overflow-x-auto" id="subscription-details">Loading...</pre>
        </div>
        
        <!-- Test Notifications -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-4">
            <h2 class="text-xl font-semibold mb-4">5. Test Notifications</h2>
            <div class="flex gap-2">
                <button onclick="testLocalNotification()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                    <i class="fas fa-desktop mr-2"></i>Test Local Notification
                </button>
                <button onclick="testServerNotification()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    <i class="fas fa-server mr-2"></i>Test Server Push (API)
                </button>
            </div>
        </div>
        
        <!-- Console Logs -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Console Logs</h2>
            <pre class="bg-gray-900 text-green-400 p-4 rounded text-xs overflow-x-auto h-64 overflow-y-auto" id="console-log">Initializing...</pre>
        </div>
    </div>
    
    <script>
        const VAPID_PUBLIC_KEY = '{{ config('webpush.vapid.public_key') }}';
        const API_BASE = '/api/client/push';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        
        let logs = [];
        
        function log(message) {
            const timestamp = new Date().toLocaleTimeString();
            const logMessage = `[${timestamp}] ${message}`;
            logs.push(logMessage);
            document.getElementById('console-log').textContent = logs.slice(-50).join('\n');
            console.log(logMessage);
        }
        
        function showStatus(elementId, message, type = 'info') {
            const colors = {
                success: 'bg-green-100 text-green-800 border-green-200',
                error: 'bg-red-100 text-red-800 border-red-200',
                info: 'bg-blue-100 text-blue-800 border-blue-200',
                warning: 'bg-yellow-100 text-yellow-800 border-yellow-200'
            };
            const el = document.getElementById(elementId);
            el.innerHTML = `<div class="p-3 rounded border ${colors[type]}">${message}</div>`;
        }
        
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        
        async function checkVAPID() {
            if (VAPID_PUBLIC_KEY && VAPID_PUBLIC_KEY.length > 0) {
                showStatus('vapid-status', '✅ VAPID Public Key configured', 'success');
                document.getElementById('vapid-key').textContent = VAPID_PUBLIC_KEY;
                log('VAPID key found: ' + VAPID_PUBLIC_KEY.substring(0, 30) + '...');
            } else {
                showStatus('vapid-status', '❌ VAPID Public Key NOT configured!', 'error');
                log('ERROR: VAPID key not found!');
            }
        }
        
        async function checkServiceWorker() {
            if ('serviceWorker' in navigator) {
                try {
                    const registration = await navigator.serviceWorker.getRegistration();
                    if (registration) {
                        const sw = registration.active || registration.installing || registration.waiting;
                        showStatus('sw-status', `✅ Service Worker active: ${sw?.scriptURL || 'unknown'}`, 'success');
                        log('Service Worker registered at: ' + (sw?.scriptURL || 'unknown'));
                    } else {
                        showStatus('sw-status', '⚠️ Service Worker not registered yet', 'warning');
                        log('Service Worker NOT registered');
                    }
                } catch (error) {
                    showStatus('sw-status', '❌ Error: ' + error.message, 'error');
                    log('ERROR checking SW: ' + error.message);
                }
            } else {
                showStatus('sw-status', '❌ Service Worker not supported in this browser', 'error');
                log('Service Worker not supported');
            }
        }
        
        async function registerServiceWorker() {
            try {
                log('Registering service worker from /sw.js...');
                const registration = await navigator.serviceWorker.register('/sw.js');
                log('Waiting for service worker to be ready...');
                await navigator.serviceWorker.ready;
                showStatus('sw-status', '✅ Service Worker registered successfully!', 'success');
                log('Service Worker registered! Scope: ' + registration.scope);
            } catch (error) {
                showStatus('sw-status', '❌ Failed to register: ' + error.message, 'error');
                log('ERROR registering SW: ' + error.message);
                console.error(error);
            }
        }
        
        async function checkPermission() {
            const permission = Notification.permission;
            const statusMap = {
                'granted': { msg: '✅ Notification permission GRANTED', type: 'success' },
                'denied': { msg: '❌ Notification permission DENIED (check browser settings)', type: 'error' },
                'default': { msg: '⚠️ Notification permission not requested yet', type: 'info' }
            };
            const status = statusMap[permission];
            showStatus('permission-status', status.msg, status.type);
            log('Notification permission: ' + permission);
        }
        
        async function requestPermission() {
            try {
                log('Requesting notification permission...');
                const permission = await Notification.requestPermission();
                log('Permission result: ' + permission);
                await checkPermission();
            } catch (error) {
                showStatus('permission-status', '❌ Error: ' + error.message, 'error');
                log('ERROR requesting permission: ' + error.message);
            }
        }
        
        async function getSubscription() {
            try {
                const registration = await navigator.serviceWorker.ready;
                return await registration.pushManager.getSubscription();
            } catch (error) {
                log('ERROR getting subscription: ' + error.message);
                return null;
            }
        }
        
        async function checkSubscription() {
            log('Checking subscription status...');
            const subscription = await getSubscription();
            if (subscription) {
                showStatus('subscription-status', '✅ Push subscription ACTIVE', 'success');
                document.getElementById('subscription-details').textContent = JSON.stringify(subscription.toJSON(), null, 2);
                log('Subscription found! Endpoint: ' + subscription.endpoint.substring(0, 50) + '...');
            } else {
                showStatus('subscription-status', '⚠️ NOT subscribed to push notifications', 'warning');
                document.getElementById('subscription-details').textContent = 'No active subscription.\nClick "Subscribe to Push" button above.';
                log('No subscription found');
            }
        }
        
        async function subscribe() {
            try {
                log('=== STARTING SUBSCRIPTION PROCESS ===');
                
                // 1. Check permission
                if (Notification.permission !== 'granted') {
                    showStatus('subscription-status', '❌ Please grant notification permission first!', 'error');
                    log('ERROR: Permission not granted');
                    return;
                }
                log('✓ Permission granted');
                
                // 2. Get service worker
                log('Getting service worker registration...');
                const registration = await navigator.serviceWorker.ready;
                log('✓ Service Worker ready: ' + registration.scope);
                
                // 3. Check existing subscription
                let subscription = await registration.pushManager.getSubscription();
                if (subscription) {
                    log('! Already subscribed, unsubscribing first...');
                    await subscription.unsubscribe();
                    log('✓ Unsubscribed from old subscription');
                }
                
                // 4. Subscribe to push
                log('Subscribing to push manager with VAPID key...');
                log('VAPID Key: ' + VAPID_PUBLIC_KEY.substring(0, 30) + '...');
                
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                });
                
                log('✓ Push Manager subscription created!');
                log('Endpoint: ' + subscription.endpoint.substring(0, 70) + '...');
                
                const subscriptionJSON = subscription.toJSON();
                log('Keys.p256dh: ' + subscriptionJSON.keys.p256dh.substring(0, 30) + '...');
                log('Keys.auth: ' + subscriptionJSON.keys.auth.substring(0, 30) + '...');
                
                // 5. Send to server
                log('Sending subscription to server API...');
                log('API endpoint: ' + API_BASE + '/subscribe');
                
                const response = await fetch(API_BASE + '/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(subscriptionJSON)
                });
                
                log('Server response status: ' + response.status);
                
                const result = await response.json();
                log('Server response: ' + JSON.stringify(result));
                
                if (result.success) {
                    showStatus('subscription-status', '✅ Successfully subscribed to push notifications!', 'success');
                    log('=== SUBSCRIPTION SUCCESS ===');
                    await checkSubscription();
                } else {
                    showStatus('subscription-status', '❌ Server error: ' + result.message, 'error');
                    log('ERROR from server: ' + result.message);
                }
                
            } catch (error) {
                showStatus('subscription-status', '❌ Error: ' + error.message, 'error');
                log('=== SUBSCRIPTION FAILED ===');
                log('ERROR: ' + error.message);
                log('Stack: ' + error.stack);
                console.error(error);
            }
        }
        
        async function unsubscribe() {
            try {
                log('Unsubscribing from push...');
                const subscription = await getSubscription();
                if (subscription) {
                    await subscription.unsubscribe();
                    showStatus('subscription-status', '✅ Unsubscribed successfully', 'success');
                    log('Unsubscribed from push');
                    await checkSubscription();
                } else {
                    showStatus('subscription-status', '⚠️ No active subscription to unsubscribe', 'info');
                    log('No subscription to unsubscribe');
                }
            } catch (error) {
                showStatus('subscription-status', '❌ Error: ' + error.message, 'error');
                log('ERROR unsubscribing: ' + error.message);
            }
        }
        
        async function testLocalNotification() {
            try {
                log('Testing local notification via Service Worker...');
                const registration = await navigator.serviceWorker.ready;
                await registration.showNotification('🔔 Test Local Notification', {
                    body: 'This is a local test notification from Service Worker',
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    vibrate: [200, 100, 200, 100, 200],
                    requireInteraction: true
                });
                log('✅ Local notification displayed!');
                alert('✅ Local notification sent! Check your notification bar.');
            } catch (error) {
                log('ERROR: ' + error.message);
                alert('❌ Error: ' + error.message);
            }
        }
        
        async function testServerNotification() {
            try {
                log('Sending test notification request to server API...');
                log('API endpoint: ' + API_BASE + '/test');
                
                const response = await fetch(API_BASE + '/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });
                
                log('Server response status: ' + response.status);
                
                const result = await response.json();
                log('Server response: ' + JSON.stringify(result));
                
                if (result.success) {
                    log('✅ Test notification sent to ' + result.devices + ' device(s)');
                    alert(`✅ Test notification sent to ${result.devices} device(s)!\nCheck your notification bar in a few seconds.`);
                } else {
                    log('ERROR: ' + result.message);
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                log('ERROR: ' + error.message);
                alert('❌ Request failed: ' + error.message);
                console.error(error);
            }
        }
        
        // Initialize on page load
        (async () => {
            log('=== Push Notification Test Tool Initialized ===');
            await checkVAPID();
            await checkServiceWorker();
            await checkPermission();
            await checkSubscription();
            log('=== Initialization complete ===');
            log('');
            log('📋 Instructions:');
            log('1. Click "Request Permission" if not granted yet');
            log('2. Make sure Service Worker is registered');
            log('3. Click "Subscribe to Push" to register device');
            log('4. Click "Test Server Push" to test notification');
            log('');
        })();
    </script>
</body>
</html>
