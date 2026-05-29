{{-- OSS Tracker Index — Portal v2 --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6 space-y-6"
     x-data="{
         refreshing: false,
         elapsed: 0,
         lastLabel: 'baru saja',
         init() {
             @if($hasCredential)
             setInterval(() => {
                 this.elapsed++;
                 if (this.elapsed < 60) this.lastLabel = 'baru saja';
                 else if (this.elapsed < 3600) this.lastLabel = Math.floor(this.elapsed/60) + 'm lalu';
                 else this.lastLabel = Math.floor(this.elapsed/3600) + 'j lalu';
             }, 1000);
             setInterval(() => { this.refreshing = true; window.location.reload(); }, 5 * 60 * 1000);
             @endif
         }
     }">

    {{-- ─── HERO ─── --}}
    <section class="portal-hero relative overflow-hidden rounded-xl border border-[var(--border-subtle)]"
             style="background: linear-gradient(135deg, #064e3b 0%, #014433 100%); color:#fff;"
             aria-label="OSS-RBA Tracker">
        <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
             style="background: radial-gradient(circle at 50% 50%, rgba(16,185,129,0.25) 0%, transparent 70%);"></div>

        <div class="relative px-5 py-5 lg:py-7">
            <span class="portal-eyebrow" style="background: rgba(16,185,129,0.2); color: rgba(255,255,255,0.9); border-color: rgba(16,185,129,0.3);">
                <i class="fas fa-circle-check text-[9px]" aria-hidden="true"></i>
                OSS-RBA Status Tracker
            </span>
            <h1 class="mt-2 text-xl font-bold text-white">Pantau Status Permohonan OSS</h1>
            <p class="mt-1 text-sm text-white/80">Dicek otomatis setiap hari pukul 09.00 WIB. Notifikasi dikirim bila status berubah.</p>
        </div>
    </section>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300">
        <i class="fas fa-circle-check text-green-500" aria-hidden="true"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('warning'))
    <div class="flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3 text-sm text-amber-800 dark:text-amber-300">
        <i class="fas fa-triangle-exclamation text-amber-500" aria-hidden="true"></i> {{ session('warning') }}
    </div>
    @endif

    {{-- ─── ONBOARDING (no credential) ─── --}}
    @if(!$hasCredential)
    <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
        {{-- Wizard steps --}}
        <div class="bg-[var(--surface-cool)] border-b border-[var(--border-subtle)] px-5 py-4">
            <p class="text-xs font-bold text-[var(--client-primary)] uppercase tracking-wider mb-3">Pengaturan Awal OSS Tracker</p>
            <nav aria-label="Setup wizard" class="flex items-center gap-0">
                @foreach(['Hubungkan', 'Verifikasi', 'Mulai Sync'] as $i => $label)
                <div class="flex items-center {{ $i < 2 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $i === 0 ? 'bg-[var(--client-primary)] text-white' : 'bg-[var(--surface-cool)] border border-[var(--border-subtle)] text-[var(--text-tertiary)]' }}">
                            @if($i === 0)<i class="fas fa-link text-[9px]" aria-hidden="true"></i>@else{{ $i + 1 }}@endif
                        </div>
                        <span class="mt-1 text-[10px] font-medium {{ $i === 0 ? 'text-[var(--client-primary)]' : 'text-[var(--text-tertiary)]' }}">{{ $label }}</span>
                    </div>
                    @if($i < 2)
                    <div class="flex-1 h-0.5 mx-1 mt-[-12px] bg-[var(--border-subtle)]" aria-hidden="true"></div>
                    @endif
                </div>
                @endforeach
            </nav>
        </div>

        {{-- Form --}}
        <div class="px-5 py-5">
            <h3 class="text-sm font-bold text-[var(--text-primary)] mb-1">Hubungkan Akun OSS Anda</h3>
            <p class="text-xs text-[var(--text-secondary)] mb-4">Masukkan kredensial akun <strong>oss.go.id</strong> Anda. Disimpan terenkripsi — tim Bizmark tidak dapat melihat password Anda.</p>
            <form method="POST" action="{{ route('client.oss-tracker.store-credential') }}" class="space-y-4" autocomplete="off">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                        Username OSS <span class="text-[var(--apple-red)]">*</span>
                    </label>
                    <input type="text" name="oss_username" autocomplete="off" required
                           placeholder="Username akun oss.go.id"
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] placeholder-[var(--text-tertiary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                        Password OSS <span class="text-[var(--apple-red)]">*</span>
                    </label>
                    <input type="password" name="oss_password" autocomplete="new-password" required
                           class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
                </div>
                <div class="flex items-start gap-2 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg px-3 py-2.5">
                    <i class="fas fa-lock text-[var(--client-primary)] text-xs mt-0.5 flex-shrink-0" aria-hidden="true"></i>
                    <p class="text-xs text-[var(--text-secondary)]">Kredensial disimpan terenkripsi end-to-end. Anda dapat menghapusnya kapan saja.</p>
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all">
                    <i class="fas fa-link text-xs" aria-hidden="true"></i> Hubungkan Akun OSS
                </button>
            </form>
        </div>
    </section>
    @endif

    {{-- ─── STATUS LIST (has credential) ─── --}}
    @if($hasCredential)
    {{-- Refresh bar --}}
    <div class="flex items-center justify-between gap-3 text-xs text-[var(--text-tertiary)]">
        <span>
            <i class="fas fa-rotate text-[var(--client-primary)] text-[10px]" aria-hidden="true"></i>
            Auto-refresh 5 menit · Terakhir: <span x-text="lastLabel" class="font-medium text-[var(--text-secondary)]">baru saja</span>
        </span>
        <button type="button" @click="refreshing=true; window.location.reload()"
                :class="refreshing ? 'opacity-50 pointer-events-none' : ''"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-[var(--border-subtle)] rounded-lg font-semibold text-[var(--text-secondary)] hover:border-[var(--client-primary)] hover:text-[var(--client-primary)] transition-colors">
            <i class="fas fa-rotate text-[10px]" :class="refreshing ? 'animate-spin' : ''" aria-hidden="true"></i>
            <span x-text="refreshing ? 'Memuat…' : 'Refresh'"></span>
        </button>
    </div>

    @if(empty($statuses) || count($statuses) === 0)
    <x-ui.empty-state icon="fas fa-satellite-dish" title="Belum ada data status"
        description="Akun OSS Anda sudah terhubung. Data akan tersedia setelah sinkronisasi pertama." />
    @else
    <div class="space-y-4">
        @foreach($statuses as $status)
        @php
            $statusKey = $status->status ?? 'pending';
            $badgeStyle = match($statusKey) {
                'approved', 'terbit'    => 'text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-700/50',
                'rejected', 'ditolak'   => 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-700/50',
                'processing', 'proses'  => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700/50',
                default                 => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-700/50',
            };
        @endphp
        <article class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-4 hover:shadow-sm transition-shadow">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <h3 class="text-sm font-bold text-[var(--text-primary)]">{{ $status->permit_type ?? $status->jenis_izin ?? 'Izin OSS' }}</h3>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border {{ $badgeStyle }}">
                            {{ ucfirst($statusKey) }}
                        </span>
                    </div>
                    @if($status->nomor_permohonan ?? $status->application_number ?? null)
                    <p class="text-xs text-[var(--text-tertiary)]">
                        No: {{ $status->nomor_permohonan ?? $status->application_number }}
                    </p>
                    @endif
                    @if($status->keterangan ?? $status->notes ?? null)
                    <p class="text-xs text-[var(--text-secondary)] mt-1">{{ $status->keterangan ?? $status->notes }}</p>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    @if($status->checked_at ?? $status->updated_at ?? null)
                    <time class="text-[10px] text-[var(--text-tertiary)]">
                        {{ \Carbon\Carbon::parse($status->checked_at ?? $status->updated_at)->format('d M Y H:i') }}
                    </time>
                    @endif
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @endif
    @endif
</div>
