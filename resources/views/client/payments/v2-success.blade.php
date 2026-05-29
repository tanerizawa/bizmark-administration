{{-- Payments Success — Portal v2 --}}
<section class="min-h-[70vh] flex items-center justify-center px-4 py-12"
         aria-label="Pembayaran berhasil">
    <div class="text-center max-w-md mx-auto">

        {{-- Success ring animation --}}
        <div class="relative inline-flex items-center justify-center mb-8">
            <div class="w-24 h-24 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center"
                 style="animation: successPulse 1.5s ease-in-out 2;">
                <i class="fas fa-check-circle text-5xl text-green-500" aria-hidden="true"></i>
            </div>
            <div class="absolute inset-0 rounded-full border-4 border-green-400/30 scale-[1.3]" aria-hidden="true"></div>
            <div class="absolute inset-0 rounded-full border-2 border-green-400/15 scale-[1.6]" aria-hidden="true"></div>
        </div>

        <span class="portal-eyebrow mb-3">
            <i class="fas fa-shield-check text-[9px]" aria-hidden="true"></i>
            Pembayaran Dikirim
        </span>

        <h1 class="text-2xl font-bold text-[var(--text-primary)] mt-3 mb-2">Terima Kasih!</h1>
        <p class="text-[var(--text-secondary)] text-sm leading-relaxed mb-2">
            Bukti pembayaran untuk permohonan
            <strong class="text-[var(--text-primary)]">{{ $application->application_number }}</strong>
            telah kami terima.
        </p>
        <p class="text-[var(--text-tertiary)] text-sm mb-8">
            Tim kami akan memverifikasi pembayaran Anda dalam 1×24 jam kerja.
            Anda akan mendapat notifikasi setelah verifikasi selesai.
        </p>

        {{-- Payment summary chip --}}
        <div class="inline-flex items-center gap-3 bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl px-5 py-3 mb-8">
            <div class="w-8 h-8 rounded-full bg-[var(--surface-cool)] flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-green-500 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-left">
                <p class="text-[11px] text-[var(--text-tertiary)] uppercase tracking-wider">Jumlah Dibayar</p>
                <p class="text-base font-bold text-[var(--text-primary)] tabular-nums">
                    Rp {{ number_format($payment->amount) }}
                </p>
            </div>
        </div>

        {{-- CTA buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('client.applications.show', $application->id) }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg shadow-sm hover:brightness-110 transition-all">
                <i class="fas fa-folder-open text-xs" aria-hidden="true"></i>
                Lihat Permohonan
            </a>
            <a href="{{ route('client.dashboard') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-[var(--surface-cool)] border border-[var(--border-subtle)] text-[var(--text-primary)] text-sm font-semibold rounded-lg hover:bg-[var(--surface-elevated)] transition-colors">
                <i class="fas fa-house text-xs" aria-hidden="true"></i>
                Dashboard
            </a>
        </div>
    </div>
</section>

<style>
@keyframes successPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.08); }
}
</style>
