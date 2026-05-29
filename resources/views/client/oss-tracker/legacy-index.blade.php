
{{-- Hero --}}
<div class="client-hero px-4 sm:px-6 py-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-1">
            <i class="fas fa-circle-check text-xl opacity-90"></i>
            <h1 class="text-2xl font-bold tracking-tight">OSS-RBA Status Tracker</h1>
        </div>
        <p class="text-white/80 text-sm">Pantau status permohonan OSS Anda secara otomatis — dicek setiap hari pukul 09.00 WIB.</p>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-5"
     x-data="{
         step: {{ $hasCredential ? 3 : 1 }},
         refreshing: false,
         elapsed: 0,
         lastRefreshedLabel: 'baru saja',
         init() {
             @if($hasCredential)
             this.startTimer();
             setInterval(() => this.autoRefresh(), 5 * 60 * 1000);
             @endif
         },
         startTimer() {
             setInterval(() => {
                 this.elapsed++;
                 if (this.elapsed < 60) this.lastRefreshedLabel = 'baru saja';
                 else if (this.elapsed < 3600) this.lastRefreshedLabel = Math.floor(this.elapsed / 60) + 'm yang lalu';
                 else this.lastRefreshedLabel = Math.floor(this.elapsed / 3600) + 'j yang lalu';
             }, 1000);
         },
         autoRefresh() {
             this.refreshing = true;
             window.location.reload();
         }
     }">

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300">
        <i class="fas fa-circle-check text-green-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('warning'))
    <div class="flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 text-sm text-amber-800 dark:text-amber-300">
        <i class="fas fa-triangle-exclamation text-amber-500"></i> {{ session('warning') }}
    </div>
    @endif

    {{-- ══ ONBOARDING WIZARD (no credential) ══ --}}
    @if(!$hasCredential)
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden">
        {{-- Wizard step header --}}
        <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border-b border-gray-200 dark:border-gray-700 px-5 py-4">
            <p class="text-xs font-bold text-[#0a66c2] uppercase tracking-wider mb-3">Pengaturan Awal OSS Tracker</p>
            <div class="flex items-center gap-0">
                @foreach(['Hubungkan', 'Verifikasi', 'Mulai Sync'] as $i => $label)
                <div class="flex items-center {{ $i < 2 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i === 0 ? 'bg-[#0a66c2] text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-400 dark:text-gray-400' }}">
                            @if($i === 0)<i class="fas fa-link text-[9px]"></i>@else{{ $i + 1 }}@endif
                        </div>
                        <span class="mt-1 text-[10px] font-medium {{ $i === 0 ? 'text-[#0a66c2]' : 'text-gray-400' }}">{{ $label }}</span>
                    </div>
                    @if($i < 2)
                    <div class="flex-1 h-0.5 mx-1 mt-[-12px] bg-gray-200 dark:bg-gray-600"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- Step 1: Connect form --}}
        <div class="px-5 py-5">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Hubungkan Akun OSS Anda</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Masukkan username dan password akun <strong>oss.go.id</strong> Anda. Kredensial disimpan terenkripsi.</p>
            <form method="POST" action="{{ route('client.oss-tracker.store-credential') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Username OSS <span class="text-red-500">*</span></label>
                    <input type="text" name="oss_username" autocomplete="off" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]"
                           placeholder="Username akun oss.go.id Anda">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password OSS <span class="text-red-500">*</span></label>
                    <input type="password" name="oss_password" autocomplete="new-password" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2.5 text-sm dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2] focus:border-[#0a66c2]">
                </div>
                <div class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900 rounded-xl px-3 py-2.5">
                    <i class="fas fa-lock text-[#0a66c2] text-xs flex-shrink-0"></i>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Kredensial disimpan terenkripsi end-to-end. Tim Bizmark tidak dapat melihat password Anda.</p>
                </div>
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-xl transition active:scale-95">
                    <i class="fas fa-link text-xs"></i> Hubungkan Akun OSS
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- ══ STATUS LIST (has credential) ══ --}}
    @if($hasCredential)

    {{-- Auto-refresh indicator --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <i class="fas fa-rotate text-[#0a66c2] text-[11px]"></i>
            <span>Auto-refresh setiap 5 menit &middot; Terakhir diperbarui: <span x-text="lastRefreshedLabel" class="font-medium text-gray-700 dark:text-gray-300">baru saja</span></span>
        </div>
        <button type="button" @click="refreshing=true; window.location.reload()"
                :class="refreshing ? 'opacity-50 pointer-events-none' : ''"
                class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-[#0a66c2] hover:text-[#0a66c2] transition active:scale-95">
            <i class="fas fa-rotate text-[10px]" :class="refreshing ? 'animate-spin' : ''"></i>
            <span x-text="refreshing ? 'Memuat…' : 'Refresh Sekarang'"></span>
        </button>
    </div>

    {{-- Credential connected badge --}}
    <div class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-xs text-green-700 dark:text-green-400">
        <i class="fas fa-circle-check text-green-500 flex-shrink-0"></i>
        Akun OSS terhubung. Status dicek otomatis setiap hari 09.00 WIB.
        <a href="{{ route('client.oss-tracker.store-credential') }}" class="ml-auto text-xs text-gray-400 hover:text-red-500 underline" onclick="return confirm('Hapus dan konfigurasi ulang kredensial OSS?')">Ubah</a>
    </div>

    @if($statuses->isEmpty())
        @include('client.components.empty-state', [
            'icon'    => 'fa-clipboard-list',
            'title'   => 'Belum Ada Permohonan Dipantau',
            'message' => 'Tim Bizmark akan menambahkan nomor permohonan OSS Anda ke sistem pemantauan ini.',
            'size'    => 'md',
            'color'   => 'blue',
        ])
    @else
    <div class="space-y-3">
        @foreach($statuses as $status)
        @php
            $syncIcon = match($status->sync_status ?? 'success') {
                'error'   => ['icon' => 'fa-circle-xmark', 'cls' => 'text-red-500'],
                'pending' => ['icon' => 'fa-clock', 'cls' => 'text-amber-500'],
                default   => ['icon' => 'fa-circle-check', 'cls' => 'text-green-500'],
            };
            $badgeClass = match($status->status_code) {
                'TERBIT', 'APPROVED', 'SELESAI' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                'DITOLAK', 'REJECTED'            => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                'PROSES', 'PROCESSING', 'REVIEW' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                'REVISI', 'REVISION'             => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                default                          => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
            };
        @endphp
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-sm transition">
            <div class="flex items-start gap-3">
                {{-- Sync status dot --}}
                <div class="mt-0.5 flex-shrink-0">
                    <i class="fas {{ $syncIcon['icon'] }} {{ $syncIcon['cls'] }} text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="font-semibold text-sm text-gray-800 dark:text-gray-100">{{ $status->permit_type }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $badgeClass }}">{{ $status->status_label }}</span>
                        @if(($status->sync_status ?? 'success') === 'error')
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400">
                            Sync Error
                        </span>
                        @endif
                    </div>
                    @if($status->application_number)
                    <p class="text-xs text-gray-500 dark:text-gray-400">No. Permohonan: {{ $status->application_number }}</p>
                    @endif
                    @if($status->oss_nib)
                    <p class="text-xs text-gray-500 dark:text-gray-400">NIB: {{ $status->oss_nib }}</p>
                    @endif
                    @if($status->project)
                    <p class="text-xs text-gray-500 dark:text-gray-400">Proyek: {{ $status->project->name }}</p>
                    @endif
                    @if($status->sync_error_message && ($status->sync_status ?? '') === 'error')
                    <p class="text-xs text-red-600 dark:text-red-400 mt-1 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-lg">
                        <i class="fas fa-info-circle mr-1"></i>{{ $status->sync_error_message }}
                    </p>
                    @endif
                    <div class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex gap-3 flex-wrap">
                        @if($status->last_checked_at)
                        <span><i class="fas fa-clock text-[9px] mr-0.5"></i>Dicek: {{ $status->last_checked_at->setTimezone('Asia/Jakarta')->format('d/m H:i') }} WIB</span>
                        @endif
                        @if($status->status_changed_at)
                        <span><i class="fas fa-arrows-rotate text-[9px] mr-0.5"></i>Berubah: {{ $status->status_changed_at->setTimezone('Asia/Jakarta')->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
                {{-- Refresh button --}}
                <div class="shrink-0">
                    <form method="POST" action="{{ route('client.oss-tracker.refresh', $status) }}"
                          x-data="{loading:false}" @submit="loading=true">
                        @csrf
                        <button type="submit"
                                :disabled="loading"
                                :class="loading ? 'opacity-50 pointer-events-none' : ''"
                                class="inline-flex items-center gap-1 text-xs border border-[#0a66c2]/40 text-[#0a66c2] dark:text-blue-400 hover:bg-[#0a66c2]/5 dark:hover:bg-[#0a66c2]/10 px-3 py-1.5 rounded-lg transition active:scale-95">
                            <i class="fas fa-rotate text-[9px]" :class="loading ? 'animate-spin' : ''"></i>
                            <span x-text="loading ? 'Memuat…' : 'Refresh'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @endif {{-- end hasCredential --}}

    <p class="text-xs text-gray-400 dark:text-gray-500 text-center">
        Status dicek otomatis setiap hari pukul 09.00 WIB. Notifikasi dikirim saat ada perubahan status.
    </p>

</div>
