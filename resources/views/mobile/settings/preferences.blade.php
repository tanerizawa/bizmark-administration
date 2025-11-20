@extends('mobile.layouts.app')

@section('title', 'Preferensi')

@section('content')
<div class="pb-20">
    
    {{-- Header Info --}}
    <div class="bg-gradient-to-br from-[#0077b5] to-[#004d6d] rounded-2xl p-6 mb-4 text-white">
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
                        <input type="checkbox" id="pushNotifications" class="sr-only peer" checked onchange="updatePreference('push_notifications', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0077b5]"></div>
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
                        <input type="checkbox" id="emailNotifications" class="sr-only peer" checked onchange="updatePreference('email_notifications', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0077b5]"></div>
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
                        <input type="checkbox" id="soundVibration" class="sr-only peer" checked onchange="updatePreference('sound_vibration', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0077b5]"></div>
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
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0077b5]"></div>
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
                        <input type="checkbox" id="offlineMode" class="sr-only peer" checked onchange="updatePreference('offline_mode', this.checked)">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0077b5]"></div>
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
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0077b5]"></div>
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
async function updatePreference(key, value) {
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
            // Show success feedback
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.textContent = 'Preferensi disimpan';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
    } catch (error) {
        console.error('Error updating preference:', error);
        alert('Gagal menyimpan preferensi');
    }
}

async function clearCache() {
    if (!confirm('Hapus semua data cache? Data offline akan dihapus.')) return;
    
    try {
        if ('caches' in window) {
            const cacheNames = await caches.keys();
            await Promise.all(cacheNames.map(name => caches.delete(name)));
        }
        
        alert('Cache berhasil dihapus. Aplikasi akan dimuat ulang.');
        window.location.reload();
    } catch (error) {
        console.error('Error clearing cache:', error);
        alert('Gagal menghapus cache');
    }
}

// Load current preferences
document.addEventListener('DOMContentLoaded', () => {
    const preferences = @json(auth()->user()->preferences ?? []);
    
    if (preferences.push_notifications !== undefined) {
        document.getElementById('pushNotifications').checked = preferences.push_notifications;
    }
    if (preferences.email_notifications !== undefined) {
        document.getElementById('emailNotifications').checked = preferences.email_notifications;
    }
    if (preferences.sound_vibration !== undefined) {
        document.getElementById('soundVibration').checked = preferences.sound_vibration;
    }
    if (preferences.dark_mode !== undefined) {
        document.getElementById('darkMode').checked = preferences.dark_mode;
    }
    if (preferences.offline_mode !== undefined) {
        document.getElementById('offlineMode').checked = preferences.offline_mode;
    }
    if (preferences.developer_mode !== undefined) {
        document.getElementById('developerMode').checked = preferences.developer_mode;
    }
});
</script>
@endsection
