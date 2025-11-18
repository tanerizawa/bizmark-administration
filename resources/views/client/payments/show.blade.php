@extends('client.layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('client.applications.show', $application->id) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pembayaran</h1>
        </div>
        <p class="text-gray-600 dark:text-gray-400">Aplikasi: {{ $application->application_number }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payment Options -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Pending Payment Warning -->
            @if($pendingPayment)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-600 p-6 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2">
                                Pembayaran Sedang Diverifikasi
                            </h3>
                            <p class="text-yellow-700 dark:text-yellow-400 mb-3">
                                Bukti pembayaran Anda sedang dalam proses verifikasi oleh admin. 
                                Anda tidak dapat melakukan pembayaran baru sampai proses verifikasi selesai.
                            </p>
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-yellow-200 dark:border-yellow-700">
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Nomor Pembayaran:</span>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $pendingPayment->payment_number }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Jumlah:</span>
                                        <p class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($pendingPayment->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Metode:</span>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($pendingPayment->payment_method) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">Tanggal Upload:</span>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $pendingPayment->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center text-sm text-yellow-700 dark:text-yellow-400">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Biasanya proses verifikasi memakan waktu 1-2 hari kerja</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quotation Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Quotation</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Amount</span>
                        <span class="font-bold text-lg text-gray-900 dark:text-white">Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Down Payment ({{ $quotation->down_payment_percentage }}%)</span>
                        <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Remaining</span>
                        <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($quotation->total_amount - $quotation->down_payment_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($payments->count() > 0)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Riwayat Pembayaran</h3>
                        <div class="space-y-2">
                            @foreach($payments as $payment)
                                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded border border-gray-200 dark:border-gray-600">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $payment->payment_number }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $payment->payment_method }} - {{ ucfirst($payment->status) }}</p>
                                    </div>
                                    <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Payment Method Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 {{ $pendingPayment ? 'opacity-50 pointer-events-none' : '' }}">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pilih Metode Pembayaran</h2>

                @if($pendingPayment)
                    <div class="text-center py-4">
                        <i class="fas fa-lock text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                        <p class="text-gray-500 dark:text-gray-400">Form pembayaran dikunci sementara</p>
                    </div>
                @else
                    <!-- Option 1: Midtrans (All payment methods) -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4 hover:border-purple-500 dark:hover:border-purple-400 cursor-pointer" onclick="selectPaymentMethod('midtrans')">
                    <div class="flex items-start">
                        <input type="radio" name="payment_method" id="midtrans" value="midtrans" class="mt-1">
                        <div class="ml-3 flex-1">
                            <label for="midtrans" class="font-semibold text-gray-900 dark:text-white cursor-pointer">Pembayaran Online</label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Virtual Account, E-Wallet, Credit Card, dan lainnya</p>
                            <div class="flex gap-2 mt-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/120px-Bank_Central_Asia.svg.png" alt="BCA" class="h-6">
                                <img src="https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/120px-BNI_logo.svg.png" alt="BNI" class="h-6">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Logo_BRI.png/120px-Logo_BRI.png" alt="BRI" class="h-6">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Logo_gopay.svg/120px-Logo_gopay.svg.png" alt="GoPay" class="h-6">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Option 2: Manual Transfer -->
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-purple-500 dark:hover:border-purple-400 cursor-pointer" onclick="selectPaymentMethod('manual')">
                    <div class="flex items-start">
                        <input type="radio" name="payment_method" id="manual" value="manual" class="mt-1">
                        <div class="ml-3 flex-1">
                            <label for="manual" class="font-semibold text-gray-900 dark:text-white cursor-pointer">Transfer Manual</label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Transfer ke rekening perusahaan dan upload bukti pembayaran</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Midtrans Payment Form -->
            <div id="midtrans-form" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Pembayaran Online</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah Pembayaran</label>
                        <select id="payment_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            <option value="down_payment">Uang Muka - Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</option>
                            <option value="full_payment">Pembayaran Penuh - Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</option>
                        </select>
                    </div>

                    <button 
                        onclick="processPayment()"
                        class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition"
                    >
                        <i class="fas fa-credit-card mr-2"></i>Lanjutkan Pembayaran
                    </button>
                </div>
            </div>

            <!-- Manual Transfer Form -->
            <div id="manual-form" class="hidden bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Transfer Manual</h3>
                
                <form action="{{ route('client.payments.manual', $application->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-4">
                        <!-- Payment Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Jumlah Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                                <option value="down_payment">Uang Muka - Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</option>
                                <option value="full_payment">Pembayaran Penuh - Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</option>
                            </select>
                        </div>

                        <!-- Bank Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nama Bank <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="bank_name" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                placeholder="Contoh: BCA, Mandiri, BNI"
                                required
                            >
                        </div>

                        <!-- Account Holder -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nama Pengirim <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="account_holder" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                placeholder="Nama sesuai rekening"
                                required
                            >
                        </div>

                        <!-- Transfer Proof -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Bukti Transfer <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="file" 
                                name="transfer_proof" 
                                accept="image/*,application/pdf"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
                                required
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: JPG, PNG, PDF (Max 5MB)</p>
                        </div>

                        <button 
                            type="submit"
                            class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition"
                        >
                            <i class="fas fa-upload mr-2"></i>Upload Bukti Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar - Bank Account Info -->
        <div class="space-y-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Rekening Perusahaan</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Bank BCA</p>
                        <p class="font-semibold text-lg text-gray-900 dark:text-white">1234567890</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">PT Bizmark Indonesia</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Bank Mandiri</p>
                        <p class="font-semibold text-lg text-gray-900 dark:text-white">0987654321</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">PT Bizmark Indonesia</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800 p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">
                    <i class="fas fa-info-circle text-yellow-600 dark:text-yellow-400 mr-2"></i>Informasi Penting
                </h3>
                <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                    <li>• Pastikan jumlah transfer sesuai dengan nominal</li>
                    <li>• Simpan bukti transfer Anda</li>
                    <li>• Verifikasi pembayaran membutuhkan 1x24 jam</li>
                    <li>• Hubungi CS jika ada kendala</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
function selectPaymentMethod(method) {
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.checked = radio.value === method;
    });

    document.getElementById('midtrans-form').classList.add('hidden');
    document.getElementById('manual-form').classList.add('hidden');

    if (method === 'midtrans') {
        document.getElementById('midtrans-form').classList.remove('hidden');
    } else if (method === 'manual') {
        document.getElementById('manual-form').classList.remove('hidden');
    }
}

async function processPayment() {
    const paymentType = document.getElementById('payment_type').value;
    
    try {
        const response = await fetch('{{ route("client.payments.initiate", $application->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_type: paymentType
            })
        });

        const data = await response.json();

        if (data.success) {
            // Open Midtrans Snap
            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    window.location.href = '{{ route("client.payments.success", [$application->id, "__PAYMENT_ID__"]) }}'.replace('__PAYMENT_ID__', data.payment_id);
                },
                onPending: function(result) {
                    alert('Menunggu pembayaran Anda');
                    window.location.href = '{{ route("client.applications.show", $application->id) }}';
                },
                onError: function(result) {
                    alert('Pembayaran gagal! ' + result.status_message);
                },
                onClose: function() {
                    alert('Anda menutup popup pembayaran');
                }
            });
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}
</script>
@endsection
