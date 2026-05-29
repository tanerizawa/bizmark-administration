@php
    $paid      = $payments->where('status', 'verified')->sum('amount');
    $remaining = max(0, $quotation->total_amount - $paid);
    $dpPaid    = $payments->where('payment_type', 'down_payment')->where('status', 'verified')->isNotEmpty();
    $fullyPaid = $paid >= $quotation->total_amount;

    // Payment lifecycle steps
    $steps = [
        ['label' => 'Uang Muka',   'done' => $dpPaid || $fullyPaid],
        ['label' => 'Verifikasi',  'done' => $dpPaid || $fullyPaid],
        ['label' => 'Pelunasan',   'done' => $fullyPaid],
        ['label' => 'Lunas',       'done' => $fullyPaid],
    ];
    $activeStep = $fullyPaid ? 3 : ($dpPaid ? 2 : 0);
@endphp

{{-- ══════════════════ HERO ══════════════════ --}}
<div class="client-hero px-4 sm:px-6 py-6">
    <div class="max-w-5xl mx-auto">
        <a href="{{ route('client.applications.show', $application->id) }}"
           class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm mb-4 active:scale-95 transition -ml-1 px-1">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Permohonan
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div>
                <p class="text-xs text-white/70 uppercase tracking-wider mb-1">Pembayaran</p>
                <h1 class="text-2xl font-bold">{{ $application->application_number }}</h1>
            </div>
            @if($fullyPaid)
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-400/20 border border-green-300/30 rounded-full text-sm font-semibold">
                <i class="fas fa-check-circle text-green-300"></i> Lunas
            </span>
            @elseif($pendingPayment)
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-400/20 border border-yellow-300/30 rounded-full text-sm font-semibold">
                <i class="fas fa-clock text-yellow-300"></i> Sedang Diverifikasi
            </span>
            @endif
        </div>

        {{-- Step indicator --}}
        <div class="flex items-center gap-0">
            @foreach($steps as $i => $step)
            <div class="flex items-center {{ $i < count($steps)-1 ? 'flex-1' : '' }}">
                <div class="flex flex-col items-center">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-colors
                        {{ $step['done'] ? 'bg-white text-[#0a66c2]' : ($i === $activeStep ? 'bg-white/30 border-2 border-white text-white' : 'bg-white/10 border border-white/30 text-white/50') }}">
                        @if($step['done'])
                            <i class="fas fa-check text-[10px]"></i>
                        @else
                            {{ $i + 1 }}
                        @endif
                    </div>
                    <span class="mt-1 text-[10px] font-medium text-center whitespace-nowrap
                        {{ $step['done'] || $i === $activeStep ? 'text-white' : 'text-white/50' }}">
                        {{ $step['label'] }}
                    </span>
                </div>
                @if($i < count($steps)-1)
                <div class="flex-1 h-0.5 mx-1 mt-[-12px] {{ $step['done'] ? 'bg-white' : 'bg-white/20' }}"></div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Stat pills --}}
        <div class="grid grid-cols-3 gap-2 mt-5">
            <div class="bg-white/10 backdrop-blur px-3 sm:px-5 py-3 text-center">
                <p class="text-lg sm:text-2xl font-bold">Rp {{ number_format($quotation->total_amount / 1000000, 1) }}M</p>
                <p class="text-[10px] sm:text-xs text-white/70 mt-0.5">Total Nilai</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-3 sm:px-5 py-3 text-center">
                <p class="text-lg sm:text-2xl font-bold">Rp {{ number_format($paid / 1000000, 1) }}M</p>
                <p class="text-[10px] sm:text-xs text-white/70 mt-0.5">Sudah Dibayar</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-3 sm:px-5 py-3 text-center">
                <p class="text-lg sm:text-2xl font-bold {{ $remaining > 0 ? '' : 'text-green-300' }}">
                    Rp {{ number_format($remaining / 1000000, 1) }}M
                </p>
                <p class="text-[10px] sm:text-xs text-white/70 mt-0.5">Sisa</p>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════ BODY ══════════════════ --}}
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-6"
     x-data="{
         method: '',
         previewUrl: null,
         previewName: '',
         handleFile(e) {
             const f = e.target.files[0];
             if (!f) return;
             this.previewName = f.name;
             if (f.type.startsWith('image/')) {
                 const r = new FileReader();
                 r.onload = ev => this.previewUrl = ev.target.result;
                 r.readAsDataURL(f);
             } else {
                 this.previewUrl = null;
             }
         }
     }">

    {{-- Pending verification alert --}}
    @if($pendingPayment)
    <div class="flex items-start gap-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 mb-5">
        <i class="fas fa-clock text-yellow-500 mt-0.5 flex-shrink-0 text-lg"></i>
        <div class="flex-1">
            <p class="font-semibold text-yellow-800 dark:text-yellow-300 text-sm mb-1">Pembayaran Sedang Diverifikasi</p>
            <p class="text-xs text-yellow-700 dark:text-yellow-400 mb-3">Bukti pembayaran Anda sedang diproses. Biasanya 1–2 hari kerja.</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white dark:bg-gray-800 rounded-xl p-3 border border-yellow-100 dark:border-yellow-900">
                <div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">No. Pembayaran</p>
                    <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $pendingPayment->payment_number }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">Jumlah</p>
                    <p class="text-xs font-semibold text-gray-900 dark:text-white">Rp {{ number_format($pendingPayment->amount, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">Metode</p>
                    <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ ucfirst($pendingPayment->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400">Diupload</p>
                    <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $pendingPayment->created_at->format('d M H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Left: Payment form --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Method selection cards --}}
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700 {{ $pendingPayment ? 'opacity-50 pointer-events-none select-none' : '' }}">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-credit-card text-[#0a66c2] text-base"></i>
                        Metode Pembayaran
                    </h2>
                </div>
                @if($pendingPayment)
                <div class="px-4 sm:px-6 py-6 text-center text-gray-400 dark:text-gray-500">
                    <i class="fas fa-lock text-3xl mb-2"></i>
                    <p class="text-sm">Form dikunci selama pembayaran diverifikasi</p>
                </div>
                @else
                <div class="p-4 sm:p-5 space-y-3">
                    {{-- Midtrans --}}
                    <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all
                        hover:border-[#0a66c2]/50 dark:hover:border-[#0a66c2]/50
                        {{ '' }}"
                        :class="method === 'midtrans' ? 'border-[#0a66c2] bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10' : 'border-gray-200 dark:border-gray-700'">
                        <input type="radio" name="payment_method_ui" value="midtrans"
                               x-model="method" class="mt-1 accent-[#0a66c2]">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white">Pembayaran Online</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">VA, e-wallet, kartu kredit, QRIS via Midtrans</p>
                            <div class="flex gap-2 mt-2 flex-wrap">
                                <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded font-medium text-gray-600 dark:text-gray-400">BCA VA</span>
                                <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded font-medium text-gray-600 dark:text-gray-400">GoPay</span>
                                <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded font-medium text-gray-600 dark:text-gray-400">QRIS</span>
                                <span class="text-[10px] px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded font-medium text-gray-600 dark:text-gray-400">+ lainnya</span>
                            </div>
                        </div>
                        <i class="fas fa-bolt text-[#0a66c2] mt-1 flex-shrink-0" aria-hidden="true"></i>
                    </label>

                    {{-- Manual transfer --}}
                    <label class="flex items-start gap-4 p-4 rounded-xl border-2 cursor-pointer transition-all
                        hover:border-[#0a66c2]/50 dark:hover:border-[#0a66c2]/50"
                        :class="method === 'manual' ? 'border-[#0a66c2] bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10' : 'border-gray-200 dark:border-gray-700'">
                        <input type="radio" name="payment_method_ui" value="manual"
                               x-model="method" class="mt-1 accent-[#0a66c2]">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 dark:text-white">Transfer Manual</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Transfer ke rekening perusahaan, upload bukti</p>
                        </div>
                        <i class="fas fa-university text-gray-400 mt-1 flex-shrink-0" aria-hidden="true"></i>
                    </label>
                </div>

                {{-- Midtrans form --}}
                <div x-show="method === 'midtrans'" x-collapse class="px-4 sm:px-5 pb-5">
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah Pembayaran</label>
                            <select id="payment_type" class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2]">
                                <option value="down_payment">Uang Muka — Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</option>
                                <option value="full_payment">Pembayaran Penuh — Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</option>
                            </select>
                        </div>
                        <button onclick="processPayment()"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-xl transition active:scale-95">
                            <i class="fas fa-arrow-right text-xs"></i> Lanjutkan Pembayaran
                        </button>
                    </div>
                </div>

                {{-- Manual form --}}
                <div x-show="method === 'manual'" x-collapse class="px-4 sm:px-5 pb-5">
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <form action="{{ route('client.payments.manual', $application->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Jumlah Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <select name="payment_type" required
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2]">
                                    <option value="down_payment">Uang Muka — Rp {{ number_format($quotation->down_payment_amount, 0, ',', '.') }}</option>
                                    <option value="full_payment">Pembayaran Penuh — Rp {{ number_format($quotation->total_amount, 0, ',', '.') }}</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nama Bank <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="bank_name" placeholder="BCA, Mandiri, BNI..." required
                                           class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Nama Pengirim <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="account_holder" placeholder="Nama sesuai rekening" required
                                           class="w-full px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-[#0a66c2]">
                                </div>
                            </div>

                            {{-- File upload with preview --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Bukti Transfer <span class="text-red-500">*</span>
                                </label>
                                <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-5 cursor-pointer hover:border-[#0a66c2] dark:hover:border-[#0a66c2] transition-colors"
                                       :class="previewUrl || previewName ? 'border-[#0a66c2] bg-[#0a66c2]/5' : ''">
                                    <template x-if="previewUrl">
                                        <img :src="previewUrl" class="max-h-40 rounded-lg object-contain" alt="Preview bukti transfer">
                                    </template>
                                    <template x-if="!previewUrl && previewName">
                                        <div class="flex items-center gap-2 text-[#0a66c2]">
                                            <i class="fas fa-file-pdf text-xl"></i>
                                            <span class="text-sm font-medium" x-text="previewName"></span>
                                        </div>
                                    </template>
                                    <template x-if="!previewName">
                                        <div class="text-center">
                                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 dark:text-gray-600 mb-1"></i>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Klik untuk upload bukti transfer</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">JPG, PNG, PDF — maks. 5MB</p>
                                        </div>
                                    </template>
                                    <input type="file" name="transfer_proof" accept="image/*,application/pdf" required
                                           class="sr-only" @change="handleFile($event)">
                                </label>
                                <template x-if="previewName">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="previewName"></p>
                                </template>
                            </div>

                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-[#0a66c2] hover:bg-[#004182] text-white font-semibold text-sm rounded-xl transition active:scale-95">
                                <i class="fas fa-upload text-xs"></i> Upload Bukti Pembayaran
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            {{-- Payment history --}}
            @if($payments->count() > 0)
            <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-history text-[#0a66c2] text-base"></i>
                        Riwayat Pembayaran
                    </h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($payments as $payment)
                    <div class="flex items-center gap-4 px-4 sm:px-6 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $payment->status === 'verified' ? 'bg-green-50 dark:bg-green-900/30' : ($payment->status === 'pending' ? 'bg-yellow-50 dark:bg-yellow-900/30' : 'bg-red-50 dark:bg-red-900/30') }}">
                            <i class="fas {{ $payment->status === 'verified' ? 'fa-check text-green-600' : ($payment->status === 'pending' ? 'fa-clock text-yellow-600' : 'fa-times text-red-600') }} text-xs dark:opacity-80"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $payment->payment_number }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($payment->payment_method) }} &middot; {{ $payment->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                                {{ $payment->status === 'verified' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                                   ($payment->status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400' :
                                   'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Bank accounts + info --}}
        <div class="space-y-4">
            <div class="bg-[#0a66c2]/5 dark:bg-[#0a66c2]/10 border border-[#0a66c2]/20 rounded-xl p-4 sm:p-5">
                <p class="text-xs font-semibold text-[#0a66c2] uppercase tracking-wider mb-3">Rekening Perusahaan</p>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Bank BCA</p>
                        <p class="text-base font-bold text-gray-900 dark:text-white tracking-widest font-mono">1234567890</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">PT Bizmark Indonesia</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Bank Mandiri</p>
                        <p class="text-base font-bold text-gray-900 dark:text-white tracking-widest font-mono">0987654321</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">PT Bizmark Indonesia</p>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <p class="text-xs font-semibold text-amber-700 dark:text-amber-300 flex items-center gap-1.5 mb-2">
                    <i class="fas fa-info-circle"></i> Informasi Penting
                </p>
                <ul class="text-xs text-gray-700 dark:text-gray-300 space-y-1.5 leading-relaxed">
                    <li class="flex gap-2"><i class="fas fa-check text-amber-500 flex-shrink-0 mt-0.5 text-[10px]"></i>Pastikan nominal transfer tepat</li>
                    <li class="flex gap-2"><i class="fas fa-check text-amber-500 flex-shrink-0 mt-0.5 text-[10px]"></i>Simpan bukti transfer asli</li>
                    <li class="flex gap-2"><i class="fas fa-check text-amber-500 flex-shrink-0 mt-0.5 text-[10px]"></i>Verifikasi 1–2 hari kerja</li>
                    <li class="flex gap-2"><i class="fas fa-check text-amber-500 flex-shrink-0 mt-0.5 text-[10px]"></i>Hubungi CS jika ada kendala</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
async function processPayment() {
    const paymentType = document.getElementById('payment_type').value;
    try {
        const response = await window.apiFetch('{{ route("client.payments.initiate", $application->id) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ payment_type: paymentType })
        });
        const data = await response.json();
        if (data.success) {
            snap.pay(data.snap_token, {
                onSuccess: () => window.location.href = '{{ route("client.payments.success", [$application->id, "__ID__"]) }}'.replace('__ID__', data.payment_id),
                onPending: () => window.location.href = '{{ route("client.applications.show", $application->id) }}',
                onError:   (r) => alert('Pembayaran gagal: ' + r.status_message),
                onClose:   () => {}
            });
        } else {
            alert('Error: ' + data.message);
        }
    } catch(e) {
        console.error(e);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}
</script>
