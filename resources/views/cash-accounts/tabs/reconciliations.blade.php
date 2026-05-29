<div style="display:flex;flex-direction:column;gap:16px">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-teal) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-balance-scale" style="color:var(--apple-teal);font-size:0.72rem"></i></span>
            <div>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Rekonsiliasi Bank</h3>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:2px 0 0">Pencocokan saldo buku dengan laporan bank</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            @if(isset($pendingReconciliations) && $pendingReconciliations > 0)
            <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)">
                <i class="fas fa-clock" style="margin-right:4px"></i>{{ $pendingReconciliations }} Pending
            </span>
            @endif
            <a href="{{ route('reconciliations.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:var(--apple-teal);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-plus" style="font-size:0.72rem"></i>Rekonsiliasi Baru
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:16px">
        <div>
            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Akun Rekening</label>
            <select name="account_id" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none" onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                <option value="">Semua Akun</option>
                @if(isset($accounts))
                @foreach($accounts as $account)
                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->account_name }}</option>
                @endforeach
                @endif
            </select>
        </div>
        <div>
            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Status</label>
            <select name="status" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none" onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reconciled" {{ request('status') == 'reconciled' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div style="display:flex;gap:8px">
            <button type="submit" style="padding:8px 16px;border:none;border-radius:9px;background:var(--apple-teal);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-filter" style="margin-right:5px"></i>Filter
            </button>
        </div>
    </form>

    {{-- Table --}}
    @if(isset($reconciliations) && count($reconciliations) > 0)
    <div style="overflow-x:auto;border-radius:12px;border:1px solid var(--dark-separator)">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:var(--dark-bg-tertiary)">
                <tr>
                    @foreach(['Akun','Periode','Saldo Buku','Saldo Bank','Selisih','Status','Aksi'] as $h)
                    <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:left;border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($reconciliations as $recon)
                @php
                    $diff = ($recon->bank_balance ?? 0) - ($recon->book_balance ?? 0);
                    $statusMap = ['draft' => ['Draft', 'var(--dark-text-secondary)'], 'pending' => ['Pending', 'var(--apple-orange)'], 'reconciled' => ['Selesai', 'var(--apple-green)']];
                    $statusInfo = $statusMap[$recon->status ?? ''] ?? ['Unknown', 'var(--dark-text-secondary)'];
                @endphp
                <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:12px 14px">
                        <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $recon->cashAccount->account_name ?? '–' }}</p>
                        @if($recon->cashAccount && $recon->cashAccount->bank_name)
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:2px 0 0">{{ $recon->cashAccount->bank_name }}</p>
                        @endif
                    </td>
                    <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-primary);white-space:nowrap">
                        {{ \Carbon\Carbon::parse($recon->period_start)->format('d M') }} – {{ \Carbon\Carbon::parse($recon->period_end)->format('d M Y') }}
                    </td>
                    <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-primary);text-align:right;white-space:nowrap">Rp {{ number_format($recon->book_balance ?? 0, 0, ',', '.') }}</td>
                    <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-primary);text-align:right;white-space:nowrap">Rp {{ number_format($recon->bank_balance ?? 0, 0, ',', '.') }}</td>
                    <td style="padding:12px 14px;font-size:0.82rem;text-align:right;white-space:nowrap;font-weight:700;color:{{ $diff == 0 ? 'var(--apple-green)' : 'var(--apple-red)' }}">
                        {{ $diff >= 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                    </td>
                    <td style="padding:12px 14px">
                        <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $statusInfo[1] }} 15%,transparent);color:{{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
                    </td>
                    <td style="padding:12px 14px">
                        <a href="{{ route('reconciliations.show', $recon) }}"
                           style="width:28px;height:28px;border-radius:7px;background:color-mix(in srgb,var(--apple-blue) 14%,transparent);color:var(--apple-blue);display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:0.75rem;border:1px solid color-mix(in srgb,var(--apple-blue) 22%,transparent)"
                           onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'" title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:48px 20px">
        <div style="width:52px;height:52px;border-radius:50%;background:color-mix(in srgb,var(--apple-teal) 12%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <i class="fas fa-balance-scale" style="color:var(--apple-teal);font-size:1.2rem"></i>
        </div>
        <p style="font-size:0.92rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Rekonsiliasi</p>
        <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 12px">Buat rekonsiliasi bank untuk mencocokkan saldo buku dengan laporan bank</p>
        <a href="{{ route('reconciliations.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;background:var(--apple-teal);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
            <i class="fas fa-plus" style="font-size:0.72rem"></i>Rekonsiliasi Pertama
        </a>
    </div>
    @endif
</div>
