{{-- Bank Reconciliations Tab Content --}}
<div class="space-y-5">
    {{-- Header with Actions --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-xl font-semibold text-white">Rekonsiliasi Bank</h3>
            <p class="text-sm text-dark-text-secondary">
                Kelola dan pantau rekonsiliasi transaksi bank
            </p>
        </div>
        <a href="{{ route('reconciliations.create') }}"
           class="admin-btn admin-btn-sm rounded bg-apple-green/25">
            <i class="fas fa-plus mr-1.5"></i>Rekonsiliasi Baru
        </a>
    </div>

    {{-- Filters --}}
    <div class="card-elevated rounded-apple p-3">
        <form method="GET" action="{{ route('cash-accounts.index') }}" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="tab" value="reconciliations">
            <div class="flex-1 min-w-[150px]">
                <label class="admin-label-compact block mb-1">Akun</label>
                <select name="recon_account" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua Akun</option>
                    @foreach($accounts ?? [] as $acc)
                        <option value="{{ $acc->id }}" {{ request('recon_account') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="admin-label-compact block mb-1">Status</label>
                <select name="recon_status" class="admin-input admin-select w-full rounded bg-dark-bg-secondary border border-dark-separator text-white">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('recon_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('recon_status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="discrepancy" {{ request('recon_status') == 'discrepancy' ? 'selected' : '' }}>Selisih</option>
                </select>
            </div>
            <button type="submit" class="admin-btn admin-btn-sm rounded bg-apple-blue/25">
                <i class="fas fa-filter"></i>
            </button>
        </form>
    </div>

    {{-- Reconciliations Table --}}
    @if(isset($reconciliations) && $reconciliations->count() > 0)
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-dark-bg-secondary">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Akun</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Saldo Buku</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Saldo Bank</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Selisih</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-dark-bg-secondary">
                    @foreach($reconciliations as $recon)
                    <tr class="hover:bg-dark-bg-tertiary transition-apple">
                        <td class="px-4 py-3">
                            <div class="space-y-0.5">
                                <p class="text-sm font-medium text-white">{{ $recon->reconciliation_date->format('d M Y') }}</p>
                                <p class="text-xs text-dark-text-tertiary">{{ $recon->reconciliation_date->diffForHumans() }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-white">{{ $recon->cashAccount->account_name ?? '-' }}</p>
                            <p class="text-xs text-dark-text-secondary">{{ $recon->cashAccount->bank_name ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="text-sm font-mono text-white">Rp {{ number_format($recon->book_balance ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="text-sm font-mono text-white">Rp {{ number_format($recon->bank_balance ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @php
                                $diff = ($recon->bank_balance ?? 0) - ($recon->book_balance ?? 0);
                            @endphp
                            <span class="text-sm font-mono {{ $diff != 0 ? 'text-apple-red' : 'text-apple-green' }}">
                                Rp {{ number_format(abs($diff), 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-apple-orange/15 text-apple-orange',
                                    'completed' => 'bg-apple-green/15 text-apple-green',
                                    'discrepancy' => 'bg-apple-red/15 text-apple-red',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-apple {{ $statusClasses[$recon->status] ?? $statusClasses['pending'] }}">
                                {{ ucfirst($recon->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('reconciliations.show', $recon) }}"
                               class="inline-flex items-center px-2 py-1 rounded text-xs bg-apple-blue/15 text-apple-blue">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($reconciliations->hasPages())
        <div class="px-4 py-3 bg-dark-bg-tertiary border-t border-dark-separator">
            {{ $reconciliations->appends(['tab' => 'reconciliations'])->links() }}
        </div>
        @endif
    </div>
    @else
    {{-- Empty State --}}
    <div class="card-elevated rounded-apple-lg p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center bg-apple-blue/15">
            <i class="fas fa-balance-scale text-2xl text-apple-blue"></i>
        </div>
        <h3 class="text-lg font-semibold text-white mb-2">Belum Ada Rekonsiliasi</h3>
        <p class="text-sm text-dark-text-secondary mb-4">
            Mulai rekonsiliasi untuk memastikan catatan buku sesuai dengan mutasi bank.
        </p>
        <a href="{{ route('reconciliations.create') }}"
           class="admin-btn rounded px-4 py-2 bg-apple-green/25">
            <i class="fas fa-plus mr-1.5"></i>Rekonsiliasi Pertama
        </a>
    </div>
    @endif
</div>
