{{-- Payments Show — Portal v2 --}}
@php
    $paid      = $payments->where('status', 'verified')->sum('amount');
    $remaining = max(0, $quotation->total_amount - $paid);
    $dpPaid    = $payments->where('payment_type', 'down_payment')->where('status', 'verified')->isNotEmpty();
    $fullyPaid = $paid >= $quotation->total_amount;

    $steps = [
        ['label' => 'Uang Muka',  'done' => $dpPaid || $fullyPaid],
        ['label' => 'Verifikasi', 'done' => $dpPaid || $fullyPaid],
        ['label' => 'Pelunasan',  'done' => $fullyPaid],
        ['label' => 'Lunas',      'done' => $fullyPaid],
    ];
    $activeStep = $fullyPaid ? 3 : ($dpPaid ? 2 : 0);
    $progressPct = $quotation->total_amount > 0 ? min(100, round($paid / $quotation->total_amount * 100)) : 0;
@endphp

{{-- ─── HERO ─── --}}
<section class="portal-hero relative overflow-hidden border-b border-[var(--border-subtle)]"
         style="background: linear-gradient(135deg, var(--client-primary) 0%, color-mix(in oklab, var(--client-primary) 65%, #001020) 100%); color:#fff;"
         aria-label="Pembayaran {{ $application->application_number }}">
    <div class="portal-glow-orb portal-glow-orb--tr hidden lg:block" aria-hidden="true"
         style="background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.16) 0%, transparent 70%);"></div>

    <div class="relative max-w-[1400px] mx-auto px-4 lg:px-8 py-5 lg:py-7">
        <a href="{{ route('client.applications.show', $application->id) }}"
           class="inline-flex items-center gap-1.5 text-white/70 hover:text-white text-xs mb-3 transition-colors">
            <i class="fas fa-arrow-left text-[9px]" aria-hidden="true"></i> Kembali ke Permohonan
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
            <div>
                <span class="portal-eyebrow" style="background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-credit-card text-[9px]" aria-hidden="true"></i> Pembayaran
                </span>
                <h1 class="mt-2 text-2xl font-bold text-white">{{ $application->application_number }}</h1>
                <p class="text-white/80 text-sm mt-0.5">{{ $application->service?->name ?? $application->service_type ?? '' }}</p>
            </div>
            @if($fullyPaid)
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-400/20 border border-green-300/30 rounded-full text-sm font-semibold text-green-200 self-start">
                <i class="fas fa-check-circle" aria-hidden="true"></i> Lunas
            </span>
            @elseif($pendingPayment)
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400/20 border border-yellow-300/30 rounded-full text-sm font-semibold text-yellow-200 self-start">
                <i class="fas fa-clock" aria-hidden="true"></i> Sedang Diverifikasi
            </span>
            @endif
        </div>

        {{-- Step indicator --}}
        <nav aria-label="Progress pembayaran" class="flex items-center gap-0 mb-5">
            @foreach($steps as $i => $step)
            <div class="flex items-center {{ $i < count($steps)-1 ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-colors
                        {{ $step['done'] ? 'bg-white text-[var(--client-primary)]' : ($i === $activeStep ? 'bg-white/30 border-2 border-white text-white' : 'bg-white/10 border border-white/30 text-white/50') }}">
                        @if($step['done'])<i class="fas fa-check text-[10px]" aria-hidden="true"></i>@else{{ $i + 1 }}@endif
                    </div>
                    <span class="mt-1 text-[10px] font-medium whitespace-nowrap {{ $step['done'] || $i === $activeStep ? 'text-white' : 'text-white/50' }}">
                        {{ $step['label'] }}
                    </span>
                </div>
                @if($i < count($steps)-1)
                <div class="flex-1 h-0.5 mx-1 mt-[-12px] {{ $step['done'] ? 'bg-white' : 'bg-white/20' }}" aria-hidden="true"></div>
                @endif
            </div>
            @endforeach
        </nav>

        {{-- Stat cards --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-xl font-bold tabular-nums">Rp {{ number_format($quotation->total_amount / 1000000, 1) }}M</p>
                <p class="text-[11px] text-white/70 mt-0.5">Total Nilai</p>
            </div>
            <div class="bg-white/10 backdrop-blur border border-white/15 rounded-lg px-4 py-3 text-center">
                <p class="text-xl font-bold tabular-nums">Rp {{ number_format($paid / 1000000, 1) }}M</p>
                <p class="text-[11px] text-white/70 mt-0.5">Sudah Dibayar</p>
            </div>
            <div class="bg-white/10 backdrop-blur border @if($remaining > 0) border-white/15 @else border-green-400/30 bg-green-400/15 @endif rounded-lg px-4 py-3 text-center">
                <p class="text-xl font-bold tabular-nums @if($remaining === 0) text-green-300 @endif">
                    Rp {{ number_format($remaining / 1000000, 1) }}M
                </p>
                <p class="text-[11px] text-white/70 mt-0.5">Sisa Tagihan</p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-white/70 mb-1.5">
                <span>Progress Pembayaran</span>
                <span class="font-semibold text-white">{{ $progressPct }}%</span>
            </div>
            <div class="h-2 bg-white/20 rounded-full overflow-hidden">
                <div class="h-full bg-white rounded-full transition-all duration-700"
                     style="width: {{ $progressPct }}%"></div>
            </div>
        </div>
    </div>
</section>

{{-- ─── CONTENT ─── --}}
<div class="max-w-[1400px] mx-auto px-4 lg:px-8 py-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: payment history + upload form --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Upload bukti bayar --}}
        @if(!$fullyPaid && !$pendingPayment)
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl p-5">
            <h2 class="text-base font-bold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                <i class="fas fa-upload text-[var(--client-primary)]" aria-hidden="true"></i>
                Upload Bukti Pembayaran
            </h2>
            <form action="{{ route('client.payments.upload', $application->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                        Jenis Pembayaran <span class="text-[var(--apple-red)]">*</span>
                    </label>
                    <select name="payment_type" required class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
                        @if(!$dpPaid)
                        <option value="down_payment">Uang Muka (DP)</option>
                        @endif
                        <option value="installment">Cicilan</option>
                        <option value="full_payment">Pelunasan</option>
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                            Jumlah (Rp) <span class="text-[var(--apple-red)]">*</span>
                        </label>
                        <input type="number" name="amount" required min="10000"
                               placeholder="{{ number_format($remaining) }}"
                               class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                            Metode Pembayaran
                        </label>
                        <select name="payment_method" class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
                            <option value="transfer">Transfer Bank</option>
                            <option value="qris">QRIS</option>
                            <option value="cash">Tunai</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">
                        Bukti Transfer <span class="text-[var(--apple-red)]">*</span>
                    </label>
                    <input type="file" name="proof_file" required accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full px-3 py-2 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)]">
                    <p class="text-[10px] text-[var(--text-tertiary)] mt-1">JPG, PNG, PDF — Maks 5MB</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1.5">Catatan</label>
                    <textarea name="notes" rows="2" placeholder="No. referensi transfer, dll."
                              class="w-full px-3 py-2.5 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-lg text-sm text-[var(--text-primary)] placeholder-[var(--text-tertiary)] resize-none"></textarea>
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--client-primary)] text-white text-sm font-semibold rounded-lg hover:brightness-110 transition-all">
                    <i class="fas fa-paper-plane text-xs" aria-hidden="true"></i> Kirim Bukti Pembayaran
                </button>
            </form>
        </section>
        @elseif($pendingPayment)
        <div class="flex items-start gap-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700/50 rounded-xl px-5 py-4">
            <i class="fas fa-clock text-yellow-500 text-sm mt-0.5" aria-hidden="true"></i>
            <div>
                <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">Menunggu Verifikasi</p>
                <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-0.5">
                    Bukti pembayaran Anda sedang diverifikasi oleh tim kami. Biasanya selesai dalam 1×24 jam kerja.
                </p>
            </div>
        </div>
        @endif

        {{-- Payment history --}}
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)]">
                <h2 class="text-base font-bold text-[var(--text-primary)]">Riwayat Pembayaran</h2>
            </div>
            @if($payments->isEmpty())
            <div class="px-5 py-8 text-center">
                <i class="fas fa-receipt text-2xl text-[var(--text-tertiary)] mb-2" aria-hidden="true"></i>
                <p class="text-sm text-[var(--text-tertiary)]">Belum ada pembayaran tercatat.</p>
            </div>
            @else
            <ul class="divide-y divide-[var(--border-subtle)]">
                @foreach($payments as $payment)
                @php
                    $pStatus = $payment->status ?? 'pending';
                    $pStatusColor = match($pStatus) {
                        'verified'  => 'text-green-600 dark:text-green-400',
                        'rejected'  => 'text-red-600 dark:text-red-400',
                        default     => 'text-amber-600 dark:text-amber-400',
                    };
                    $pStatusLabel = match($pStatus) {
                        'verified'  => 'Terverifikasi',
                        'rejected'  => 'Ditolak',
                        default     => 'Menunggu',
                    };
                @endphp
                <li class="px-5 py-4 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-[var(--text-primary)]">
                            Rp {{ number_format($payment->amount) }}
                        </p>
                        <p class="text-xs text-[var(--text-tertiary)] mt-0.5">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'transfer')) }}
                            · {{ $payment->created_at->format('d M Y H:i') }}
                        </p>
                        @if($payment->notes)
                        <p class="text-xs text-[var(--text-tertiary)] truncate max-w-[250px] mt-0.5">{{ $payment->notes }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        <span class="text-xs font-semibold {{ $pStatusColor }}">{{ $pStatusLabel }}</span>
                        @if($payment->proof_file)
                        <a href="{{ Storage::url($payment->proof_file) }}" target="_blank" rel="noopener"
                           class="text-xs text-[var(--client-primary)] hover:underline">
                            <i class="fas fa-image text-[10px]" aria-hidden="true"></i> Bukti
                        </a>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </section>
    </div>

    {{-- RIGHT: invoice breakdown --}}
    <aside class="space-y-5">
        <section class="bg-[var(--surface-elevated)] border border-[var(--border-subtle)] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--border-subtle)]">
                <h2 class="text-sm font-bold text-[var(--text-primary)]">Rincian Tagihan</h2>
            </div>
            <div class="px-5 py-4 space-y-3 text-sm">
                @if($quotation->down_payment_amount)
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-secondary)]">Uang Muka</span>
                    <span class="font-semibold text-[var(--text-primary)]">Rp {{ number_format($quotation->down_payment_amount) }}</span>
                </div>
                @endif
                @foreach($quotation->items ?? [] as $item)
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-secondary)] truncate mr-2">{{ $item->description ?? $item->name ?? 'Item' }}</span>
                    <span class="font-semibold text-[var(--text-primary)] flex-shrink-0">Rp {{ number_format($item->amount ?? 0) }}</span>
                </div>
                @endforeach
                <div class="pt-3 border-t border-[var(--border-subtle)] flex items-center justify-between">
                    <span class="font-bold text-[var(--text-primary)]">Total</span>
                    <span class="font-bold text-[var(--client-primary)] text-base">Rp {{ number_format($quotation->total_amount) }}</span>
                </div>
                <div class="flex items-center justify-between text-green-600 dark:text-green-400">
                    <span>Sudah Dibayar</span>
                    <span class="font-semibold">Rp {{ number_format($paid) }}</span>
                </div>
                <div class="flex items-center justify-between {{ $remaining > 0 ? 'text-[var(--apple-red)]' : 'text-green-600 dark:text-green-400' }}">
                    <span class="font-semibold">Sisa</span>
                    <span class="font-bold">Rp {{ number_format($remaining) }}</span>
                </div>
            </div>
        </section>

        <a href="{{ route('client.applications.show', $application->id) }}"
           class="block text-center px-4 py-3 bg-[var(--surface-cool)] border border-[var(--border-subtle)] rounded-xl text-sm font-semibold text-[var(--text-primary)] hover:bg-[var(--surface-elevated)] transition-colors">
            <i class="fas fa-folder text-[var(--client-primary)] mr-1.5" aria-hidden="true"></i>
            Lihat Permohonan
        </a>
    </aside>
</div>
