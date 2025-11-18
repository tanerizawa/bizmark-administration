@extends('client.layouts.app')

@section('title', 'Detail Quotation')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <!-- Header -->
    <div class="mb-4 sm:mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('client.applications.show', $application->id) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Detail Quotation</h1>
        </div>
        
        @if($quotation->status === 'expired')
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                    <p class="text-red-800 dark:text-red-300 font-semibold">Quotation sudah kadaluarsa</p>
                </div>
                <p class="text-red-600 dark:text-red-400 text-xs sm:text-sm mt-1">Silakan hubungi admin untuk quotation baru</p>
            </div>
        @elseif($quotation->status === 'accepted')
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    <p class="text-green-800 dark:text-green-300 font-semibold">Quotation telah diterima</p>
                </div>
                <p class="text-green-600 dark:text-green-400 text-xs sm:text-sm mt-1">Terima kasih! Silakan lakukan pembayaran untuk melanjutkan proses.</p>
            </div>

            <!-- Payment Button (prominent at top) -->
            @if(in_array($application->status, ['quotation_accepted', 'payment_pending']))
                <div class="mt-4 p-4 sm:p-6 bg-gradient-to-br from-green-500 to-green-600 rounded-xl sm:rounded-2xl shadow-lg">
                    <div class="text-center text-white">
                        <i class="fas fa-credit-card text-3xl sm:text-4xl mb-3"></i>
                        <h3 class="text-lg sm:text-xl font-bold mb-2">Siap untuk Melanjutkan?</h3>
                        <p class="text-green-50 text-xs sm:text-sm mb-4">Lanjutkan ke pembayaran untuk memproses permohonan Anda</p>
                        <a href="{{ route('client.payments.show', $application->id) }}" 
                           class="inline-block px-6 sm:px-8 py-3 sm:py-4 bg-white text-green-600 font-bold text-base sm:text-lg rounded-lg sm:rounded-xl hover:bg-green-50 transition shadow-md">
                            <i class="fas fa-arrow-right mr-2"></i>Lanjut ke Pembayaran
                        </a>
                    </div>
                </div>
            @endif
        @elseif($quotation->status === 'rejected')
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-times-circle text-gray-600 dark:text-gray-400"></i>
                    <p class="text-gray-800 dark:text-gray-200 font-semibold">Quotation ditolak</p>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-xs sm:text-sm mt-1">Admin akan menghubungi Anda segera.</p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            <!-- Quotation Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Quotation</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Nomor Quotation</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $quotation->quotation_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Tanggal Dibuat</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $quotation->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Berlaku Hingga</p>
                        <p class="font-semibold text-sm sm:text-base {{ $quotation->isExpired() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $quotation->valid_until->format('d M Y') }}
                            @if($quotation->isExpired())
                                <span class="text-xs">(Kadaluarsa)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $quotation->status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                            {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                            {{ $quotation->status === 'expired' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                            {{ $quotation->status === 'draft' || $quotation->status === 'sent' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Application Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Permohonan</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Nomor Aplikasi</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $application->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Jenis Izin</p>
                        <p class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">{{ $application->permitType->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Cost Composition Card -->
            <div class="bg-gradient-to-br from-[#0a66c2]/5 to-[#0a66c2]/10 dark:from-[#0a66c2]/10 dark:to-[#0a66c2]/20 rounded-2xl shadow-sm border border-[#0a66c2]/20 dark:border-[#0a66c2]/30 p-4 sm:p-6">
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 bg-[#0a66c2]/10 dark:bg-[#0a66c2]/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calculator text-[#0a66c2] text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white mb-1">Komposisi Biaya Layanan</h3>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300">Total biaya quotation ini mencakup berbagai komponen biaya:</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-3 sm:p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-landmark text-blue-600 dark:text-blue-400"></i>
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Biaya Pemerintah</p>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Biaya resmi yang dibayarkan ke instansi pemerintah</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-3 sm:p-4 border border-emerald-200 dark:border-emerald-800">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-handshake text-emerald-600 dark:text-emerald-400"></i>
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Jasa Konsultan</p>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Biaya jasa konsultan BizMark untuk pengurusan izin</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-3 sm:p-4 border border-amber-200 dark:border-amber-800">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="fas fa-file-alt text-amber-600 dark:text-amber-400"></i>
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200">Persiapan Dokumen</p>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Biaya penyiapan dan legalisasi dokumen</p>
                    </div>
                </div>
                
                <div class="mt-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                    <p class="text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2">
                        <i class="fas fa-lightbulb mt-0.5 flex-shrink-0"></i>
                        <span><span class="font-semibold">Catatan:</span> Rincian lengkap biaya ditampilkan di bawah. Total biaya sudah mencakup semua komponen yang diperlukan untuk pengurusan izin Anda.</span>
                    </p>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-1 flex items-center">
                    <i class="fas fa-file-invoice-dollar text-[#0a66c2] mr-2"></i>
                    <span>Rincian Biaya Lengkap</span>
                </h2>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-5">Detail breakdown semua biaya layanan</p>
                
                <div class="space-y-4">
                    <!-- Base Price -->
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Biaya Layanan Utama</p>
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $application->permitType->name }}</p>
                        </div>
                        <p class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($quotation->base_price, 0, ',', '.') }}</p>
                    </div>

                    <!-- Additional Fees -->
                    @if($quotation->additional_fees && count($quotation->additional_fees) > 0)
                        <div class="space-y-2">
                            <p class="font-medium text-gray-900 dark:text-white">Biaya Tambahan</p>
                            @foreach($quotation->additional_fees as $fee)
                                <div class="flex justify-between items-center pl-4">
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $fee['description'] }}</p>
                                    <p class="text-xs sm:text-sm text-gray-900 dark:text-white">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                        <hr class="border-gray-200 dark:border-gray-700">
                    @endif

                    <!-- Discount -->
                    @if($quotation->discount_amount > 0)
                        <div class="flex justify-between items-center">
                            <p class="text-gray-600 dark:text-gray-400">Diskon</p>
                            <p class="text-green-600 dark:text-green-400">- Rp {{ number_format($quotation->discount_amount, 0, ',', '.') }}</p>
                        </div>
                    @endif

                    <!-- Subtotal -->
                    <div class="flex justify-between items-center font-medium">
                        <p class="text-gray-900 dark:text-white">Subtotal</p>
                        <p class="text-gray-900 dark:text-white">Rp {{ number_format($quotation->base_price + collect($quotation->additional_fees ?? [])->sum('amount') - $quotation->discount_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Tax -->
                    <div class="flex justify-between items-center">
                        <p class="text-gray-600 dark:text-gray-400">Pajak ({{ $quotation->tax_percentage }}%)</p>
                        <p class="text-gray-900 dark:text-white">Rp {{ number_format($quotation->tax_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300 dark:border-gray-600">
                        <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">TOTAL</p>
                        <p class="text-lg sm:text-xl font-bold text-[#0a66c2] dark:text-[#0a66c2]">Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Down Payment -->
                    <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 dark:border-[#0a66c2]/30 rounded-xl p-4 mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-[#0a66c2]"></i>
                                <p class="font-semibold text-gray-900 dark:text-white">Uang Muka ({{ $quotation->down_payment_percentage }}%)</p>
                            </div>
                            <p class="font-bold text-[#0a66c2] dark:text-[#0a66c2]">Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Sisa Pembayaran</p>
                            <p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($quotation->total_amount - $quotation->down_payment_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            @if($quotation->terms_and_conditions)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-sticky-note text-[#0a66c2] mr-2"></i>
                        <span>Catatan</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $quotation->terms_and_conditions }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Action Buttons -->
            @if($quotation->status === 'draft' || $quotation->status === 'sent')
                @if(!$quotation->isExpired())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-4">Aksi</h3>
                        
                        <!-- Accept Button -->
                        <form action="{{ route('client.quotations.accept', $application->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menerima quotation ini?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition mb-3">
                                <i class="fas fa-check mr-2"></i>Terima Quotation
                            </button>
                        </form>

                        <!-- Reject Button -->
                        <button 
                            onclick="showRejectModal()"
                            class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition"
                        >
                            <i class="fas fa-times mr-2"></i>Tolak Quotation
                        </button>
                    </div>
                @endif

                <!-- Payment Button (shown after acceptance) -->
                @if($quotation->status === 'accepted' && in_array($application->status, ['quotation_accepted', 'payment_pending']))
                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border-2 border-green-500 dark:border-green-700 rounded-2xl">
                        <p class="text-xs sm:text-sm text-green-700 dark:text-green-300 font-medium mb-3 text-center">
                            <i class="fas fa-check-circle mr-2"></i>Quotation telah diterima. Silakan lanjutkan pembayaran.
                        </p>
                        <a href="{{ route('client.payments.show', $application->id) }}" 
                           class="block w-full px-4 sm:px-6 py-3 sm:py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold text-base sm:text-lg rounded-xl transition text-center shadow-lg">
                            <i class="fas fa-credit-card mr-2"></i>Lanjut ke Pembayaran
                        </a>
                    </div>
                @endif
            @endif

            <!-- Contact Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-800 p-4 sm:p-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white mb-3">Butuh Bantuan?</h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-4">Hubungi kami jika Anda memiliki pertanyaan tentang quotation ini.</p>
                <div class="space-y-2 text-xs sm:text-sm">
                    <p class="text-gray-700 dark:text-gray-300">
                        <i class="fas fa-phone mr-2 text-blue-600 dark:text-blue-400"></i>
                        +62 xxx-xxxx-xxxx
                    </p>
                    <p class="text-gray-700 dark:text-gray-300">
                        <i class="fas fa-envelope mr-2 text-blue-600 dark:text-blue-400"></i>
                        cs@bizmark.id
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 overflow-y-auto">
    <div class="relative top-20 mx-auto p-4 sm:p-5 border w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl">
            <div class="p-4 sm:p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Tolak Quotation</h3>
                    <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form action="{{ route('client.quotations.reject', $application->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="rejection_reason" 
                            rows="4" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-[#0a66c2] focus:border-transparent dark:bg-gray-700 dark:text-white"
                            placeholder="Jelaskan alasan Anda menolak quotation ini..."
                            required
                        ></textarea>
                    </div>
                    
                    <div class="flex gap-2 sm:gap-3">
                        <button 
                            type="button"
                            onclick="closeRejectModal()"
                            class="flex-1 px-4 py-2 text-sm sm:text-base bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="flex-1 px-4 py-2 text-sm sm:text-base bg-red-600 text-white rounded-xl hover:bg-red-700 transition"
                        >
                            Tolak Quotation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection
