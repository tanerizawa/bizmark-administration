@extends('client.layouts.app')

@section('title', 'Detail Quotation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('client.applications.show', $application->id) }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Quotation</h1>
        </div>
        
        @if($quotation->status === 'expired')
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                    <p class="text-red-800 font-semibold">Quotation sudah kadaluarsa</p>
                </div>
                <p class="text-red-600 text-sm mt-1">Silakan hubungi admin untuk quotation baru</p>
            </div>
        @elseif($quotation->status === 'accepted')
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-600"></i>
                    <p class="text-green-800 font-semibold">Quotation telah diterima</p>
                </div>
                <p class="text-green-600 text-sm mt-1">Terima kasih! Silakan lakukan pembayaran untuk melanjutkan proses.</p>
            </div>

            <!-- Payment Button (prominent at top) -->
            @if(in_array($application->status, ['quotation_accepted', 'payment_pending']))
                <div class="mt-4 p-6 bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg">
                    <div class="text-center text-white">
                        <i class="fas fa-credit-card text-4xl mb-3"></i>
                        <h3 class="text-xl font-bold mb-2">Siap untuk Melanjutkan?</h3>
                        <p class="text-green-50 mb-4">Lanjutkan ke pembayaran untuk memproses permohonan Anda</p>
                        <a href="{{ route('client.payments.show', $application->id) }}" 
                           class="inline-block px-8 py-4 bg-white text-green-600 font-bold text-lg rounded-lg hover:bg-green-50 transition shadow-md">
                            <i class="fas fa-arrow-right mr-2"></i>Lanjut ke Pembayaran
                        </a>
                    </div>
                </div>
            @endif
        @elseif($quotation->status === 'rejected')
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-times-circle text-gray-600"></i>
                    <p class="text-gray-800 font-semibold">Quotation ditolak</p>
                </div>
                <p class="text-gray-600 text-sm mt-1">Admin akan menghubungi Anda segera.</p>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quotation Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Quotation</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nomor Quotation</p>
                        <p class="font-semibold text-gray-900">{{ $quotation->quotation_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Dibuat</p>
                        <p class="font-semibold text-gray-900">{{ $quotation->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Berlaku Hingga</p>
                        <p class="font-semibold {{ $quotation->isExpired() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $quotation->valid_until->format('d M Y') }}
                            @if($quotation->isExpired())
                                <span class="text-xs">(Kadaluarsa)</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            {{ $quotation->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $quotation->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $quotation->status === 'expired' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $quotation->status === 'draft' || $quotation->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst($quotation->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Application Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Permohonan</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nomor Aplikasi</p>
                        <p class="font-semibold text-gray-900">{{ $application->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jenis Izin</p>
                        <p class="font-semibold text-gray-900">{{ $application->permitType->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Rincian Biaya</h2>
                
                <div class="space-y-4">
                    <!-- Base Price -->
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <div>
                            <p class="font-medium text-gray-900">Biaya Dasar</p>
                            <p class="text-sm text-gray-600">{{ $application->permitType->name }}</p>
                        </div>
                        <p class="font-semibold text-gray-900">Rp {{ number_format($quotation->base_price, 0, ',', '.') }}</p>
                    </div>

                    <!-- Additional Fees -->
                    @if($quotation->additional_fees && count($quotation->additional_fees) > 0)
                        <div class="space-y-2">
                            <p class="font-medium text-gray-900">Biaya Tambahan</p>
                            @foreach($quotation->additional_fees as $fee)
                                <div class="flex justify-between items-center pl-4">
                                    <p class="text-sm text-gray-600">{{ $fee['description'] }}</p>
                                    <p class="text-sm text-gray-900">Rp {{ number_format($fee['amount'], 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                        <hr class="border-gray-200">
                    @endif

                    <!-- Discount -->
                    @if($quotation->discount_amount > 0)
                        <div class="flex justify-between items-center">
                            <p class="text-gray-600">Diskon</p>
                            <p class="text-green-600">- Rp {{ number_format($quotation->discount_amount, 0, ',', '.') }}</p>
                        </div>
                    @endif

                    <!-- Subtotal -->
                    <div class="flex justify-between items-center font-medium">
                        <p class="text-gray-900">Subtotal</p>
                        <p class="text-gray-900">Rp {{ number_format($quotation->base_price + collect($quotation->additional_fees ?? [])->sum('amount') - $quotation->discount_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Tax -->
                    <div class="flex justify-between items-center">
                        <p class="text-gray-600">Pajak ({{ $quotation->tax_percentage }}%)</p>
                        <p class="text-gray-900">Rp {{ number_format($quotation->tax_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300">
                        <p class="text-xl font-bold text-gray-900">TOTAL</p>
                        <p class="text-xl font-bold text-purple-600">Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Down Payment -->
                    <div class="bg-purple-50 rounded-lg p-4 mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <p class="font-semibold text-gray-900">Uang Muka ({{ $quotation->down_payment_percentage }}%)</p>
                            <p class="font-bold text-purple-600">Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="flex justify-between items-center">
                            <p class="text-sm text-gray-600">Sisa Pembayaran</p>
                            <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($quotation->total_amount - $quotation->down_payment_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            @if($quotation->terms_and_conditions)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $quotation->terms_and_conditions }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Action Buttons -->
            @if($quotation->status === 'draft' || $quotation->status === 'sent')
                @if(!$quotation->isExpired())
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
                        
                        <!-- Accept Button -->
                        <form action="{{ route('client.quotations.accept', $application->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menerima quotation ini?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition mb-3">
                                <i class="fas fa-check mr-2"></i>Terima Quotation
                            </button>
                        </form>

                        <!-- Reject Button -->
                        <button 
                            onclick="showRejectModal()"
                            class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition"
                        >
                            <i class="fas fa-times mr-2"></i>Tolak Quotation
                        </button>
                    </div>
                @endif

                <!-- Payment Button (shown after acceptance) -->
                @if($quotation->status === 'accepted' && in_array($application->status, ['quotation_accepted', 'payment_pending']))
                    <div class="mt-4 p-4 bg-green-50 border-2 border-green-500 rounded-lg">
                        <p class="text-sm text-green-700 font-medium mb-3 text-center">
                            <i class="fas fa-check-circle mr-2"></i>Quotation telah diterima. Silakan lanjutkan pembayaran.
                        </p>
                        <a href="{{ route('client.payments.show', $application->id) }}" 
                           class="block w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white font-bold text-lg rounded-lg hover:from-green-700 hover:to-green-800 transition text-center shadow-lg">
                            <i class="fas fa-credit-card mr-2"></i>Lanjut ke Pembayaran
                        </a>
                    </div>
                @endif
            @endif

            <!-- Contact Info -->
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Butuh Bantuan?</h3>
                <p class="text-sm text-gray-600 mb-4">Hubungi kami jika Anda memiliki pertanyaan tentang quotation ini.</p>
                <div class="space-y-2 text-sm">
                    <p class="text-gray-700">
                        <i class="fas fa-phone mr-2 text-blue-600"></i>
                        +62 xxx-xxxx-xxxx
                    </p>
                    <p class="text-gray-700">
                        <i class="fas fa-envelope mr-2 text-blue-600"></i>
                        support@bizmark.id
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Tolak Quotation</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('client.quotations.reject', $application->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="rejection_reason" 
                    rows="4" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    placeholder="Jelaskan alasan Anda menolak quotation ini..."
                    required
                ></textarea>
            </div>
            
            <div class="flex gap-3">
                <button 
                    type="button"
                    onclick="closeRejectModal()"
                    class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                >
                    Tolak Quotation
                </button>
            </div>
        </form>
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
