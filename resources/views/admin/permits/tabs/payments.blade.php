{{-- Payments Tab Content --}}
<div class="space-y-5">
    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest text-dark-text-secondary">Total Pembayaran</p>
            <p class="text-xl font-bold text-white">{{ $totalPayments }}</p>
            <p class="text-xs text-dark-text-secondary">Semua transaksi</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest text-apple-orange/90">Pending</p>
            <p class="text-xl font-bold text-apple-orange">{{ $pendingPayments }}</p>
            <p class="text-xs text-dark-text-secondary">Perlu verifikasi</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest text-apple-green/90">Terverifikasi</p>
            <p class="text-xl font-bold text-apple-green">{{ $verifiedPayments }}</p>
            <p class="text-xs text-dark-text-secondary">Sudah disetujui</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest text-apple-blue/90">Total Nilai</p>
            <p class="text-2xl font-bold text-apple-blue">
                Rp {{ number_format($totalAmount/1000000, 1) }}M
            </p>
            <p class="text-xs text-dark-text-secondary">Pendapatan terverifikasi</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card-elevated rounded-apple-lg p-4">
        <form method="GET" action="{{ route('admin.permits.index') }}" class="space-y-3" data-auto-submit>
            <input type="hidden" name="tab" value="payments">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-dark-text-secondary">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Referensi/nomor permohonan..." 
                           class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-dark-text-secondary">Status</label>
                    <select name="status" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                        <option value="">Semua Status</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-dark-text-secondary">Metode</label>
                    <select name="payment_method" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                        <option value="">Semua Metode</option>
                        <option value="manual" {{ request('payment_method') == 'manual' ? 'selected' : '' }}>Transfer Manual</option>
                        <option value="midtrans" {{ request('payment_method') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary-sm flex-1">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.permits.index', ['tab' => 'payments']) }}" class="btn-secondary-sm flex-1">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead class="bg-[rgba(28,28,30,0.45)]">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Referensi</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Permohonan</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Klien</th>
                        <th scope="col" class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Jumlah</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Tanggal</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-dark-bg-secondary">
                    @forelse($payments as $payment)
                        <tr class="hover-lift transition-apple">
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-semibold text-dark-text-primary">{{ $payment->payment_reference }}</div>
                                <div class="text-xs text-dark-text-secondary mt-1">
                                    {{ ucfirst($payment->payment_method) }}
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                @if($payment->application)
                                    <div class="text-sm font-medium text-dark-text-primary">
                                        {{ $payment->application->application_number }}
                                    </div>
                                    <div class="text-xs text-dark-text-secondary mt-1">
                                        {{ $payment->application->permitType->name ?? 'N/A' }}
                                    </div>
                                @else
                                    <span class="text-xs text-dark-text-tertiary">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-sm text-dark-text-primary">
                                {{ $payment->application->client->company_name ?? $payment->application->client->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <div class="text-sm font-semibold text-white">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                @if($payment->status == 'processing')
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-orange/20 text-apple-orange">
                                        <i class="fas fa-clock mr-1"></i>Proses
                                    </span>
                                @elseif($payment->status == 'verified')
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-green/20 text-apple-green">
                                        <i class="fas fa-check-circle mr-1"></i>Verified
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-red/20 text-apple-red">
                                        <i class="fas fa-times-circle mr-1"></i>Gagal
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-sm text-dark-text-secondary">
                                    {{ $payment->payment_date ? $payment->payment_date->locale('id')->isoFormat('D MMM Y') : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    @if($payment->application)
                                        <a href="{{ route('admin.permit-applications.show', $payment->application->id) }}"
                                           class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple bg-apple-teal/20 text-apple-teal border border-apple-teal/30"
                                           title="Lihat Permohonan">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($payment->payment_proof)
                                        <a href="{{ Storage::url($payment->payment_proof) }}" target="_blank"
                                           class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple bg-[rgba(175,82,222,0.2)] text-[#AF52DE] border border-[rgba(175,82,222,0.3)]"
                                           title="Lihat Bukti">
                                            <i class="fas fa-file-image"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-money-check-alt text-4xl mb-6 text-dark-text-tertiary"></i>
                                    <h3 class="text-base font-semibold mb-2 text-white">Belum Ada Pembayaran</h3>
                                    <p class="mb-6 text-dark-text-secondary">
                                        Transaksi pembayaran akan muncul di sini
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
        <div class="rounded-apple-lg px-4 py-3 bg-dark-bg-tertiary border border-white/20 shadow-soft">
            {{ $payments->appends(['tab' => 'payments'])->links('pagination::tailwind') }}
        </div>
    @endif
</div>
