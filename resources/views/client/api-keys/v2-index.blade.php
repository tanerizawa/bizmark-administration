{{-- API Keys Index — Portal v2 --}}
@php
    $plans = [
        ['key'=>'free',       'name'=>'Free',       'price'=>'Gratis',   'limit'=>'100',    'period'=>'/bln',  'featured'=>false],
        ['key'=>'starter',    'name'=>'Starter',    'price'=>'Rp 299rb', 'limit'=>'5.000',  'period'=>'/bln',  'featured'=>true],
        ['key'=>'pro',        'name'=>'Pro',         'price'=>'Rp 999rb', 'limit'=>'50.000', 'period'=>'/bln',  'featured'=>false],
        ['key'=>'enterprise', 'name'=>'Enterprise',  'price'=>'Custom',   'limit'=>'∞',      'period'=>'',      'featured'=>false],
    ];
    $currentPlan = $keys->first()?->plan ?? null;
    $totalKeys = $keys->count();
    $activeKeys = $keys->where('is_active', true)->count();
    $totalRequests = $keys->sum('request_count') ?? 0;
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, #1e1b4b 0%, #0f0c29 100%); color:#fff;"
         aria-label="B2B API Keys">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(139,92,246,0.3) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <span class="portal-eyebrow" style="background: rgba(139,92,246,0.2); color: rgba(255,255,255,0.9); border-color: rgba(139,92,246,0.3);">
            <i class="fas fa-key text-[9px]" aria-hidden="true"></i>
            B2B API Keys
        </span>
        <h1 class="mt-2 text-2xl font-bold text-white">API Akses Bizmark</h1>
        <p class="mt-1 text-sm text-white/80">Akses data KBLI, cost estimate, dan compliance secara programatik.</p>

        <div class="portal-stat-strip grid grid-cols-3 gap-3 mt-5">
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-2xl font-bold tabular-nums text-white">{{ $totalKeys }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Total Keys</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-2xl font-bold tabular-nums text-white">{{ $activeKeys }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Aktif</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-2xl font-bold tabular-nums text-white">{{ number_format($totalRequests) }}</p>
                <p class="text-[11px] text-white/70 mt-0.5">Total Request</p>
            </div>
        </div>
    </div>
</section>

{{-- ─── MAIN ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6 space-y-8"
     x-data="{
         createModal: false,
         newKeyName: '',
         newKeyPlan: 'free',
         generatedKey: null,
         copied: false,
         copyKey() {
             if (!this.generatedKey) return;
             navigator.clipboard.writeText(this.generatedKey).then(() => {
                 this.copied = true;
                 setTimeout(() => this.copied = false, 2000);
             });
         }
     }">

    {{-- Alerts --}}
    @if(session('success'))
    <div class="flex items-center gap-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-4 py-3 text-sm text-green-800 dark:text-green-300">
        <i class="fas fa-circle-check text-green-500" aria-hidden="true"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-4 py-3 text-sm text-red-700 dark:text-red-300">
        <i class="fas fa-circle-xmark text-red-500 flex-shrink-0" aria-hidden="true"></i> {{ $errors->first() }}
    </div>
    @endif

    {{-- Plan cards --}}
    <section>
        <h2 class="text-sm font-bold text-[var(--text-primary)] mb-4">Pilih Plan API</h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($plans as $p)
            <div class="relative flex flex-col gap-2 p-4 rounded-xl border-2 transition-all
                {{ $currentPlan === $p['key'] ? 'border-[var(--client-primary)] bg-[var(--client-primary)]/5 dark:bg-[var(--client-primary)]/10' : ($p['featured'] ? 'border-[var(--client-primary)]/40' : 'border-[var(--border-subtle)] bg-[var(--surface-elevated)]') }}">
                @if($p['featured'])
                <span class="absolute -top-2.5 left-3 text-[10px] font-bold px-2 py-0.5 bg-[var(--client-primary)] text-white rounded-full">Populer</span>
                @endif
                @if($currentPlan === $p['key'])
                <span class="absolute -top-2.5 right-3 text-[10px] font-bold px-2 py-0.5 bg-green-500 text-white rounded-full">Aktif</span>
                @endif
                <p class="font-bold text-sm text-[var(--text-primary)] mt-1">{{ $p['name'] }}</p>
                <p class="text-xl font-extrabold text-[var(--client-primary)]">{{ $p['price'] }}<span class="text-xs text-[var(--text-tertiary)] font-normal">{{ $p['period'] }}</span></p>
                <p class="text-xs text-[var(--text-secondary)]">{{ $p['limit'] }} req/bln</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Keys table --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-[var(--text-primary)]">API Keys Saya</h2>
            <button type="button" @click="createModal = true"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-[var(--client-primary)] text-white text-xs font-semibold rounded-lg hover:brightness-110 transition-all">
                <i class="fas fa-plus text-[10px]" aria-hidden="true"></i> Buat Key Baru
            </button>
        </div>

        @if($keys->isEmpty())
        <x-ui.empty-state icon="fas fa-key" title="Belum ada API key"
            description="Buat key pertama Anda untuk mulai mengakses Bizmark API." />
        @else
        <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface-cool)] border-b border-[var(--border-subtle)]">
                    <tr>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)]">Nama / Key</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)] hidden sm:table-cell">Plan</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)] hidden md:table-cell">Requests</th>
                        <th class="text-left px-4 py-3 text-[11px] font-semibold uppercase tracking-wider text-[var(--text-tertiary)]">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-subtle)]">
                    @foreach($keys as $key)
                    <tr class="hover:bg-[var(--surface-cool)] transition-colors">
                        <td class="px-4 py-3">
                            <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $key->name }}</p>
                            <code class="text-[10px] font-mono text-[var(--text-tertiary)] bg-[var(--surface-cool)] px-1.5 py-0.5 rounded">
                                {{ substr($key->key, 0, 20) }}…
                            </code>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="text-xs font-semibold px-2 py-0.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-full text-[var(--text-secondary)] capitalize">
                                {{ $key->plan ?? 'free' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="text-xs tabular-nums text-[var(--text-secondary)]">{{ number_format($key->request_count ?? 0) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-[10px] font-bold {{ $key->is_active ? 'text-green-600 dark:text-green-400' : 'text-[var(--text-tertiary)]' }}">
                                {{ $key->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('client.api-keys.toggle', $key->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-semibold text-[var(--text-tertiary)] hover:text-[var(--client-primary)] transition-colors">
                                        {{ $key->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('client.api-keys.destroy', $key->id) }}"
                                      onsubmit="return confirm('Yakin hapus API key ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </section>

    {{-- Docs CTA --}}
    <section class="bg-gradient-to-r from-indigo-900/80 to-purple-900/80 border border-indigo-700/50 rounded-xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="text-sm font-bold text-white mb-1">Butuh bantuan integrasi?</p>
            <p class="text-xs text-white/70">Lihat dokumentasi API lengkap dengan contoh request dan response.</p>
        </div>
        <a href="https://docs.bizmark.id/api" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-indigo-900 text-sm font-semibold rounded-lg hover:shadow-md transition-shadow flex-shrink-0">
            <i class="fas fa-book text-xs" aria-hidden="true"></i> Dokumentasi API
        </a>
    </section>
</div>

{{-- ─── CREATE MODAL ─── --}}
<div x-show="createModal" x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="createModal = false" style="display:none">
    <div class="w-full max-w-md bg-[var(--surface-elevated)] rounded-2xl shadow-2xl overflow-hidden"
         @click.outside="createModal = false">
        <div class="px-5 py-4 border-b border-[var(--border-subtle)] flex items-center justify-between">
            <h3 class="text-base font-bold text-[var(--text-primary)]">Buat API Key Baru</h3>
            <button type="button" @click="createModal = false" aria-label="Tutup modal" class="text-[var(--text-tertiary)] hover:text-[var(--text-primary)] transition-colors">
                <i class="fas fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('client.api-keys.store') }}" class="px-5 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                    Nama Key <span class="text-[var(--apple-red)]">*</span>
                </label>
                <input type="text" name="name" x-model="newKeyName" required placeholder="Mis. Production App"
                       class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] focus:ring-2 focus:ring-[var(--client-primary)]">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Plan</label>
                <select name="plan" x-model="newKeyPlan"
                        class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
                    @foreach($plans as $p)
                    <option value="{{ $p['key'] }}">{{ $p['name'] }} — {{ $p['price'] }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all">
                <i class="fas fa-key text-xs" aria-hidden="true"></i> Buat API Key
            </button>
        </form>
    </div>
</div>
