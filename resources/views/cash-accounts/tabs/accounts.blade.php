@php
    $accountTypeMap = [
        'bank' => ['Bank', 'var(--apple-blue)', 'fa-university'],
        'cash' => ['Kas Tunai', 'var(--apple-green)', 'fa-money-bill-wave'],
        'receivable' => ['Piutang', 'var(--apple-orange)', 'fa-file-invoice-dollar'],
        'payable' => ['Hutang', 'var(--apple-red)', 'fa-hand-holding-usd'],
    ];
@endphp

<div style="display:flex;flex-direction:column;gap:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-university" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
            <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Daftar Rekening & Kas</h3>
        </div>
        <a href="{{ route('cash-accounts.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:9px;background:var(--apple-blue);color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <i class="fas fa-plus" style="font-size:0.72rem"></i>Tambah Akun
        </a>
    </div>

    @if($accounts->isEmpty())
    <div style="text-align:center;padding:48px 20px">
        <div style="width:52px;height:52px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <i class="fas fa-university" style="color:var(--apple-blue);font-size:1.2rem"></i>
        </div>
        <p style="font-size:0.92rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Akun</p>
        <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 12px">Buat akun pertama untuk mulai mencatat keuangan</p>
        <a href="{{ route('cash-accounts.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
            <i class="fas fa-plus" style="font-size:0.72rem"></i>Tambah Akun Pertama
        </a>
    </div>
    @else
    <div style="overflow-x:auto;border-radius:12px;border:1px solid var(--dark-separator)">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:var(--dark-bg-tertiary)">
                <tr>
                    @foreach(['Akun','Tipe','Bank / No. Rekening','Saldo Saat Ini','Status','Aksi'] as $h)
                    <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:left;border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                @php
                    $typeInfo = $accountTypeMap[$account->account_type] ?? ['Lainnya', 'var(--dark-text-secondary)', 'fa-circle'];
                @endphp
                <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:12px 14px">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,{{ $typeInfo[1] }} 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas {{ $typeInfo[2] }}" style="color:{{ $typeInfo[1] }};font-size:0.8rem"></i></div>
                            <div>
                                <p style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $account->account_name }}</p>
                                @if($account->notes)<p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:2px 0 0;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $account->notes }}</p>@endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 14px">
                        <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $typeInfo[1] }} 15%,transparent);color:{{ $typeInfo[1] }}">{{ $typeInfo[0] }}</span>
                    </td>
                    <td style="padding:12px 14px">
                        <div>
                            @if($account->bank_name)<p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0">{{ $account->bank_name }}</p>@endif
                            @if($account->account_number)<p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:2px 0 0;font-family:monospace">{{ $account->account_number }}</p>@endif
                            @if(!$account->bank_name && !$account->account_number)<span style="font-size:0.78rem;color:var(--dark-text-secondary)">–</span>@endif
                        </div>
                    </td>
                    <td style="padding:12px 14px">
                        <p style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0;white-space:nowrap">{{ $account->formatted_balance }}</p>
                    </td>
                    <td style="padding:12px 14px">
                        @if($account->is_active)
                        <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)">Aktif</span>
                        @else
                        <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--dark-text-secondary) 15%,transparent);color:var(--dark-text-secondary)">Non-aktif</span>
                        @endif
                    </td>
                    <td style="padding:12px 14px">
                        <div style="display:flex;align-items:center;gap:6px">
                            <a href="{{ route('cash-accounts.show', $account) }}"
                               title="Lihat Mutasi"
                               style="width:28px;height:28px;border-radius:7px;background:color-mix(in srgb,var(--apple-blue) 14%,transparent);color:var(--apple-blue);display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:0.75rem;border:1px solid color-mix(in srgb,var(--apple-blue) 22%,transparent)"
                               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('cash-accounts.edit', $account) }}"
                               title="Edit"
                               style="width:28px;height:28px;border-radius:7px;background:color-mix(in srgb,var(--apple-orange) 14%,transparent);color:var(--apple-orange);display:inline-flex;align-items:center;justify-content:center;text-decoration:none;font-size:0.75rem;border:1px solid color-mix(in srgb,var(--apple-orange) 22%,transparent)"
                               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('cash-accounts.destroy', $account) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus akun {{ addslashes($account->account_name) }}? Semua data transaksi terkait mungkin terpengaruh.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Hapus"
                                        style="width:28px;height:28px;border-radius:7px;background:color-mix(in srgb,var(--apple-red) 14%,transparent);color:var(--apple-red);display:inline-flex;align-items:center;justify-content:center;font-size:0.75rem;border:1px solid color-mix(in srgb,var(--apple-red) 22%,transparent);cursor:pointer"
                                        onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalBalance = $accounts->sum('current_balance');
        $activeCount = $accounts->where('is_active', true)->count();
        $bankCount = $accounts->where('account_type', 'bank')->count();
    @endphp
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-wallet" style="color:var(--apple-blue);font-size:0.82rem"></i></div>
            <div>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 2px;text-transform:uppercase;letter-spacing:.06em">Total Saldo</p>
                <p style="font-size:1rem;font-weight:800;color:var(--dark-text-primary);margin:0">Rp {{ number_format($totalBalance / 1000000, 1) }}M</p>
            </div>
        </div>
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,var(--apple-green) 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.82rem"></i></div>
            <div>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 2px;text-transform:uppercase;letter-spacing:.06em">Akun Aktif</p>
                <p style="font-size:1rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ $activeCount }} / {{ $accounts->count() }}</p>
            </div>
        </div>
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,var(--apple-teal) 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-university" style="color:var(--apple-teal);font-size:0.82rem"></i></div>
            <div>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 2px;text-transform:uppercase;letter-spacing:.06em">Rekening Bank</p>
                <p style="font-size:1rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ $bankCount }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
