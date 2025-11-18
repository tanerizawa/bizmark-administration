<!-- Notification Settings Page Component -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-bell text-2xl text-indigo-600"></i>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-900">Pengaturan Notifikasi</h3>
            <p class="text-sm text-gray-600">Kelola notifikasi push yang Anda terima</p>
        </div>
    </div>
    
    <div x-data="notificationSettings()" class="space-y-6">
        <!-- Push Notification Status -->
        <div class="border-b border-gray-200 pb-6">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h4 class="font-semibold text-gray-900">Push Notifications</h4>
                    <p class="text-sm text-gray-600">Terima notifikasi langsung ke perangkat Anda (muncul di notification bar)</p>
                </div>
                <div>
                    <span x-show="status === 'granted'" class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                        <i class="fas fa-check-circle"></i> Aktif
                    </span>
                    <span x-show="status === 'denied'" class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                        <i class="fas fa-times-circle"></i> Diblokir
                    </span>
                    <span x-show="status === 'default'" class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                        <i class="fas fa-bell-slash"></i> Tidak Aktif
                    </span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="mt-4">
                <button 
                    x-show="status !== 'granted'" 
                    @click="enableNotifications()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                    <i class="fas fa-bell mr-2"></i>Aktifkan Notifikasi
                </button>
                
                <button 
                    x-show="status === 'granted'" 
                    @click="testNotification()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                    <i class="fas fa-paper-plane mr-2"></i>Test Notifikasi
                </button>
                
                <!-- Info tentang notifikasi -->
                <div x-show="status === 'granted'" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex gap-2">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Notifikasi akan muncul di:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li><strong>Desktop:</strong> Notification center (pojok kanan bawah)</li>
                                <li><strong>Android:</strong> Notification bar (geser dari atas)</li>
                                <li><strong>iOS:</strong> Notification center (geser dari atas)</li>
                            </ul>
                            <p class="mt-2 text-xs">💡 Notifikasi tetap muncul meskipun browser tidak terbuka</p>
                        </div>
                    </div>
                </div>
                
                <div x-show="status === 'denied'" class="text-sm text-gray-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Notifikasi diblokir oleh browser. Silakan aktifkan di pengaturan browser Anda.
                </div>
            </div>
        </div>
        
        <!-- Notification Types (only show if enabled) -->
        <div x-show="status === 'granted'">
            <h4 class="font-semibold text-gray-900 mb-4">Jenis Notifikasi</h4>
            
            <div class="space-y-3">
                <!-- Status Updates -->
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-signature text-xl text-indigo-600"></i>
                        <div>
                            <p class="font-medium text-gray-900">Update Status Izin</p>
                            <p class="text-sm text-gray-600">Perubahan status permohonan izin</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="preferences.status_updates" @change="savePreferences()" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
                
                <!-- Document Requests -->
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-alt text-xl text-amber-600"></i>
                        <div>
                            <p class="font-medium text-gray-900">Permintaan Dokumen</p>
                            <p class="text-sm text-gray-600">Notifikasi dokumen yang dibutuhkan</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="preferences.document_requests" @change="savePreferences()" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
                
                <!-- Deadline Reminders -->
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-clock text-xl text-red-600"></i>
                        <div>
                            <p class="font-medium text-gray-900">Pengingat Deadline</p>
                            <p class="text-sm text-gray-600">Reminder tenggat waktu penting</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="preferences.deadline_reminders" @change="savePreferences()" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
                
                <!-- Messages -->
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-comment text-xl text-blue-600"></i>
                        <div>
                            <p class="font-medium text-gray-900">Pesan Baru</p>
                            <p class="text-sm text-gray-600">Pesan dari admin atau tim</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="preferences.messages" @change="savePreferences()" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Subscription Info -->
        <div x-show="status === 'granted'" class="bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-900 mb-2 text-sm">Informasi Langganan</h4>
            <div class="space-y-1 text-sm text-gray-600">
                <p><span class="font-medium">Perangkat:</span> <span x-text="deviceInfo"></span></p>
                <p><span class="font-medium">Langganan:</span> <span x-text="subscriptionCount"></span> perangkat</p>
                <p><span class="font-medium">Status:</span> <span class="text-green-600 font-medium">Aktif</span></p>
            </div>
        </div>
    </div>
