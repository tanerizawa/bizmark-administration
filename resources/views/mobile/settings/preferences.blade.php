@extends('mobile.layouts.app')

@section('title', 'Preferensi')

@section('content')
<div class="pb-20">
    
    {{-- Header Info --}}
    <div class="bg-gradient-to-br from-[#0A66C2] to-[#004182] rounded-2xl p-6 mb-4 text-white">
        <div class="flex items-center gap-3 mb-2">
            <i class="fas fa-sliders text-2xl"></i>
            <div>
                <h2 class="text-xl font-bold">Preferensi</h2>
                <p class="text-sm opacity-90">Sesuaikan pengalaman aplikasi Anda</p>
            </div>
        </div>
    </div>

    {{-- Notifications Section --}}
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 px-1">NOTIFIKASI</h3>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Push Notifications</div>
                        <div class="text-xs text-gray-500 mt-0.5">Terima notifikasi push dari aplikasi</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-3">
                        <input type="checkbox" id="pushNotifications" class="sr-only peer" onchange="updatePreference('push_notifications', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A66C2]"></div>
                    </label>
                </div>
            </div>
            
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Email Notifications</div>
                        <div class="text-xs text-gray-500 mt-0.5">Terima notifikasi via email</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-3">
                        <input type="checkbox" id="emailNotifications" class="sr-only peer" onchange="updatePreference('email_notifications', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A66C2]"></div>
                    </label>
                </div>
            </div>
            
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Sound & Vibration</div>
                        <div class="text-xs text-gray-500 mt-0.5">Suara dan getar untuk notifikasi</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-3">
                        <input type="checkbox" id="soundVibration" class="sr-only peer" onchange="updatePreference('sound_vibration', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A66C2]"></div>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Appearance Section --}}
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 px-1">TAMPILAN</h3>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Dark Mode</div>
                        <div class="text-xs text-gray-500 mt-0.5">Tema gelap untuk mata lebih nyaman</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-3">
                        <input type="checkbox" id="darkMode" class="sr-only peer" onchange="updatePreference('dark_mode', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A66C2]"></div>
                    </label>
                </div>
            </div>
            
            <div class="p-4">
                <div class="font-medium text-gray-900 mb-3">Bahasa / Language</div>
                <select class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-900" onchange="updatePreference('language', this.value)">
                    <option value="id" selected>Bahasa Indonesia</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Data & Storage --}}
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3 px-1">DATA & PENYIMPANAN</h3>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Offline Mode</div>
                        <div class="text-xs text-gray-500 mt-0.5">Simpan data untuk akses offline</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-3">
                        <input type="checkbox" id="offlineMode" class="sr-only peer" onchange="updatePreference('offline_mode', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A66C2]"></div>
                    </label>
                </div>
            </div>
            
            <button onclick="clearCache()" class="w-full p-4 text-left hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Hapus Cache</div>
                        <div class="text-xs text-gray-500 mt-0.5">Bersihkan data cache aplikasi</div>
                    </div>
                    <i class="fas fa-trash text-red-600"></i>
                </div>
            </button>
        </div>
    </div>

    {{-- Advanced --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-900 mb-3 px-1">LANJUTAN</h3>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">Developer Mode</div>
                        <div class="text-xs text-gray-500 mt-0.5">Tampilkan informasi debug</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-3">
                        <input type="checkbox" id="developerMode" class="sr-only peer" onchange="updatePreference('developer_mode', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0A66C2]"></div>
                    </label>
                </div>
            </div>
            
            <form action="{{ mobile_route('force-desktop') }}" method="POST" class="p-4">
                @csrf
                <button type="submit" class="w-full text-left">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Mode Desktop</div>
                            <div class="text-xs text-gray-500 mt-0.5">Paksa tampilan desktop</div>
                        </div>
                        <i class="fas fa-desktop text-gray-600"></i>
                    </div>
                </button>
            </form>
        </div>
    </div>

</div>

<script>
// VAPID public key from server
const VAPID_PUBLIC_KEY = '{{ config("webpush.vapid.public_key") }}';

async function updatePreference(key, value) {
    // Special handling for push notifications
    if (key === 'push_notifications') {
        if (value) {
            await subscribeToPush();
        } else {
            await unsubscribeFromPush();
        }
        return;
    }
    
    try {
        const response = await fetch('{{ mobile_route("profile.preferences") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ [key]: value })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Preferensi disimpan', 'success');
        }
    } catch (error) {
        console.error('Error updating preference:', error);
        showToast('Gagal menyimpan preferensi', 'error');
    }
}

async function subscribeToPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        showToast('Browser tidak mendukung push notification', 'error');
        document.getElementById('pushNotifications').checked = false;
        return;
    }
    
    try {
        // Request notification permission
        const permission = await Notification.requestPermission();
        
        if (permission !== 'granted') {
            showToast('Izin notifikasi ditolak', 'error');
            document.getElementById('pushNotifications').checked = false;
            return;
        }
        
        // Get service worker registration
        const registration = await navigator.serviceWorker.ready;
        
        // Subscribe to push
        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });
        
        // Send subscription to server
        const response = await fetch('{{ mobile_route("push.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(subscription.toJSON())
        });
        
        if (response.ok) {
            showToast('Push notification aktif', 'success');
            // Also save preference
            await savePreference('push_notifications', true);
        } else {
            throw new Error('Failed to save subscription');
        }
    } catch (error) {
        console.error('Push subscription error:', error);
        showToast('Gagal mengaktifkan push notification', 'error');
        document.getElementById('pushNotifications').checked = false;
    }
}

