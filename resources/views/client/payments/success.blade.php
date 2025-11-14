@extends('client.layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Success Message -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Berhasil!</h1>
            
            @if($payment->payment_method === 'manual')
                <p class="text-gray-600 mb-6">
                    Bukti transfer Anda telah kami terima. Tim kami akan melakukan verifikasi dalam 1x24 jam.
                </p>
            @else
                <p class="text-gray-600 mb-6">
                    Pembayaran Anda telah berhasil diproses. Aplikasi Anda akan segera ditinjau oleh tim kami.
                </p>
            @endif

            <!-- Payment Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="font-semibold text-gray-900 mb-4">Detail Pembayaran</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">No. Pembayaran</span>
                        <span class="font-semibold">{{ $payment->payment_number }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">No. Aplikasi</span>
                        <span class="font-semibold">{{ $application->application_number }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jumlah</span>
                        <span class="font-bold text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode</span>
                        <span class="font-semibold">{{ ucfirst($payment->payment_method) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status</span>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($payment->status === 'success') bg-green-100 text-green-800
                            @elseif($payment->status === 'processing') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waktu</span>
                        <span class="font-semibold">{{ $payment->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 rounded-lg p-6 mb-6 text-left border border-blue-200">
                <h3 class="font-semibold text-gray-900 mb-3">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Langkah Selanjutnya
                </h3>
                
                @if($payment->payment_method === 'manual')
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>✓ Bukti transfer Anda sedang dalam proses verifikasi</li>
                        <li>✓ Verifikasi biasanya memakan waktu 1x24 jam</li>
                        <li>✓ Anda akan mendapat notifikasi setelah pembayaran diverifikasi</li>
                        <li>✓ Setelah pembayaran diverifikasi, aplikasi Anda akan diproses</li>
                    </ul>
                @else
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li>✓ Pembayaran Anda telah dikonfirmasi</li>
                        <li>✓ Tim kami akan segera memproses aplikasi Anda</li>
                        <li>✓ Anda akan mendapat notifikasi untuk setiap update status</li>
                        <li>✓ Cek dashboard secara berkala untuk melihat progress</li>
                    </ul>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <a href="{{ route('client.applications.show', $application->id) }}" 
                   class="flex-1 px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-eye mr-2"></i>Lihat Aplikasi
                </a>
                
                <a href="{{ route('client.dashboard') }}" 
                   class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-home mr-2"></i>Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Support Info -->
        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Butuh bantuan? Hubungi kami di:</p>
            <p class="font-semibold mt-1">
                <i class="fas fa-phone mr-2"></i>+62 123 4567 8900
                <span class="mx-3">|</span>
                <i class="fas fa-envelope mr-2"></i>support@bizmark.id
            </p>
        </div>
    </div>
</div>
@endsection
