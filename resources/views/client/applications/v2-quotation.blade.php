{{-- Quotation — Portal v2 editorial invoice layout --}}
@php
    $statusMap = [
        'draft'    => ['label' => 'Draft',       'variant' => 'neutral', 'icon' => 'fa-file-alt'],
        'sent'     => ['label' => 'Dikirim',      'variant' => 'info',    'icon' => 'fa-paper-plane'],
        'accepted' => ['label' => 'Diterima',     'variant' => 'success', 'icon' => 'fa-check-circle'],
        'rejected' => ['label' => 'Ditolak',      'variant' => 'danger',  'icon' => 'fa-times-circle'],
        'expired'  => ['label' => 'Kadaluarsa',   'variant' => 'warning', 'icon' => 'fa-clock'],
    ];
    $sm = $statusMap[$quotation->status] ?? $statusMap['sent'];
    $canAct = in_array($quotation->status, ['draft','sent']) && !$quotation->isExpired();

    $subtotal = $quotation->base_price
        + collect($quotation->additional_fees ?? [])->sum('amount')
        - ($quotation->discount_amount ?? 0);
    $remainder = $quotation->total_amount - $quotation->down_payment_amount;

    $daysLeft = $quotation->valid_until ? now()->diffInDays($quotation->valid_until, false) : null;
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, #1a0050 0%, #0a0030 100%); color:#fff;"
         aria-label="Penawaran">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(124,58,237,0.3) 0%, transparent 70%); --tr-x: 60px; --tr-y: -60px;"></div>

    <div class="relative max-w-[1000px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <a href="{{ route('client.applications.show', $application->id) }}"
           class="inline-flex items-center gap-2 text-white/70 hover:text-white text-xs font-medium mb-4 transition-colors">
            <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
            Detail Permohonan {{ $application->application_number }}
        </a>

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <span class="portal-eyebrow" style="background: rgba(124,58,237,0.25); color: rgba(255,255,255,0.9); border-color: rgba(124,58,237,0.4);">
                    <i class="fas fa-file-invoice-dollar text-[9px]" aria-hidden="true"></i>
                    Penawaran / Quotation
                </span>
                <h1 class="mt-2 portal-mono text-xl lg:text-2xl font-bold leading-tight">{{ $quotation->quotation_number }}</h1>
                <p class="mt-1 text-sm text-white/80">{{ $application->permitType->name ?? 'Permohonan Izin' }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="portal-pill portal-pill--{{ $sm['variant'] }} portal-pill--with-icon">
                        <i class="fas {{ $sm['icon'] }} text-[9px]" aria-hidden="true"></i>
                        {{ $sm['label'] }}
                    </span>
                    @if($daysLeft !== null && $daysLeft >= 0)
                    <span class="text-xs {{ $daysLeft <= 3 ? 'text-[var(--apple-orange)]' : 'text-white/70' }}">
                        <i class="fas fa-clock text-[10px] mr-1" aria-hidden="true"></i>
                        Berlaku {{ $daysLeft === 0 ? 'hari ini' : "hingga {$quotation->valid_until->format('d M Y')}" }}
                    </span>
                    @endif
                    <button type="button" onclick="window.print()" aria-label="Cetak dokumen quotation" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-white/60 hover:text-white transition-colors print:hidden">
                        <i class="fas fa-print text-[10px]" aria-hidden="true"></i> Cetak
                    </button>
                </div>
            </div>

            {{-- Quick stat --}}
            <div class="flex gap-2 flex-shrink-0 print:hidden">
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 min-w-[110px] text-center">
                    <p class="text-[10px] uppercase tracking-wider text-white/60 font-semibold">Total</p>
                    <p class="text-xl font-bold tabular-nums mt-0.5">Rp {{ number_format($quotation->total_amount / 1000000, 1) }}M</p>
                </div>
                <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 min-w-[110px] text-center">
                    <p class="text-[10px] uppercase tracking-wider text-white/60 font-semibold">DP ({{ $quotation->down_payment_percentage }}%)</p>
                    <p class="text-xl font-bold tabular-nums mt-0.5">Rp {{ number_format($quotation->down_payment_amount / 1000000, 1) }}M</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── BODY ─── --}}
