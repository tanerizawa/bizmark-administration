@php
    $groupedTransactions = collect($recentTransactions)->groupBy('date');
    $totalInflow = collect($recentTransactions)->where('type', 'income')->sum('amount');
    $totalOutflow = collect($recentTransactions)->where('type', 'expense')->sum('amount');
    $totalKasbon = collect($recentTransactions)->where('type', 'kasbon')->sum('amount');
@endphp

<div style="display:flex;flex-direction:column;gap:16px">
    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:8px">
        <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-project-diagram" style="color:var(--apple-purple);font-size:0.72rem"></i></span>
        <div>
            <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Transaksi Proyek</h3>
            <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:2px 0 0">Timeline transaksi terkait proyek dalam periode ini</p>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,var(--apple-green) 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-arrow-down" style="color:var(--apple-green);font-size:0.82rem"></i></div>
            <div>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 2px;text-transform:uppercase;letter-spacing:.06em">Kas Masuk</p>
                <p style="font-size:0.95rem;font-weight:800;color:var(--apple-green);margin:0">Rp {{ number_format($totalInflow / 1000000, 1) }}M</p>
            </div>
        </div>
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,var(--apple-red) 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-arrow-up" style="color:var(--apple-red);font-size:0.82rem"></i></div>
            <div>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 2px;text-transform:uppercase;letter-spacing:.06em">Kas Keluar</p>
                <p style="font-size:0.95rem;font-weight:800;color:var(--apple-red);margin:0">Rp {{ number_format($totalOutflow / 1000000, 1) }}M</p>
            </div>
        </div>
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px">
            <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,var(--apple-orange) 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-hand-holding-usd" style="color:var(--apple-orange);font-size:0.82rem"></i></div>
            <div>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0 0 2px;text-transform:uppercase;letter-spacing:.06em">Kasbon</p>
                <p style="font-size:0.95rem;font-weight:800;color:var(--apple-orange);margin:0">Rp {{ number_format($totalKasbon / 1000000, 1) }}M</p>
            </div>
        </div>
    </div>

    @if(count($recentTransactions) > 0)
    <div style="display:flex;flex-direction:column;gap:14px">
        @foreach($groupedTransactions as $date => $transactions)
        <div>
            {{-- Date divider --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <span style="font-size:0.72rem;font-weight:700;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;white-space:nowrap">{{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <div style="flex:1;height:1px;background:var(--dark-separator)"></div>
                <span style="font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);padding:2px 8px;border-radius:8px;white-space:nowrap">{{ count($transactions) }}</span>
            </div>
            {{-- Transactions --}}
            <div style="display:flex;flex-direction:column;gap:6px">
                @foreach($transactions as $tx)
                @php
                    $typeStyles = [
                        'income' => ['var(--apple-green)', 'fa-arrow-circle-down', '+'],
                        'expense' => ['var(--apple-red)', 'fa-arrow-circle-up', '-'],
                        'kasbon' => ['var(--apple-orange)', 'fa-hand-holding-usd', ''],
                        'kasbon_return' => ['var(--apple-blue)', 'fa-undo', '+'],
                    ];
                    $ts = $typeStyles[$tx['type']] ?? ['var(--dark-text-secondary)', 'fa-circle', ''];
                @endphp
                <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;border-radius:10px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-left:3px solid {{ $ts[0] }}"
                     onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='var(--dark-bg-secondary)'">
                    <div style="width:30px;height:30px;border-radius:8px;background:color-mix(in srgb,{{ $ts[0] }} 16%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas {{ $ts[1] }}" style="color:{{ $ts[0] }};font-size:0.8rem"></i></div>
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
                            <div style="flex:1;min-width:0">
                                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $tx['description'] }}</p>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                    @if($tx['project_id'])
                                    <a href="{{ url('/admin/projects/' . $tx['project_id']) }}"
                                       style="font-size:0.72rem;color:var(--apple-blue);text-decoration:none;display:inline-flex;align-items:center;gap:3px"
                                       onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-folder" style="font-size:0.65rem"></i>{{ $tx['project_name'] }}
                                    </a>
                                    @endif
                                    @if($tx['account_name'])
                                    <span style="font-size:0.72rem;color:var(--dark-text-secondary)"><i class="fas fa-university" style="margin-right:3px"></i>{{ $tx['account_name'] }}</span>
                                    @endif
                                </div>
                                @if($tx['notes'])<p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:3px 0 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $tx['notes'] }}</p>@endif
                            </div>
                            <div style="text-align:right;flex-shrink:0">
                                <p style="font-size:0.92rem;font-weight:800;color:{{ $ts[0] }};margin:0 0 2px;white-space:nowrap">{{ $ts[2] }}Rp {{ number_format($tx['amount'], 0, ',', '.') }}</p>
                                @if(isset($tx['balance_after']))
                                <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;white-space:nowrap">Saldo: Rp {{ number_format($tx['balance_after'], 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:48px 20px">
        <div style="width:52px;height:52px;border-radius:50%;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
            <i class="fas fa-project-diagram" style="color:var(--apple-purple);font-size:1.2rem"></i>
        </div>
        <p style="font-size:0.92rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Transaksi Proyek</p>
        <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">Tidak ada transaksi proyek dalam periode yang dipilih</p>
    </div>
    @endif
</div>
