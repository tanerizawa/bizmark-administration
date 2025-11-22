{{-- Payments Tab Content --}}
<div class="space-y-5">
    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Total Pembayaran</p>
            <p class="text-3xl font-bold text-white">{{ $totalPayments }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua transaksi</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Pending</p>
            <p class="text-3xl font-bold" style="color: rgba(255,159,10,1);">{{ $pendingPayments }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu verifikasi</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Terverifikasi</p>
            <p class="text-3xl font-bold" style="color: rgba(52,199,89,1);">{{ $verifiedPayments }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sudah disetujui</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-2">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Nilai</p>
            <p class="text-2xl font-bold" style="color: rgba(10,132,255,1);">
                Rp {{ number_format($totalAmount/1000000, 1) }}M
            </p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pendapatan terverifikasi</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card-elevated rounded-apple-lg p-4">
        <form method="GET" action="{{ route('admin.permits.index') }}" class="space-y-3" data-auto-submit>
            <input type="hidden" name="tab" value="payments">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Referensi/nomor permohonan..." 
                           class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Status</label>
                    <select name="status" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                        <option value="">Semua Status</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Metode</label>
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
                <thead style="background-color: rgba(28,28,30,0.45);">
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
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
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
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                          style="background-color: rgba(255,159,10,0.15); color: rgba(255,159,10,1);">
                                        <i class="fas fa-clock mr-1"></i>Proses
                                    </span>
                                @elseif($payment->status == 'verified')
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                          style="background-color: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                                        <i class="fas fa-check-circle mr-1"></i>Verified
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                          style="background-color: rgba(255,59,48,0.15); color: rgba(255,59,48,1);">
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
                                           class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                           style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal); border: 1px solid rgba(90, 200, 250, 0.25);"
                                           title="Lihat Permohonan">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if($payment->payment_proof)
                                        <a href="{{ Storage::url($payment->payment_proof) }}" target="_blank"
                                           class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                           style="background-color: rgba(175,82,222,0.15); color: rgba(175,82,222,1); border: 1px solid rgba(175,82,222,0.25);"
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
                                    <i class="fas fa-money-check-alt text-6xl mb-6" style="color: rgba(235, 235, 245, 0.3);"></i>
                                    <h3 class="text-xl font-semibold mb-2" style="color: #FFFFFF;">Belum Ada Pembayaran</h3>
                                    <p class="mb-6" style="color: rgba(235, 235, 245, 0.6);">
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
        <div class="rounded-apple-lg px-4 py-3" style="background-color: #2C2C2E; border: 1px solid rgba(84, 84, 88, 0.65); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);">
            {{ $payments->appends(['tab' => 'payments'])->links('pagination::tailwind') }}
        </div>
    @endif
</div>
