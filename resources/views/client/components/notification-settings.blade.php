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
                    <p class="text-sm text-gray-600">Terima notifikasi langsung ke perangkat Anda</p>
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
            this.checkStatus();
            this.loadPreferences();
            this.loadSubscriptionInfo();
            this.detectDevice();
        },
        
        checkStatus() {
            if ('Notification' in window) {
                this.status = Notification.permission;
            }
        },
        
        async enableNotifications() {
            if (window.subscribeToPushNotifications) {
                const success = await window.subscribeToPushNotifications();
                if (success) {
                    this.status = 'granted';
                    this.loadSubscriptionInfo();
                }
            }
        },
        
        async testNotification() {
            if (this.status === 'granted') {
                // Show browser notification
                new Notification('Test Notifikasi', {
                    body: 'Notifikasi berhasil! Anda akan menerima update seperti ini.',
                    icon: '/icons/icon-192x192.png',
                    badge: '/icons/badge-72x72.png',
                    vibrate: [200, 100, 200]
                });
            }
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
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    this.subscriptionCount = data.subscription_count;
                }
            } catch (error) {
                console.error('Error loading subscription info:', error);
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