<div class="max-w-[1000px] mx-auto px-4 lg:px-8 py-6 space-y-5 print:px-0 print:py-0 print:space-y-4">

    {{-- Status banners --}}
    @if($quotation->status === 'rejected')
    <div class="flex items-start gap-3 bg-[var(--apple-red)]/8 border border-[var(--apple-red)]/25 rounded-xl p-4">
        <i class="fas fa-times-circle text-[var(--apple-red)] mt-0.5" aria-hidden="true"></i>
        <div>
            <p class="text-sm font-semibold text-[var(--text-primary)]">Penawaran Ditolak</p>
            @if($quotation->rejection_reason)
            <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $quotation->rejection_reason }}</p>
            @endif
        </div>
    </div>
    @endif

    @if($quotation->status === 'accepted')
    <div class="flex items-start gap-3 bg-[var(--apple-green)]/8 border border-[var(--apple-green)]/25 rounded-xl p-4">
        <i class="fas fa-check-circle text-[var(--apple-green)] mt-0.5" aria-hidden="true"></i>
        <p class="text-sm font-semibold text-[var(--text-primary)]">
            Penawaran diterima pada {{ $quotation->accepted_at?->format('d M Y, H:i') }}. Lanjutkan dengan pembayaran uang muka.
        </p>
    </div>
    @endif

    @if($quotation->isExpired() && $quotation->status !== 'accepted')
    <div class="flex items-start gap-3 bg-[var(--apple-orange)]/8 border border-[var(--apple-orange)]/25 rounded-xl p-4">
        <i class="fas fa-triangle-exclamation text-[var(--apple-orange)] mt-0.5" aria-hidden="true"></i>
        <p class="text-sm font-semibold text-[var(--text-primary)]">Penawaran sudah kadaluarsa. Hubungi admin untuk memperbarui.</p>
    </div>
    @endif

    {{-- ─── Two column on desktop ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT: invoice breakdown (2/3) --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Invoice card --}}
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
                {{-- Invoice header (print-friendly) --}}
                <div class="px-5 lg:px-6 py-5 border-b border-[var(--border-subtle)] flex items-start justify-between">
                    <div>
                        <span class="portal-eyebrow"><i class="fas fa-file-invoice text-[9px]" aria-hidden="true"></i> Rincian Biaya</span>
                        @if($quotation->creator)
                        <p class="text-xs text-[var(--text-secondary)] mt-1">Dibuat oleh: {{ $quotation->creator->name }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="portal-mono text-xs text-[var(--text-tertiary)]">{{ $quotation->quotation_number }}</p>
                        <p class="text-xs text-[var(--text-tertiary)] mt-0.5">{{ $quotation->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                {{-- Line items --}}
                <div class="px-5 lg:px-6 py-4 space-y-2">
                    {{-- Base price --}}
                    <div class="flex items-start justify-between gap-4 py-2.5 border-b border-[var(--border-subtle)]">
                        <div>
                            <p class="text-sm font-semibold text-[var(--text-primary)]">Biaya Layanan Utama</p>
                            <p class="text-xs text-[var(--text-secondary)] mt-0.5">{{ $application->permitType->name ?? 'Layanan Perizinan' }}</p>
                        </div>
                        <p class="text-sm font-semibold text-[var(--text-primary)] whitespace-nowrap tabular-nums">
                            Rp {{ number_format($quotation->base_price, 0, ',', '.') }}
                        </p>
                    </div>

                    {{-- Additional fees --}}
                    @if($quotation->additional_fees && count($quotation->additional_fees) > 0)
                        @foreach($quotation->additional_fees as $fee)
                        <div class="flex items-center justify-between gap-4 py-1.5 pl-3 text-sm border-l-2 border-[var(--border-subtle)]">
                            <p class="text-[var(--text-secondary)]">{{ $fee['description'] }}</p>
                            <p class="text-[var(--text-primary)] whitespace-nowrap tabular-nums">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    @endif

                    {{-- Discount --}}
                    @if(($quotation->discount_amount ?? 0) > 0)
                    <div class="flex items-center justify-between gap-4 py-1.5 text-sm">
                        <p class="text-[var(--apple-green)] flex items-center gap-1.5">
                            <i class="fas fa-tag text-[10px]" aria-hidden="true"></i> Diskon
                        </p>
                        <p class="text-[var(--apple-green)] whitespace-nowrap tabular-nums font-medium">
                            − Rp {{ number_format($quotation->discount_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    @endif

                    {{-- Subtotal --}}
                    <div class="flex items-center justify-between gap-4 py-2 text-sm font-medium">
                        <p class="text-[var(--text-secondary)]">Subtotal</p>
                        <p class="text-[var(--text-primary)] whitespace-nowrap tabular-nums">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
                    </div>

                    {{-- Tax --}}
                    <div class="flex items-center justify-between gap-4 py-1.5 text-sm">
                        <p class="text-[var(--text-tertiary)]">Pajak ({{ $quotation->tax_percentage ?? 0 }}%)</p>
                        <p class="text-[var(--text-primary)] whitespace-nowrap tabular-nums">Rp {{ number_format($quotation->tax_amount ?? 0, 0, ',', '.') }}</p>
                    </div>

                    {{-- Total --}}
                    <div class="flex items-center justify-between gap-4 pt-3 border-t-2 border-[var(--border-subtle)]">
                        <p class="text-base font-bold text-[var(--text-primary)] uppercase tracking-wide">Total</p>
                        <p class="text-2xl font-bold text-[var(--client-primary)] tabular-nums">
                            Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- DP + Pelunasan box --}}
                <div class="mx-5 lg:mx-6 mb-5 rounded-xl bg-[var(--client-primary-light)] border border-[var(--client-primary)]/20 px-4 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-xs font-semibold text-[var(--client-primary)] uppercase tracking-wider">
                                Uang Muka ({{ $quotation->down_payment_percentage }}%)
                            </p>
                            <p class="text-xs text-[var(--text-secondary)] mt-0.5">Dibayar di awal</p>
                        </div>
                        <p class="text-xl font-bold text-[var(--client-primary)] tabular-nums">Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="h-px bg-[var(--client-primary)]/15 my-2"></div>
                    <div class="flex items-center justify-between text-sm">
                        <p class="text-[var(--text-secondary)]">Sisa Pelunasan</p>
                        <p class="font-semibold text-[var(--text-primary)] tabular-nums">Rp {{ number_format($remainder, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Terms & Conditions --}}
            @if($quotation->terms_and_conditions)
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-5 lg:px-6 py-4">
                <span class="portal-eyebrow"><i class="fas fa-scroll text-[9px]" aria-hidden="true"></i> Syarat & Ketentuan</span>
                <p class="mt-3 text-sm text-[var(--text-secondary)] whitespace-pre-line leading-relaxed">{{ $quotation->terms_and_conditions }}</p>
            </div>
            @endif
        </div>

        {{-- RIGHT: action + info (1/3) --}}
        <aside class="space-y-4 print:hidden">

            {{-- CTA card --}}
            @if($canAct)
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 py-4">
                <span class="portal-eyebrow"><i class="fas fa-bolt text-[9px]" aria-hidden="true"></i> Tindakan</span>
                <div class="mt-3 space-y-2.5">
                    <form method="POST" action="{{ route('client.quotations.accept', $application->id) }}">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--apple-green)] hover:opacity-90 text-white text-sm font-bold rounded-lg transition-opacity">
                            <i class="fas fa-check text-xs" aria-hidden="true"></i>
                            Terima Penawaran
                        </button>
                    </form>

                    <button type="button" @click="$dispatch('drawer-open', { name: 'reject-quotation' })"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-transparent hover:bg-[var(--apple-red)]/8 text-[var(--apple-red)] text-sm font-semibold rounded-lg border border-[var(--apple-red)]/30 transition-colors">
                        <i class="fas fa-times text-xs" aria-hidden="true"></i>
                        Tolak
                    </button>
                </div>

                <p class="mt-3 text-[10px] text-[var(--text-tertiary)] leading-relaxed">
                    Dengan menerima, Anda menyetujui rincian harga dan syarat di atas.
                </p>
            </div>
            @endif

            {{-- Summary card --}}
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 py-4">
                <span class="portal-eyebrow"><i class="fas fa-info-circle text-[9px]" aria-hidden="true"></i> Ringkasan</span>
                <dl class="mt-3 space-y-2.5 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-[var(--text-tertiary)]">Nomor</dt>
                        <dd class="portal-mono text-[var(--text-primary)] font-semibold">{{ $quotation->quotation_number }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-[var(--text-tertiary)]">Status</dt>
                        <dd>
                            <span class="portal-pill portal-pill--{{ $sm['variant'] }}">{{ $sm['label'] }}</span>
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-[var(--text-tertiary)]">Dibuat</dt>
                        <dd class="text-[var(--text-primary)]">{{ $quotation->created_at->format('d M Y') }}</dd>
                    </div>
                    @if($quotation->valid_until)
                    <div class="flex justify-between gap-2">
                        <dt class="text-[var(--text-tertiary)]">Berlaku s/d</dt>
                        <dd class="text-[var(--text-primary)] {{ $daysLeft !== null && $daysLeft <= 3 ? 'text-[var(--apple-orange)] font-semibold' : '' }}">
                            {{ $quotation->valid_until->format('d M Y') }}
                        </dd>
                    </div>
                    @endif
                    @if($quotation->accepted_at)
                    <div class="flex justify-between gap-2">
                        <dt class="text-[var(--text-tertiary)]">Diterima</dt>
                        <dd class="text-[var(--apple-green)] font-semibold">{{ $quotation->accepted_at->format('d M Y') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Creator --}}
            @if($quotation->creator)
            <div class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-4 py-4">
                <span class="portal-eyebrow"><i class="fas fa-user-tie text-[9px]" aria-hidden="true"></i> Dibuat oleh</span>
                <div class="mt-2 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-[var(--client-primary-light)] border border-[var(--client-primary)]/25 inline-flex items-center justify-center">
                        <i class="fas fa-user text-[10px] text-[var(--client-primary)]" aria-hidden="true"></i>
                    </div>
                    <p class="text-sm font-semibold text-[var(--text-primary)]">{{ $quotation->creator->name }}</p>
                </div>
            </div>
            @endif
        </aside>
    </div>
</div>

{{-- ─── REJECT DRAWER ─── --}}
<x-ui.drawer name="reject-quotation" size="sm" title="Tolak Penawaran" subtitle="Berikan alasan penolakan">
    <form method="POST" action="{{ route('client.quotations.reject', $application->id) }}" class="space-y-4">
        @csrf
        <p class="text-sm text-[var(--text-secondary)]">
            Anda akan menolak penawaran <strong class="text-[var(--text-primary)]">{{ $quotation->quotation_number }}</strong>.
            Admin akan meninjau ulang dan mengirimkan penawaran baru.
        </p>
        <div>
            <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                Alasan Penolakan <span class="text-[var(--apple-red)]">*</span>
            </label>
            <textarea name="rejection_reason" rows="4" required
                      placeholder="Contoh: Harga terlalu tinggi, perlu negosiasi…"
                      class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg focus:ring-2 focus:ring-[var(--client-primary)] focus:border-[var(--client-primary)] text-sm text-[var(--text-primary)] placeholder-[var(--text-tertiary)] resize-none"></textarea>
        </div>
        <div class="flex gap-2 pt-1">
            <button type="button" @click="$dispatch('drawer-close', { name: 'reject-quotation' })"
                    class="flex-1 px-3 py-2.5 bg-[var(--surface-cool)] hover:bg-[var(--surface-sunken)] text-[var(--text-primary)] text-sm font-semibold rounded-md border border-[var(--border-subtle)] transition-colors">
                Batal
            </button>
            <button type="submit"
                    class="flex-1 px-3 py-2.5 bg-[var(--apple-red)] hover:opacity-90 text-white text-sm font-semibold rounded-md transition-opacity">
                Konfirmasi Tolak
            </button>
        </div>
    </form>
</x-ui.drawer>

@push('styles')
<style>
@media print {
    header, nav, .print\:hidden { display: none !important; }
    body { background: white !important; }
    .portal-hero { background: #1a0050 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
}
</style>
@endpush
