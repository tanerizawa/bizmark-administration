{{-- Preview & Submit (T&C) — Portal v2 --}}

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 55%, #001020) 100%); color:#fff;"
         aria-label="Persetujuan Syarat & Ketentuan">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.16) 0%, transparent 70%);"></div>
    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
            <i class="fas fa-shield-check text-[9px]" aria-hidden="true"></i>
            Langkah Akhir
        </span>
        <h1 class="mt-2 text-xl font-bold text-white">{{ config('terms.id.title', 'Syarat & Ketentuan Layanan') }}</h1>
        <p class="mt-1 text-sm text-white/80">{{ config('terms.id.subtitle', 'Baca dan setujui sebelum mengajukan permohonan.') }}</p>
        <div class="flex flex-wrap gap-3 mt-3 text-xs text-white/60">
            <span><i class="fas fa-calendar-alt mr-1" aria-hidden="true"></i>Versi: {{ config('terms.version', '1.0') }}</span>
            <span><i class="fas fa-clock mr-1" aria-hidden="true"></i>Berlaku: {{ \Carbon\Carbon::parse(config('terms.effective_date', now()))->isoFormat('D MMMM YYYY') }}</span>
        </div>
    </div>
</section>

{{-- ─── MAIN ─── --}}
<div class="max-w-[860px] mx-auto px-4 lg:px-8 py-6 space-y-5"
     x-data="{
         lang: 'id',
         hasScrolled: false,
         agreed: false,
         checkScroll(el) {
             if ((el.scrollTop + el.clientHeight) >= (el.scrollHeight - 20)) {
                 this.hasScrolled = true;
             }
         }
     }">

    {{-- Application summary card --}}
    <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5 flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-[var(--client-primary)]/10 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-file-alt text-[var(--client-primary)]" aria-hidden="true"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-[var(--text-primary)]">Permohonan #{{ $application->application_number }}</p>
            <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $application->permitType->name }}</p>
            <p class="text-[11px] text-[var(--text-tertiary)] mt-1">
                Diajukan: {{ $application->created_at->isoFormat('D MMMM YYYY') }}
            </p>
        </div>
        <span class="text-[10px] font-bold px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full flex-shrink-0">
            <i class="fas fa-clock mr-1" aria-hidden="true"></i>Menunggu Persetujuan
        </span>
    </div>

    {{-- Language toggle --}}
    <div class="flex gap-2">
        <button type="button" @click="lang = 'id'"
                :class="lang === 'id' ? 'bg-[var(--client-primary)] text-white' : 'bg-[var(--surface-cool)] text-[var(--text-secondary)] border border-[var(--border-subtle)]'"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors">
            🇮🇩 Indonesia
        </button>
        <button type="button" @click="lang = 'en'"
                :class="lang === 'en' ? 'bg-[var(--client-primary)] text-white' : 'bg-[var(--surface-cool)] text-[var(--text-secondary)] border border-[var(--border-subtle)]'"
                class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors">
            🇬🇧 English
        </button>
    </div>

    {{-- T&C scroll box --}}
    <div class="relative">
        <div id="termsScrollBox"
             @scroll="checkScroll($el)"
             class="h-[400px] overflow-y-auto bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5 text-sm leading-relaxed text-[var(--text-secondary)] space-y-4 scroll-smooth">
            @php
                $terms = config('terms');
            @endphp
            <div x-show="lang === 'id'">
                @if($terms)
                @foreach($terms['id']['sections'] ?? [] as $section)
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-[var(--text-primary)] mb-2">{{ $section['title'] ?? '' }}</h3>
                    @if(isset($section['content']))
                    <p class="text-xs text-[var(--text-secondary)]">{{ $section['content'] }}</p>
                    @endif
                    @if(isset($section['items']))
                    <ul class="list-disc list-inside space-y-1 mt-2">
                        @foreach($section['items'] as $item)
                        <li class="text-xs text-[var(--text-secondary)]">{{ $item }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
                @else
                <p class="text-xs text-[var(--text-tertiary)]">Syarat dan ketentuan layanan BizMark.ID belum tersedia. Silakan hubungi tim kami.</p>
                @endif
            </div>
            <div x-show="lang === 'en'" x-cloak>
                @if($terms)
                @foreach($terms['en']['sections'] ?? [] as $section)
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-[var(--text-primary)] mb-2">{{ $section['title'] ?? '' }}</h3>
                    @if(isset($section['content']))
                    <p class="text-xs text-[var(--text-secondary)]">{{ $section['content'] }}</p>
                    @endif
                    @if(isset($section['items']))
                    <ul class="list-disc list-inside space-y-1 mt-2">
                        @foreach($section['items'] as $item)
                        <li class="text-xs text-[var(--text-secondary)]">{{ $item }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
                @else
                <p class="text-xs text-[var(--text-tertiary)]">Terms and conditions are not yet available. Please contact our team.</p>
                @endif
            </div>
        </div>
        {{-- Scroll hint --}}
        <div x-show="!hasScrolled"
             class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-[var(--surface-elevated)] to-transparent rounded-b-xl flex items-end justify-center pb-2 pointer-events-none">
            <span class="text-[10px] text-[var(--text-tertiary)] animate-bounce">
                <i class="fas fa-arrow-down mr-1" aria-hidden="true"></i>Scroll untuk membaca
            </span>
        </div>
    </div>

    {{-- Agree + Submit --}}
    <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5 space-y-4">
        <label class="flex items-start gap-3 cursor-pointer"
               :class="{ 'opacity-50 pointer-events-none': !hasScrolled }">
            <input type="checkbox" x-model="agreed" :disabled="!hasScrolled"
                   class="mt-0.5 w-4 h-4 accent-[var(--client-primary)] flex-shrink-0">
            <span class="text-xs text-[var(--text-secondary)]" x-text="lang === 'id'
                ? 'Saya telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan layanan BizMark.ID yang berlaku.'
                : 'I have read, understood, and agree to all Terms & Conditions of BizMark.ID services.'"></span>
        </label>

        @if(!$hasScrolled ?? false)
        <p x-show="!hasScrolled" class="text-[11px] text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
            <i class="fas fa-info-circle text-[10px]" aria-hidden="true"></i>
            Scroll hingga akhir dokumen untuk mengaktifkan tombol persetujuan.
        </p>
        @endif

        <form method="POST" action="{{ route('client.applications.submit', $application->id) }}">
            @csrf
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('client.applications.show', $application->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[var(--text-secondary)] bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-xl hover:border-[var(--client-primary)]/40 transition-colors">
                    <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
                    <span x-text="lang === 'id' ? 'Kembali' : 'Back'"></span>
                </a>
                <button type="submit"
                        :disabled="!agreed"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-xl hover:brightness-110 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                    <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i>
                    <span x-text="lang === 'id' ? 'Saya Setuju dan Ajukan' : 'I Agree and Submit'"></span>
                </button>
            </div>
        </form>

        <p class="text-[10px] text-[var(--text-tertiary)] text-center">
            <i class="fas fa-lock text-[9px] mr-1" aria-hidden="true"></i>
            Data Anda dilindungi. Persetujuan dicatat dengan timestamp dan versi syarat yang berlaku.
        </p>
    </div>
</div>