async function unsubscribeFromPush() {
    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        
        if (subscription) {
            await subscription.unsubscribe();
            
            // Notify server
            await fetch('{{ mobile_route("push.unsubscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ endpoint: subscription.endpoint })
            });
        }
        
        showToast('Push notification dinonaktifkan', 'success');
        await savePreference('push_notifications', false);
    } catch (error) {
        console.error('Push unsubscribe error:', error);
        showToast('Gagal menonaktifkan push notification', 'error');
    }
}

async function savePreference(key, value) {
    try {
        await fetch('{{ mobile_route("profile.preferences") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ [key]: value })
        });
    } catch (error) {
        console.error('Error saving preference:', error);
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-20 left-1/2 transform -translate-x-1/2 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} text-white px-4 py-2 rounded-lg shadow-lg z-50`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

async function clearCache() {
    if (!confirm('Hapus semua data cache? Data offline akan dihapus.')) return;
    
    try {
        if ('caches' in window) {
            const cacheNames = await caches.keys();
            await Promise.all(cacheNames.map(name => caches.delete(name)));
        }
        
        // Also tell service worker to clear cache
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });
        }
        
        showToast('Cache berhasil dihapus', 'success');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error clearing cache:', error);
        showToast('Gagal menghapus cache', 'error');
    }
}

// Helper function to convert VAPID key
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');
    
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Check current push subscription status on load
async function checkPushStatus() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        document.getElementById('pushNotifications').disabled = true;
        return;
    }
    
    try {
        const registration = await navigator.serviceWorker.ready;
        const subscription = await registration.pushManager.getSubscription();
        document.getElementById('pushNotifications').checked = !!subscription;
    } catch (error) {
        console.error('Error checking push status:', error);
    }
}

// Load current preferences
document.addEventListener('DOMContentLoaded', () => {
    const preferences = @json(auth()->user()->preferences ?? []);
    
    const defaultPrefs = {
        push_notifications: true,
        email_notifications: true,
        sound_vibration: true,
        dark_mode: false,
        offline_mode: true,
        developer_mode: false
    };

    const fields = {
        email_notifications: 'emailNotifications',
        sound_vibration: 'soundVibration',
        dark_mode: 'darkMode',
        offline_mode: 'offlineMode',
        developer_mode: 'developerMode'
    };

    for (const [key, elId] of Object.entries(fields)) {
        const el = document.getElementById(elId);
        if (el) {
            el.checked = preferences[key] !== undefined ? preferences[key] : defaultPrefs[key];
        }
    }
    
    // Check actual push subscription status
    checkPushStatus();
});
</script>
@endsection
