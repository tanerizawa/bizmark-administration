{{-- Payments Tab --}}
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Quick Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
        @php
            $payStats = [
                ['label'=>'Total Pembayaran', 'value'=>$totalPayments,   'sub'=>'Semua transaksi',         'color'=>'var(--dark-text-primary)'],
                ['label'=>'Pending',          'value'=>$pendingPayments, 'sub'=>'Perlu verifikasi',        'color'=>$pendingPayments > 0 ? 'var(--apple-orange)' : 'var(--apple-green)'],
                ['label'=>'Terverifikasi',    'value'=>$verifiedPayments,'sub'=>'Sudah disetujui',         'color'=>'var(--apple-green)'],
                ['label'=>'Total Nilai',      'value'=>'Rp '.number_format($totalAmount/1000000,1).'M', 'sub'=>'Pendapatan terverifikasi', 'color'=>'var(--apple-blue)'],
            ];
        @endphp
        @foreach($payStats as $s)
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};margin:0;opacity:.85">{{ $s['label'] }}</p>
            <p style="font-size:1.5rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter Form --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <form method="GET" action="{{ route('admin.permits.index') }}">
            <input type="hidden" name="tab" value="payments">
            <div style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;align-items:end">
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Referensi / nomor permohonan..."
                           style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Status</label>
                    <select name="status" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Status</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="verified"   {{ request('status') == 'verified'   ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="failed"     {{ request('status') == 'failed'     ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Metode</label>
                    <select name="payment_method" style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:0.85rem;width:100%;outline:none">
                        <option value="">Semua Metode</option>
                        <option value="manual"   {{ request('payment_method') == 'manual'   ? 'selected' : '' }}>Transfer Manual</option>
                        <option value="midtrans" {{ request('payment_method') == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                    </select>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:8px 16px;border-radius:8px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer">
                        <i class="fas fa-filter" style="font-size:0.7rem"></i>Filter
                    </button>
                    <a href="{{ route('admin.permits.index', ['tab' => 'payments']) }}" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none">
                        <i class="fas fa-times" style="font-size:0.75rem"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:var(--dark-bg-secondary)">
                    <tr>
                        @foreach(['Referensi','Permohonan','Klien','Jumlah','Status','Tanggal','Aksi'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ in_array($col,['Jumlah','Status','Aksi']) ? ($col==='Jumlah'?'right':'center') : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    @php
                        $pColor = match($payment->status) { 'processing' => 'var(--apple-orange)', 'verified' => 'var(--apple-green)', default => 'var(--apple-red)' };
                        $pIcon  = match($payment->status) { 'processing' => 'fa-clock', 'verified' => 'fa-check-circle', default => 'fa-times-circle' };
                        $pLabel = match($payment->status) { 'processing' => 'Proses', 'verified' => 'Verified', default => 'Gagal' };
                    @endphp
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $payment->payment_reference }}</span>
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);display:block;margin-top:2px">{{ ucfirst($payment->payment_method) }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            @if($payment->application)
                            <span style="font-size:0.82rem;font-weight:500;color:var(--dark-text-primary);display:block">{{ $payment->application->application_number }}</span>
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);display:block;margin-top:2px">{{ $payment->application->permitType->name ?? 'N/A' }}</span>
                            @else
                            <span style="font-size:0.78rem;color:var(--dark-text-secondary);opacity:.5">N/A</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">
                            {{ $payment->application->client->company_name ?? $payment->application->client->name ?? 'N/A' }}
                        </td>
                        <td style="padding:12px 14px;text-align:right">
                            <span style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary)">Rp {{ number_format($payment->amount,0,',','.') }}</span>
                        </td>
                        <td style="padding:12px 14px;text-align:center;white-space:nowrap">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $pColor }} 15%,transparent);color:{{ $pColor }}">
                                <i class="fas {{ $pIcon }}" style="font-size:0.6rem"></i>{{ $pLabel }}
                            </span>
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">
                            {{ $payment->payment_date ? optional($payment->payment_date)->locale('id')->isoFormat('D MMM Y') : '—' }}
                        </td>
                        <td style="padding:12px 14px;text-align:center;white-space:nowrap">
                            <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                                @if($payment->application)
                                <a href="{{ route('admin.permit-applications.show', $payment->application->id) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);color:var(--apple-teal);text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-teal) 30%,transparent)" title="Lihat Permohonan" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-eye" style="font-size:0.7rem"></i></a>
                                @endif
                                @if($payment->payment_proof)
                                <a href="{{ Storage::url($payment->payment_proof) }}" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple);text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-purple) 30%,transparent)" title="Bukti Bayar" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1"><i class="fas fa-file-image" style="font-size:0.7rem"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:48px;text-align:center">
                            <i class="fas fa-money-check-alt" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:12px"></i>
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Pembayaran</p>
                            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Transaksi pembayaran akan muncul di sini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($payments->hasPages())
    <div style="padding:14px 20px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px">
        <x-ui.pagination :paginator="$payments->appends(array_merge(request()->all(), ['tab'=>'payments']))" variant="full" :show-info="true" />
    </div>
    @endif

</div>
