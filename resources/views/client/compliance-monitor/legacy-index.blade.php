@php
    $expiringCount = $monitors->where('status', 'expiring_soon')->count();
    $expiredCount  = $monitors->where('status', 'expired')->count();
    $activeCount   = $monitors->where('status', 'active')->count();
@endphp
<div class="px-4 py-6 max-w-2xl mx-auto"
     x-data="{
         filter: 'all',
         get filtered() {
             return this.filter === 'all' ? true : true; {{-- filtering done server-side via Alpine show below --}}
         }
     }">

    {{-- Hero --}}
    <div class="monitor-hero">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h1 class="font-bold text-lg mb-1">Compliance Monitor</h1>
                <p class="text-sm opacity-75">Pantau status izin aktif dan tanggal kedaluwarsa Anda.</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- CSV Export --}}
                <a href="{{ route('client.compliance-monitor.export') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white/20 hover:bg-white/30 text-white rounded-lg transition active:scale-95"
                   title="Export CSV">
                    <i class="fas fa-download text-[10px]"></i> CSV
                </a>
                {{-- Push Notification Subscribe --}}
                @if(isset($pushSubscribed) && !$pushSubscribed)
                <button type="button" id="btn-push-subscribe"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-white/20 hover:bg-white/30 text-white rounded-lg transition active:scale-95"
                        onclick="subscribePush()">
                    <i class="fas fa-bell text-[10px]"></i> Aktifkan Notifikasi
                </button>
                @endif
            </div>
        </div>
        <div class="stat-pills mt-3">
            <span class="stat-pill">
                <span class="dot" style="background:#22c55e"></span>
                Aktif: {{ $stats['active'] }}
            </span>
            <span class="stat-pill">
                <span class="dot" style="background:#f59e0b"></span>
                Segera Expire: {{ $stats['expiring_soon'] }}
            </span>
            <span class="stat-pill">
                <span class="dot" style="background:#ef4444"></span>
                Expired: {{ $stats['expired'] }}
            </span>
        </div>
    </div>

    {{-- Filter chips --}}
    @if($monitors->isNotEmpty())
    <div class="flex items-center gap-2 flex-wrap mb-4">
        <button type="button" @click="filter='all'"
                :class="filter==='all' ? 'bg-[#0a66c2] text-white border-[#0a66c2]' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-[#0a66c2]'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition">
            Semua
            <span class="text-[10px] font-bold opacity-70">{{ $monitors->count() }}</span>
        </button>
        <button type="button" @click="filter='expiring_soon'"
                :class="filter==='expiring_soon' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-amber-400'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition">
            <i class="fas fa-clock text-[10px]"></i> Mendekati Expired (30hr)
            @if($expiringCount > 0)<span class="text-[10px] font-bold opacity-80">{{ $expiringCount }}</span>@endif
        </button>
        <button type="button" @click="filter='expired'"
                :class="filter==='expired' ? 'bg-red-500 text-white border-red-500' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-red-400'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition">
            <i class="fas fa-triangle-exclamation text-[10px]"></i> Expired
            @if($expiredCount > 0)<span class="text-[10px] font-bold opacity-80">{{ $expiredCount }}</span>@endif
        </button>
        <button type="button" @click="filter='active'"
                :class="filter==='active' ? 'bg-green-600 text-white border-green-600' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-green-500'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold border rounded-full transition">
            <i class="fas fa-check text-[10px]"></i> Aktif
            @if($activeCount > 0)<span class="text-[10px] font-bold opacity-80">{{ $activeCount }}</span>@endif
        </button>
    </div>
    @endif

    {{-- Push notification success toast --}}
    <div x-data="{show: false}" x-show="show" x-transition
         x-init="document.addEventListener('push-subscribed', () => { show=true; setTimeout(()=>show=false,4000) })"
         class="mb-4 flex items-center gap-2 px-4 py-2.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-xs text-green-700 dark:text-green-400"
         style="display:none">
        <i class="fas fa-bell text-green-500"></i> Notifikasi izin aktif. Anda akan diberi tahu sebelum izin kedaluwarsa.
    </div>

    @if($monitors->isEmpty())
        @include('client.components.empty-state', [
            'icon'     => 'fa-shield-check',
            'title'    => 'Belum ada izin dipantau',
            'message'  => 'Hubungi tim Bizmark untuk mengaktifkan pemantauan izin Anda.',
            'size'     => 'md',
            'color'    => 'gray',
        ])
    @else
        {{-- No results for filter --}}
        <div x-show="filter !== 'all' && document.querySelectorAll('[data-status]').length > 0 && [...document.querySelectorAll('[data-status]')].every(el => el.style.display === 'none')"
             class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
            Tidak ada izin dengan status ini.
        </div>

        @foreach($monitors as $monitor)
        @php
            $days    = $monitor->daysUntilExpiry();
            $pct     = $monitor->progressPercent();
            $color   = match($monitor->status) {
                'expiring_soon' => '#f59e0b',
                'expired'       => '#ef4444',
                'renewed'       => '#6366f1',
                default         => '#22c55e',
            };
        @endphp
        <div class="permit-card status-{{ $monitor->status }}"
             data-status="{{ $monitor->status }}"
             x-show="filter === 'all' || filter === '{{ $monitor->status }}'">
            <div class="permit-header">
                <div>
                    <p class="permit-name">{{ $monitor->permit_type }}</p>
                    @if($monitor->permit_number)
                        <p class="permit-project">No: {{ $monitor->permit_number }}</p>
                    @endif
                    @if($monitor->project)
                        <p class="permit-project"><i class="fas fa-folder-open text-xs mr-1"></i>{{ $monitor->project->name }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="status-badge status-badge--{{ $monitor->status }}">
                        {{ match($monitor->status) {
                            'active'        => 'Aktif',
                            'expiring_soon' => 'Segera Expire',
                            'expired'       => 'Expired',
                            'renewed'       => 'Diperbarui',
                        } }}
                    </span>
                    {{-- Severity countdown badge --}}
                    @if($monitor->status === 'expiring_soon')
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">
                        <i class="fas fa-hourglass-half text-[8px]"></i>
                        @if($days <= 7) Kritis: {{ $days }}h lagi
                        @elseif($days <= 14) Waspada: {{ $days }}h lagi
                        @else {{ $days }} hari lagi
                        @endif
                    </span>
                    @elseif($monitor->status === 'expired')
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">
                        <i class="fas fa-circle-xmark text-[8px]"></i> {{ abs($days) }} hari lalu
                    </span>
                    @endif
                </div>
            </div>

            {{-- Severity timeline bar --}}
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill {{ $monitor->status === 'expiring_soon' && $days <= 7 ? 'animate-pulse' : '' }}"
                     style="width:{{ $pct }}%;background:{{ $color }}"></div>
            </div>
            <div class="expire-row">
                <span>Expire: <strong>{{ $monitor->expires_at->format('d M Y') }}</strong></span>
                <span class="{{ $monitor->status === 'expired' ? 'text-red-600 dark:text-red-400 font-semibold' : ($monitor->status === 'expiring_soon' ? 'text-amber-600 dark:text-amber-400 font-semibold' : '') }}">
                    @if($days > 0)
                        {{ $days }} hari lagi
                    @elseif($days === 0)
                        Hari ini!
                    @else
                        {{ abs($days) }} hari lalu
                    @endif
                </span>
            </div>

            @if(in_array($monitor->status, ['expiring_soon', 'expired']))
                @php
                    $waBase = data_get(config('landing_metrics'), 'contact.whatsapp_link', 'https://wa.me/6283879602855');
                    $waText = 'Halo, saya ingin perpanjang izin "' . $monitor->permit_type . '" yang akan expire ' . $monitor->expires_at->format('d M Y') . '.';
                    $waHref = $waBase . '?text=' . rawurlencode($waText);
                @endphp
                <a href="{{ $waHref }}" target="_blank" rel="noopener" class="renewal-cta">
                    <i class="fab fa-whatsapp"></i> Hubungi Bizmark untuk Renewal
                </a>
            @endif
        </div>
        @endforeach
    @endif

</div>

<script>
async function subscribePush() {
    try {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Browser Anda tidak mendukung push notification.');
            return;
        }
        const perm = await Notification.requestPermission();
        if (perm !== 'granted') return;

        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: '{{ config("webpush.vapid.public_key") }}'
        });

        await window.apiFetch('{{ route("api.client.push.subscribe") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(sub.toJSON())
        });

        document.getElementById('btn-push-subscribe')?.remove();
        document.dispatchEvent(new Event('push-subscribed'));
    } catch (e) {
        console.error('Push subscription failed:', e);
    }
}
</script>
