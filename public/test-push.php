<!DOCTYPE html>
<html>
<head>
    <title>Push Notification Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary { background: #4F46E5; color: white; }
        .btn-success { background: #10B981; color: white; }
        .btn-danger { background: #EF4444; color: white; }
        .status {
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .status.success { background: #D1FAE5; color: #065F46; }
        .status.error { background: #FEE2E2; color: #991B1B; }
        .status.info { background: #DBEAFE; color: #1E40AF; }
        pre {
            background: #1F2937;
            color: #F3F4F6;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>🔔 Push Notification Test Tool</h1>
    
    <div class="card">
        <h2>1. VAPID Configuration</h2>
        <div id="vapid-status"></div>
        <pre id="vapid-key"></pre>
    </div>
    
    <div class="card">
        <h2>2. Service Worker Status</h2>
        <div id="sw-status"></div>
        <button class="btn-primary" onclick="registerServiceWorker()">Register Service Worker</button>
    </div>
    
    <div class="card">
        <h2>3. Notification Permission</h2>
        <div id="permission-status"></div>
        <button class="btn-primary" onclick="requestPermission()">Request Permission</button>
    </div>
    
    <div class="card">
        <h2>4. Push Subscription</h2>
        <div id="subscription-status"></div>
        <button class="btn-success" onclick="subscribe()">Subscribe to Push</button>
        <button class="btn-danger" onclick="unsubscribe()">Unsubscribe</button>
        <pre id="subscription-details"></pre>
    </div>
    
    <div class="card">
        <h2>5. Test Notification</h2>
        <button class="btn-primary" onclick="testLocalNotification()">Test Local Notification</button>
        <button class="btn-success" onclick="testServerNotification()">Test Server Push (API)</button>
    </div>
    
    <div class="card">
        <h2>Console Logs</h2>
        <pre id="console-log"></pre>
    </div>
    
    <script>
        const VAPID_PUBLIC_KEY = '<?php echo env("VAPID_PUBLIC_KEY"); ?>';
        const API_BASE = '/api/client/push';
        const CSRF_TOKEN = '{{ csrf_token() }}';
        
        let logs = [];
        
        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logMessage = `[${timestamp}] ${message}`;
            logs.push(logMessage);
            document.getElementById('console-log').textContent = logs.slice(-20).join('\n');
            console.log(logMessage);
        }
        
        function showStatus(elementId, message, type = 'info') {
            const el = document.getElementById(elementId);
            el.innerHTML = `<div class="status ${type}">${message}</div>`;
        }
        
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            
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
                log('VAPID key found: ' + VAPID_PUBLIC_KEY.substring(0, 20) + '...');
            } else {
                showStatus('vapid-status', '❌ VAPID Public Key NOT configured', 'error');
                log('ERROR: VAPID key not found!', 'error');
            }
        }
        
        async function checkServiceWorker() {
            if ('serviceWorker' in navigator) {
                try {
                    const registration = await navigator.serviceWorker.getRegistration();
                    if (registration) {
                        showStatus('sw-status', `✅ Service Worker active: ${registration.active?.scriptURL || 'unknown'}`, 'success');
                        log('Service Worker registered');
                    } else {
                        showStatus('sw-status', '⚠️ Service Worker not registered', 'error');
                        log('Service Worker NOT registered');
                    }
                } catch (error) {
                    showStatus('sw-status', '❌ Error: ' + error.message, 'error');
                    log('ERROR checking SW: ' + error.message);
                }
            } else {
                showStatus('sw-status', '❌ Service Worker not supported', 'error');
                log('Service Worker not supported in this browser');
            }
        }
        
        async function registerServiceWorker() {
            try {
                log('Registering service worker...');
                const registration = await navigator.serviceWorker.register('/sw.js');
                await navigator.serviceWorker.ready;
                showStatus('sw-status', '✅ Service Worker registered successfully!', 'success');
                log('Service Worker registered: ' + registration.scope);
            } catch (error) {
                showStatus('sw-status', '❌ Failed to register: ' + error.message, 'error');
                log('ERROR registering SW: ' + error.message);
            }
        }
        
        async function checkPermission() {
            const permission = Notification.permission;
            const statusMap = {
                'granted': { msg: '✅ Notification permission GRANTED', type: 'success' },
                'denied': { msg: '❌ Notification permission DENIED', type: 'error' },
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
                await checkPermission();
                log('Permission result: ' + permission);
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
            const subscription = await getSubscription();
            if (subscription) {
                showStatus('subscription-status', '✅ Push subscription active', 'success');
                document.getElementById('subscription-details').textContent = JSON.stringify(subscription.toJSON(), null, 2);
                log('Subscription found');
            } else {
                showStatus('subscription-status', '⚠️ Not subscribed to push notifications', 'info');
                document.getElementById('subscription-details').textContent = 'No active subscription';
                log('No subscription found');
            }
        }
        
        async function subscribe() {
            try {
                log('Starting subscription process...');
                
                // Check permission
                if (Notification.permission !== 'granted') {
                    showStatus('subscription-status', '❌ Please grant notification permission first!', 'error');
                    return;
                }
                
                // Get service worker
                const registration = await navigator.serviceWorker.ready;
                log('Service Worker ready');
                
                // Subscribe
                log('Subscribing to push manager...');
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                });
                
                log('Push Manager subscription created');
                log('Endpoint: ' + subscription.endpoint.substring(0, 50) + '...');
                
                // Send to server
                log('Sending subscription to server...');
                const response = await fetch(API_BASE + '/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(subscription.toJSON())
                });
                
                const result = await response.json();
                log('Server response: ' + JSON.stringify(result));
                
                if (result.success) {
                    showStatus('subscription-status', '✅ Successfully subscribed!', 'success');
                    await checkSubscription();
                } else {
                    showStatus('subscription-status', '❌ Server error: ' + result.message, 'error');
                }
                
            } catch (error) {
                showStatus('subscription-status', '❌ Error: ' + error.message, 'error');
                log('ERROR subscribing: ' + error.message);
                console.error(error);
            }
        }
        
        async function unsubscribe() {
            try {
                log('Unsubscribing...');
                const subscription = await getSubscription();
                if (subscription) {
                    await subscription.unsubscribe();
                    showStatus('subscription-status', '✅ Unsubscribed successfully', 'success');
                    log('Unsubscribed from push');
                    await checkSubscription();
                } else {
                    showStatus('subscription-status', '⚠️ No active subscription', 'info');
                }
            } catch (error) {
                showStatus('subscription-status', '❌ Error: ' + error.message, 'error');
                log('ERROR unsubscribing: ' + error.message);
            }
        }
        
        async function testLocalNotification() {
            try {
                log('Testing local notification...');
                const registration = await navigator.serviceWorker.ready;
                await registration.showNotification('🔔 Test Notification', {
                    body: 'This is a local test notification',
                    icon: '/favicon.ico',
                    vibrate: [200, 100, 200]
                });
                log('Local notification sent!');
            } catch (error) {
                log('ERROR: ' + error.message);
            }
        }
        
        async function testServerNotification() {
            try {
                log('Sending test request to server...');
                const response = await fetch(API_BASE + '/test', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });
                
                const result = await response.json();
                log('Server response: ' + JSON.stringify(result));
                
                if (result.success) {
                    alert('✅ Test notification sent! Check your notification bar.');
                } else {
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                log('ERROR: ' + error.message);
                alert('❌ Request failed: ' + error.message);
            }
        }
        
        // Initialize
        (async () => {
            await checkVAPID();
            await checkServiceWorker();
            await checkPermission();
            await checkSubscription();
            log('=== Initialization complete ===');
        })();
    </script>
</body>
</html>
