<div class="max-w-xl mx-auto px-4 sm:px-6 py-10">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl overflow-hidden shadow-sm">

        {{-- Success header --}}
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 px-6 py-8 text-center">
            <div class="confetti-ring w-20 h-20 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-white/40">
                <i class="fas fa-check-circle text-white text-3xl"></i>
            </div>
            <h1 class="confetti-text text-2xl font-bold text-white mb-1">
                @if($payment->payment_method === 'manual') Bukti Diterima! @else Pembayaran Berhasil! @endif
            </h1>
            <p class="confetti-text text-green-100 text-sm">
                @if($payment->payment_method === 'manual')
                    Tim kami akan verifikasi dalam 1–2 hari kerja
                @else
                    Pembayaran Anda telah dikonfirmasi secara otomatis
                @endif
            </p>
        </div>

        {{-- Payment detail --}}
        <div class="px-5 sm:px-6 py-5">
            <dl class="space-y-3">
                @foreach([
                    ['No. Pembayaran', $payment->payment_number],
                    ['No. Permohonan', $application->application_number],
                    ['Jumlah', 'Rp ' . number_format($payment->amount, 0, ',', '.')],
                    ['Metode', ucfirst($payment->payment_method)],
                    ['Waktu', $payment->created_at->format('d M Y, H:i')],
                ] as [$label, $value])
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-sm text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $label }}</dt>
                    <dd class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $value }}</dd>
                </div>
                @endforeach
                <div class="flex items-center justify-between gap-4 pt-2 border-t border-gray-100 dark:border-gray-700">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                    <dd>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            @if($payment->status === 'verified' || $payment->status === 'success') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                            @elseif($payment->status === 'pending') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400
                            @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Next steps --}}
        <div class="mx-5 sm:mx-6 mb-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-4">
            <p class="text-xs font-semibold text-[#0a66c2] uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <i class="fas fa-list-check"></i> Langkah Selanjutnya
            </p>
            <ul class="text-xs text-gray-700 dark:text-gray-300 space-y-1.5">
                @if($payment->payment_method === 'manual')
                    <li class="flex gap-2"><i class="fas fa-circle-check text-blue-400 flex-shrink-0 mt-0.5 text-[10px]"></i>Bukti transfer sedang diverifikasi</li>
                    <li class="flex gap-2"><i class="fas fa-circle-check text-blue-400 flex-shrink-0 mt-0.5 text-[10px]"></i>Notifikasi dikirim setelah diverifikasi</li>
                    <li class="flex gap-2"><i class="fas fa-circle-check text-blue-400 flex-shrink-0 mt-0.5 text-[10px]"></i>Permohonan diproses pasca verifikasi</li>
                @else
                    <li class="flex gap-2"><i class="fas fa-circle-check text-blue-400 flex-shrink-0 mt-0.5 text-[10px]"></i>Pembayaran telah terkonfirmasi</li>
                    <li class="flex gap-2"><i class="fas fa-circle-check text-blue-400 flex-shrink-0 mt-0.5 text-[10px]"></i>Tim kami segera memproses permohonan Anda</li>
                    <li class="flex gap-2"><i class="fas fa-circle-check text-blue-400 flex-shrink-0 mt-0.5 text-[10px]"></i>Notifikasi untuk setiap update status</li>
                @endif
            </ul>
        </div>

        {{-- CTA --}}
        <div class="flex flex-col sm:flex-row gap-3 px-5 sm:px-6 pb-6">
            <a href="{{ route('client.applications.show', $application->id) }}"
               class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-xl transition active:scale-95">
                <i class="fas fa-arrow-right text-xs"></i> Lihat Permohonan
            </a>
            <a href="{{ route('client.dashboard') }}"
               class="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-sm rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition active:scale-95">
                <i class="fas fa-house text-xs"></i> Dashboard
            </a>
        </div>

        {{-- Support --}}
        <div class="border-t border-gray-100 dark:border-gray-700 px-5 sm:px-6 py-4 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Butuh bantuan?
                <a href="mailto:cs@bizmark.id" class="text-[#0a66c2] hover:underline font-medium">cs@bizmark.id</a>
            </p>
        </div>

    </div>

    <div class="mt-6 text-center">
        <p class="text-xs text-gray-400 dark:text-gray-500">
            Simpan halaman ini atau screenshot sebagai bukti pembayaran
        </p>
    </div>
</div>
