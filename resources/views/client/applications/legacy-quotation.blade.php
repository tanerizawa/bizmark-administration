@php
    $statusMap = [
        'draft'    => ['label' => 'Draft',    'icon' => 'fa-file-alt',       'color' => 'gray'],
        'sent'     => ['label' => 'Dikirim',  'icon' => 'fa-paper-plane',    'color' => 'blue'],
        'accepted' => ['label' => 'Diterima', 'icon' => 'fa-check-circle',   'color' => 'green'],
        'rejected' => ['label' => 'Ditolak',  'icon' => 'fa-times-circle',   'color' => 'red'],
        'expired'  => ['label' => 'Kadaluarsa','icon' => 'fa-clock',         'color' => 'gray'],
    ];
    $sm = $statusMap[$quotation->status] ?? $statusMap['sent'];
    $canAct = in_array($quotation->status, ['draft', 'sent']) && !$quotation->isExpired();
@endphp

{{-- ═══════════════════════════════════════════
     HERO
═══════════════════════════════════════════ --}}
<div class="client-hero px-4 sm:px-6 py-6 print:hidden">
    <div class="max-w-5xl mx-auto">
        {{-- Back --}}
        <a href="{{ route('client.applications.show', $application->id) }}"
           class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm mb-4 active:scale-95 transition-all -ml-1 px-1">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Permohonan
        </a>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Quotation</p>
                <h1 class="text-2xl font-bold mb-1">{{ $quotation->quotation_number }}</h1>
                <p class="text-sm text-white/80">{{ $application->application_number }} &middot; {{ $application->permitType->name ?? 'Permohonan Izin' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/20 backdrop-blur font-semibold text-sm">
                    <i class="fas {{ $sm['icon'] }}"></i>
                    {{ $sm['label'] }}
                </span>
                <button onclick="window.print()"
                        class="hidden sm:inline-flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium rounded-xl transition">
                    <i class="fas fa-print text-xs"></i> Cetak
                </button>
            </div>
        </div>

        {{-- Stat pills --}}
        <div class="grid grid-cols-3 gap-2 mt-5">
            <div class="bg-white/10 backdrop-blur px-3 sm:px-5 py-3 text-center">
                <p class="text-lg sm:text-2xl font-bold">Rp {{ number_format($quotation->total_amount / 1000000, 1) }}M</p>
                <p class="text-[10px] sm:text-xs text-white/70 mt-0.5">Total Nilai</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-3 sm:px-5 py-3 text-center">
                <p class="text-lg sm:text-2xl font-bold">Rp {{ number_format($quotation->down_payment_amount / 1000000, 1) }}M</p>
                <p class="text-[10px] sm:text-xs text-white/70 mt-0.5">Uang Muka ({{ $quotation->down_payment_percentage }}%)</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-3 sm:px-5 py-3 text-center">
                <p class="text-lg sm:text-2xl font-bold">{{ $quotation->valid_until->format('d M') }}</p>
                <p class="text-[10px] sm:text-xs text-white/70 mt-0.5">Berlaku Hingga</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     MAIN BODY
═══════════════════════════════════════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6"
     x-data="{
         showBreakdown: true,
         showAcceptModal: false,
         showRejectModal: false,
     }">

    {{-- Status alert --}}
    @if($quotation->status === 'expired')
    <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-5">
        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="font-semibold text-red-800 dark:text-red-300 text-sm">Quotation sudah kadaluarsa</p>
            <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">Silakan hubungi admin untuk quotation baru.</p>
        </div>
    </div>
    @elseif($quotation->status === 'accepted')
    <div class="flex items-start gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-5">
        <i class="fas fa-check-circle text-green-500 mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="font-semibold text-green-800 dark:text-green-300 text-sm">Quotation telah diterima</p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">Silakan lanjutkan ke pembayaran untuk memproses permohonan.</p>
        </div>
    </div>
    @elseif($quotation->status === 'rejected')
    <div class="flex items-start gap-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-5">
        <i class="fas fa-times-circle text-gray-500 mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">Quotation ditolak</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Admin akan menghubungi Anda segera untuk tindak lanjut.</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── Left: Breakdown ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Price Breakdown (collapsible) --}}
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
                <button type="button"
                        @click="showBreakdown = !showBreakdown"
                        class="w-full flex items-center justify-between px-4 sm:px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left min-h-[56px]">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-[#0a66c2] text-base"></i>
                        Rincian Biaya
                    </h2>
                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200"
                       :class="showBreakdown ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="showBreakdown" x-collapse>
                    <div class="px-4 sm:px-6 pb-5 space-y-3">
                        {{-- Base price --}}
                        <div class="flex justify-between items-start py-3 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">Biaya Layanan Utama</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->permitType->name ?? '' }}</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap ml-4">Rp {{ number_format($quotation->base_price, 0, ',', '.') }}</p>
                        </div>

                        {{-- Additional fees --}}
                        @if($quotation->additional_fees && count($quotation->additional_fees) > 0)
                            @foreach($quotation->additional_fees as $fee)
                            <div class="flex justify-between items-center pl-4 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">{{ $fee['description'] }}</p>
                                <p class="text-gray-900 dark:text-white whitespace-nowrap ml-4">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</p>
                            </div>
                            @endforeach
                            <hr class="border-gray-100 dark:border-gray-700">
                        @endif

                        {{-- Discount --}}
                        @if($quotation->discount_amount > 0)
                        <div class="flex justify-between items-center text-sm">
                            <p class="text-gray-600 dark:text-gray-400">Diskon</p>
                            <p class="text-green-600 dark:text-green-400 font-medium whitespace-nowrap ml-4">− Rp {{ number_format($quotation->discount_amount, 0, ',', '.') }}</p>
                        </div>
                        @endif

                        {{-- Subtotal --}}
                        <div class="flex justify-between items-center text-sm font-medium">
                            <p class="text-gray-700 dark:text-gray-300">Subtotal</p>
                            <p class="text-gray-900 dark:text-white whitespace-nowrap ml-4">Rp {{ number_format($quotation->base_price + collect($quotation->additional_fees ?? [])->sum('amount') - $quotation->discount_amount, 0, ',', '.') }}</p>
                        </div>

                        {{-- Tax --}}
                        <div class="flex justify-between items-center text-sm">
                            <p class="text-gray-500 dark:text-gray-400">Pajak ({{ $quotation->tax_percentage }}%)</p>
                            <p class="text-gray-900 dark:text-white whitespace-nowrap ml-4">Rp {{ number_format($quotation->tax_amount, 0, ',', '.') }}</p>
                        </div>

                        {{-- Total --}}
                        <div class="flex justify-between items-center pt-3 border-t-2 border-gray-200 dark:border-gray-600">
                            <p class="text-base font-bold text-gray-900 dark:text-white">TOTAL</p>
                            <p class="text-xl font-bold text-[#0a66c2]">Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</p>
                        </div>

                        {{-- DP box --}}
                        <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 rounded-xl p-4 mt-1">
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave text-[#0a66c2] text-xs"></i>
                                    Uang Muka ({{ $quotation->down_payment_percentage }}%)
                                </p>
                                <p class="text-base font-bold text-[#0a66c2]">Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>Sisa pembayaran (pelunasan)</span>
                                <span class="font-medium text-gray-700 dark:text-gray-300">Rp {{ number_format($quotation->total_amount - $quotation->down_payment_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terms --}}
            @if($quotation->terms_and_conditions)
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-sticky-note text-[#0a66c2] text-base"></i>
                        Catatan & Syarat
                    </h2>
                </div>
                <div class="px-4 sm:px-6 py-4">
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $quotation->terms_and_conditions }}</p>
                </div>
            </div>
            @endif

            {{-- Quotation meta --}}
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    <div class="flex justify-between items-center px-4 sm:px-6 py-3 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Nomor Quotation</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $quotation->quotation_number }}</span>
                    </div>
                    <div class="flex justify-between items-center px-4 sm:px-6 py-3 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Tanggal Dibuat</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $quotation->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center px-4 sm:px-6 py-3 text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Berlaku Hingga</span>
                        <span class="font-medium {{ $quotation->isExpired() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $quotation->valid_until->format('d M Y') }}
                            @if($quotation->isExpired()) <span class="text-xs">(Kadaluarsa)</span> @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right: Sidebar ── --}}
        <div class="space-y-4">

            {{-- Accept / Reject actions --}}
            @if($canAct)
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 p-4 sm:p-5">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Tindakan</p>
                <div class="space-y-2">
                    <button @click="showAcceptModal = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-xl transition active:scale-95">
                        <i class="fas fa-check text-xs"></i> Terima Quotation
                    </button>
                    <button @click="showRejectModal = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-700 border border-red-200 dark:border-red-800 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 font-semibold text-sm rounded-xl transition active:scale-95">
                        <i class="fas fa-times text-xs"></i> Tolak Quotation
                    </button>
                </div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-3 text-center">Berlaku hingga {{ $quotation->valid_until->format('d M Y') }}</p>
            </div>
            @endif

            {{-- Go to payment (accepted) --}}
            @if($quotation->status === 'accepted' && in_array($application->status, ['quotation_accepted', 'payment_pending']))
            <div class="bg-[#0a66c2] p-4 sm:p-5">
                <p class="text-sm font-bold text-white mb-1">Siap Melanjutkan?</p>
                <p class="text-xs text-white/70 mb-4">Lakukan pembayaran untuk memproses permohonan Anda.</p>
                <a href="{{ route('client.payments.show', $application->id) }}"
                   class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white text-[#0a66c2] font-bold text-sm rounded-xl hover:bg-blue-50 active:scale-95 transition-all">
                    <i class="fas fa-arrow-right text-xs"></i> Lanjut ke Pembayaran
                </a>
            </div>
            @endif

            {{-- Print --}}
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 p-4 print:hidden">
                <button onclick="window.print()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition">
                    <i class="fas fa-print text-xs"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════
         ACCEPT CONFIRMATION MODAL
    ══════════════════════════════════ --}}
    <div x-show="showAcceptModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @click.self="showAcceptModal = false"
         style="display:none">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 pt-6 pb-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Konfirmasi Penerimaan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                {{-- Summary --}}
                <div class="bg-gray-50 dark:bg-gray-700/40 rounded-xl p-4 mb-5 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Nomor Quotation</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $quotation->quotation_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Total Nilai</span>
                        <span class="font-bold text-[#0a66c2]">Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Uang Muka</span>
                        <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">Dengan menerima, Anda menyetujui nilai dan syarat di atas. Langkah berikutnya adalah pembayaran uang muka.</p>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button @click="showAcceptModal = false"
                        class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Batal
                </button>
                <form action="{{ route('client.quotations.accept', $application->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-[#0a66c2] hover:bg-[#004182] text-white text-sm font-bold rounded-xl transition active:scale-95">
                        <i class="fas fa-check mr-1 text-xs"></i> Ya, Terima
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════
         REJECT MODAL
    ══════════════════════════════════ --}}
    <div x-show="showRejectModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         @click.self="showRejectModal = false"
         style="display:none">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <div class="px-6 pt-6 pb-2">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-times text-red-500"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Tolak Quotation</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Berikan alasan penolakan</p>
                        </div>
                    </div>
                    <button @click="showRejectModal = false" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <form action="{{ route('client.quotations.reject', $application->id) }}" method="POST" id="rejectForm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" for="rejection_reason">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason"
                                  name="rejection_reason"
                                  rows="4"
                                  required
                                  placeholder="Jelaskan alasan Anda menolak quotation ini..."
                                  class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] dark:bg-gray-700 dark:text-white transition"></textarea>
                    </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button type="button" @click="showRejectModal = false"
                        class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" form="rejectForm"
                        class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition active:scale-95">
                    <i class="fas fa-times mr-1 text-xs"></i> Tolak
                </button>
            </div>
                </form>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .client-hero { background: #0a66c2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    nav, footer, .print\:hidden { display: none !important; }
    body { font-size: 12pt; }
    .rounded-2xl, .rounded-xl { border-radius: 0 !important; }
}
</style>
@endpush