</div>

<script>
function notificationSettings() {
    return {
        status: 'default',
        subscriptionCount: 0,
        deviceInfo: 'Loading...',
        preferences: {
            status_updates: true,
            document_requests: true,
            deadline_reminders: true,
            messages: true
        },
        
        async init() {
            console.log('[Notification Settings] Initializing...');
            this.checkStatus();
            this.loadPreferences();
            this.detectDevice();
            
            // Load subscription info if notifications are enabled
            if (this.status === 'granted') {
                this.loadSubscriptionInfo();
            }
            
            console.log('[Notification Settings] Status:', this.status);
        },
        
        checkStatus() {
            if ('Notification' in window) {
                this.status = Notification.permission;
                console.log('[Notification Settings] Permission:', Notification.permission);
            } else {
                console.warn('[Notification Settings] Notifications not supported');
                this.status = 'unsupported';
            }
        },
        
        async enableNotifications() {
            try {
                // Check if notifications are supported
                if (!('Notification' in window)) {
                    this.showToast('Browser Anda tidak mendukung notifikasi push.', 'error');
                    return;
                }
                
                // Check if already granted
                if (Notification.permission === 'granted') {
                    this.status = 'granted';
                    this.showToast('Notifikasi sudah aktif!', 'success');
                    this.loadSubscriptionInfo();
                    return;
                }
                
                // Check if denied
                if (Notification.permission === 'denied') {
                    this.showToast('Notifikasi diblokir. Silakan aktifkan di pengaturan browser.', 'error');
                    return;
                }
                
                // Show loading state
                const button = event.target;
                const originalHTML = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
                
                // Request permission
                const permission = await Notification.requestPermission();
                
                if (permission === 'granted') {
                    this.status = 'granted';
                    this.showToast('Notifikasi berhasil diaktifkan!', 'success');
                    
                    // Try to subscribe to push notifications if available
                    if (window.subscribeToPushNotifications) {
                        try {
                            await window.subscribeToPushNotifications();
                            this.loadSubscriptionInfo();
                        } catch (subscribeError) {
                            console.warn('Push subscription failed:', subscribeError);
                            // Still okay, basic notifications work
                        }
                    }
                    
                    // Send test notification
                    setTimeout(() => {
                        this.testNotification();
                    }, 500);
                    
                } else if (permission === 'denied') {
                    this.status = 'denied';
                    this.showToast('Notifikasi ditolak. Aktifkan di pengaturan browser jika Anda berubah pikiran.', 'error');
                } else {
                    this.status = 'default';
                    this.showToast('Permintaan notifikasi dibatalkan.', 'info');
                }
                
                // Restore button
                button.disabled = false;
                button.innerHTML = originalHTML;
                
            } catch (error) {
                console.error('Error enabling notifications:', error);
                this.showToast('Gagal mengaktifkan notifikasi: ' + error.message, 'error');
            }
        },
        
        async testNotification() {
            try {
                console.log('[Notification Test] Starting test notification...');
                
                // Check if notifications are supported
                if (!('Notification' in window)) {
                    this.showToast('Browser Anda tidak mendukung notifikasi push.', 'error');
                    return;
                }
                
                // Check permission status
                if (this.status !== 'granted') {
                    this.showToast('Silakan aktifkan notifikasi terlebih dahulu.', 'warning');
                    return;
                }
                
                // Show loading state on button
                const button = event?.target || document.querySelector('[x-on\\:click="testNotification\\(\\)"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
                }
                
                // Method 1: Try server-side push notification (most reliable)
                try {
                    console.log('[Notification Test] Sending via server API...');
                    const response = await fetch('/api/client/push/test', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                    
                    // Check if response is OK (200-299)
                    if (response.ok) {
                        const result = await response.json();
                        console.log('[Notification Test] Server response:', result);
                        
                        if (result.success) {
                            this.showToast(`✅ Notifikasi push berhasil terkirim ke ${result.devices} perangkat!`, 'success');
                            
                            // Restore button
                            if (button) {
                                button.disabled = false;
                                button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                            }
                            return; // SUCCESS - stop here, don't continue to fallback
                        }
                    }
                    
                    // If we reach here, either response not OK or result.success = false
                    const result = await response.json();
                    console.warn('[Notification Test] Server returned error:', result);
                    throw new Error(result.message || 'Server error');
                    
                } catch (apiError) {
                    console.warn('[Notification Test] API method failed:', apiError);
                    
                    // If no subscription, try to subscribe first
                    if (apiError.message && apiError.message.includes('No push subscriptions')) {
                            this.showToast('Mendaftarkan perangkat untuk notifikasi push...', 'info');
                            
                            if (window.subscribeToPushNotifications) {
                                try {
                                    const subscribed = await window.subscribeToPushNotifications();
                                    if (subscribed) {
                                        console.log('[Notification Test] Subscription successful');
                                        
                                        // Update subscription info
                                        await this.loadSubscriptionInfo();
                                        
                                        // Wait a bit for subscription to be registered server-side
                                        await new Promise(resolve => setTimeout(resolve, 1500));
                                        
                                        // Retry test via API (will send REAL push notification)
                                        console.log('[Notification Test] Retrying after subscription...');
                                        try {
                                            const retryResponse = await fetch('/api/client/push/test', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                                    'Accept': 'application/json'
                                                }
                                            });
                                            
                                            const retryResult = await retryResponse.json();
                                            console.log('[Notification Test] Retry result:', retryResult);
                                            
                                            if (retryResult.success) {
                                                this.showToast(`✅ Notifikasi push terkirim ke ${retryResult.devices} perangkat! Cek notification bar.`, 'success');
                                                
                                                // Restore button
                                                if (button) {
                                                    button.disabled = false;
                                                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                                                }
                                                return;
                                            }
                                        } catch (retryError) {
                                            console.warn('[Notification Test] API retry failed:', retryError);
                                        }
                                        
                                        // If API retry fails, send notification directly via Service Worker
                                        console.log('[Notification Test] API retry failed, sending via SW...');
                                        
                                        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                                            try {
                                                const registration = await navigator.serviceWorker.ready;
                                                await registration.showNotification('🔔 Test Notifikasi Berhasil!', {
                                                    body: 'Perangkat Anda sudah terdaftar! Notifikasi akan muncul seperti ini.',
                                                    icon: '/favicon.ico',
                                                    badge: '/favicon.ico',
                                                    vibrate: [200, 100, 200, 100, 200],
                                                    tag: 'test-notification-' + Date.now(),
                                                    requireInteraction: true,
                                                    data: {
                                                        url: '/client/dashboard',
                                                        test: true
                                                    }
                                                });
                                                
                                                this.showToast('✅ Perangkat terdaftar! Notifikasi test terkirim ke notification bar.', 'success');
                                                
                                                // Restore button
                                                if (button) {
                                                    button.disabled = false;
                                                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                                                }
                                                return;
                                            } catch (swError) {
                                                console.error('[Notification Test] SW notification failed:', swError);
                                            }
                                        }
                                        
                                        // Last resort: direct browser notification
                                        console.log('[Notification Test] Sending via browser API...');
                                        const notification = new Notification('🔔 Test Notifikasi Berhasil!', {
                                            body: 'Perangkat Anda sudah terdaftar! Notifikasi akan muncul seperti ini.',
                                            icon: '/favicon.ico',
                                            vibrate: [200, 100, 200, 100, 200],
                                            tag: 'test-notification-' + Date.now()
                                        });
                                        
                                        notification.onclick = function(event) {
                                            event.preventDefault();
                                            window.focus();
                                            notification.close();
                                        };
                                        
                                        this.showToast('✅ Perangkat terdaftar! Cek notification bar untuk melihat notifikasi.', 'success');
                                        
                                        // Restore button
                                        if (button) {
                                            button.disabled = false;
                                            button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                                        }
                                        return;
                                        
                                    } else {
                                        this.showToast('Gagal berlangganan notifikasi. Coba lagi.', 'error');
                                        if (button) {
                                            button.disabled = false;
                                            button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                                        }
                                        return;
                                    }
                                } catch (subscribeError) {
                                    console.error('[Notification Test] Subscribe error:', subscribeError);
                                    // Continue to fallback
                                }
                            }
                    }
                    // If not subscription error, just continue to fallback
                }
                
                // Method 2: Try via service worker (for PWA)
                if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
                    try {
                        console.log('[Notification Test] Trying via Service Worker...');
                        const registration = await navigator.serviceWorker.ready;
                        await registration.showNotification('🔔 Test Notifikasi', {
                            body: 'Notifikasi push berfungsi dengan baik! Anda akan menerima update status izin seperti ini.',
                            icon: '/favicon.ico',
                            badge: '/favicon.ico',
                            vibrate: [200, 100, 200],
                            tag: 'test-notification-' + Date.now(),
                            requireInteraction: false,
                            data: {
                                url: '/client/dashboard',
                                test: true
                            }
                        });
                        
                        this.showToast('✅ Notifikasi push terkirim! Cek notification bar perangkat Anda.', 'success');
                        
                        // Restore button
                        if (button) {
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                        }
                        return;
                    } catch (swError) {
                        console.warn('[Notification Test] Service Worker method failed:', swError);
                    }
                }
                
                // Method 3: Fallback to direct browser notification
                console.log('[Notification Test] Trying direct browser notification...');
                const notification = new Notification('🔔 Test Notifikasi', {
                    body: 'Notifikasi push berfungsi dengan baik! Anda akan menerima update status izin seperti ini.',
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    vibrate: [200, 100, 200],
                    tag: 'test-notification-' + Date.now()
                });
                
                // Handle notification click
                notification.onclick = function(event) {
                    event.preventDefault();
                    window.focus();
                    notification.close();
                };
                
                // Show success message
                this.showToast('✅ Notifikasi push terkirim! Cek notification bar perangkat Anda.', 'success');
                
                // Restore button
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                }
                
            } catch (error) {
                console.error('[Notification Test] Error:', error);
                this.showToast('Gagal mengirim test notifikasi: ' + error.message, 'error');
                
                // Restore button
                const button = event?.target || document.querySelector('[x-on\\:click="testNotification\\(\\)"]');
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Test Notifikasi';
                }
            }
        },
        
        showToast(message, type = 'info') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-600 text-white' : 
                type === 'error' ? 'bg-red-600 text-white' : 
                'bg-blue-600 text-white'
            }`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        'fa-info-circle';
            
            toast.innerHTML = `
                <i class="fas ${icon} text-xl"></i>
                <p class="font-medium">${message}</p>
            `;
            
            document.body.appendChild(toast);
            
            // Slide in
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 10);
            
            // Auto-remove after 4 seconds
            setTimeout(() => {
                toast.style.transform = 'translateX(150%)';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 4000);
        },
        
        loadPreferences() {
            const saved = localStorage.getItem('notification-preferences');
            if (saved) {
                this.preferences = JSON.parse(saved);
            }
        },
        
        savePreferences() {
            localStorage.setItem('notification-preferences', JSON.stringify(this.preferences));
        },
        
        async loadSubscriptionInfo() {
            try {
                const response = await fetch('/api/client/push/status', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                console.log('[Notification Settings] Subscription info:', data);
                
                if (data.success) {
                    this.subscriptionCount = data.subscription_count || 0;
                } else {
                    console.warn('[Notification Settings] API returned success=false');
                    this.subscriptionCount = 0;
                }
            } catch (error) {
                console.error('[Notification Settings] Error loading subscription info:', error);
                this.subscriptionCount = 0;
                // Don't show error to user, it's not critical
            }
        },
        
        detectDevice() {
            const ua = navigator.userAgent;
            if (/android/i.test(ua)) {
                this.deviceInfo = 'Android';
            } else if (/iPad|iPhone|iPod/.test(ua)) {
                this.deviceInfo = 'iOS';
            } else if (/Windows/.test(ua)) {
                this.deviceInfo = 'Windows';
            } else if (/Mac/.test(ua)) {
                this.deviceInfo = 'Mac';
            } else {
                this.deviceInfo = 'Unknown';
            }
        }
    }
}
</script>
