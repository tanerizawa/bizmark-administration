<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px">
            <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-chart-line" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
            <div>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Laporan Arus Kas (PSAK 2)</h3>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:2px 0 0">{{ \Carbon\Carbon::parse($cashFlowStatement['period_start'])->isoFormat('D MMM Y') }} – {{ \Carbon\Carbon::parse($cashFlowStatement['period_end'])->isoFormat('D MMM Y') }}</p>
            </div>
        </div>
        <span style="display:inline-flex;padding:3px 12px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 14%,transparent);color:var(--apple-blue)">PSAK 2 — Arus Kas</span>
    </div>

    @php
        $netOperating = $cashFlowStatement['net_operating'] ?? 0;
        $netFinancing = $cashFlowStatement['net_financing'] ?? 0;
        $netChange = $cashFlowStatement['net_change'] ?? 0;
    @endphp

    {{-- Operating Activities --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;gap:8px">
                <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-cogs" style="color:var(--apple-green);font-size:0.72rem"></i></span>
                <h4 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Aktivitas Operasi</h4>
            </div>
            <span style="font-size:0.82rem;font-weight:800;color:{{ $netOperating >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }}">{{ $netOperating >= 0 ? '+' : '' }}Rp {{ number_format($netOperating, 0, ',', '.') }}</span>
        </div>
        <div style="padding:16px 18px;display:flex;flex-direction:column;gap:8px">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:9px;background:var(--dark-bg-tertiary)">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="fas fa-arrow-circle-down" style="color:var(--apple-green);font-size:0.8rem"></i>
                    <span style="font-size:0.82rem;color:var(--dark-text-primary)">Penerimaan Operasional</span>
                    <span style="font-size:0.72rem;color:var(--dark-text-secondary)">(Pembayaran Proyek & Jasa)</span>
                </div>
                <span style="font-size:0.85rem;font-weight:700;color:var(--apple-green)">Rp {{ number_format($cashFlowStatement['operating_receipts'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:9px;background:var(--dark-bg-tertiary)">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="fas fa-arrow-circle-up" style="color:var(--apple-red);font-size:0.8rem"></i>
                    <span style="font-size:0.82rem;color:var(--dark-text-primary)">Pembayaran Operasional</span>
                    <span style="font-size:0.72rem;color:var(--dark-text-secondary)">(Biaya Proyek & Operasional)</span>
                </div>
                <span style="font-size:0.85rem;font-weight:700;color:var(--apple-red)">(Rp {{ number_format($cashFlowStatement['operating_payments'] ?? 0, 0, ',', '.') }})</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:9px;background:color-mix(in srgb,{{ $netOperating >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }} 8%,transparent);border:1px solid color-mix(in srgb,{{ $netOperating >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }} 16%,transparent)">
                <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Arus Kas Bersih Operasional</span>
                <span style="font-size:0.88rem;font-weight:800;color:{{ $netOperating >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }}">{{ $netOperating >= 0 ? '+' : '' }}Rp {{ number_format($netOperating, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Financing Activities --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;gap:8px">
                <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-handshake" style="color:var(--apple-orange);font-size:0.72rem"></i></span>
                <h4 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Aktivitas Pendanaan (Kasbon)</h4>
            </div>
            <span style="font-size:0.82rem;font-weight:800;color:{{ $netFinancing >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }}">{{ $netFinancing >= 0 ? '+' : '' }}Rp {{ number_format($netFinancing, 0, ',', '.') }}</span>
        </div>
        <div style="padding:16px 18px;display:flex;flex-direction:column;gap:8px">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:9px;background:var(--dark-bg-tertiary)">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="fas fa-minus-circle" style="color:var(--apple-orange);font-size:0.8rem"></i>
                    <span style="font-size:0.82rem;color:var(--dark-text-primary)">Kasbon Diberikan</span>
                </div>
                <span style="font-size:0.85rem;font-weight:700;color:var(--apple-orange)">(Rp {{ number_format($cashFlowStatement['kasbon_given'] ?? 0, 0, ',', '.') }})</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:9px;background:var(--dark-bg-tertiary)">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="fas fa-plus-circle" style="color:var(--apple-blue);font-size:0.8rem"></i>
                    <span style="font-size:0.82rem;color:var(--dark-text-primary)">Kasbon Diterima Kembali</span>
                </div>
                <span style="font-size:0.85rem;font-weight:700;color:var(--apple-blue)">Rp {{ number_format($cashFlowStatement['kasbon_received'] ?? 0, 0, ',', '.') }}</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:9px;background:color-mix(in srgb,{{ $netFinancing >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }} 8%,transparent);border:1px solid color-mix(in srgb,{{ $netFinancing >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }} 16%,transparent)">
                <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Arus Kas Bersih Pendanaan</span>
                <span style="font-size:0.88rem;font-weight:800;color:{{ $netFinancing >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }}">{{ $netFinancing >= 0 ? '+' : '' }}Rp {{ number_format($netFinancing, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid color-mix(in srgb,var(--apple-blue) 28%,var(--dark-separator));border-radius:14px;overflow:hidden">
        <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator));background:color-mix(in srgb,var(--apple-blue) 6%,transparent)">
            <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 20%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-balance-scale" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
            <h4 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Ringkasan Posisi Kas</h4>
        </div>
        <div style="padding:16px 18px;display:flex;flex-direction:column;gap:8px">
            @foreach([['Kas & Setara Kas Awal Periode', $cashFlowStatement['cash_beginning'] ?? 0, 'var(--dark-text-secondary)', false],['Kenaikan (Penurunan) Bersih Kas', $netChange, $netChange >= 0 ? 'var(--apple-green)' : 'var(--apple-red)', true]] as $row)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:9px;background:var(--dark-bg-tertiary)">
                <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ $row[0] }}</span>
                <span style="font-size:0.85rem;font-weight:700;color:{{ $row[2] }}">{{ $row[3] && $row[1] >= 0 ? '+' : '' }}Rp {{ number_format($row[1], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-radius:11px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent)">
                <span style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary)">Kas & Setara Kas Akhir Periode</span>
                <span style="font-size:1rem;font-weight:900;color:var(--apple-blue)">Rp {{ number_format($cashFlowStatement['cash_ending'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;text-align:center">
        <i class="fas fa-info-circle" style="margin-right:4px"></i>Laporan disusun sesuai PSAK 2 — Laporan Arus Kas, menggunakan metode langsung (direct method).
    </p>
</div>
