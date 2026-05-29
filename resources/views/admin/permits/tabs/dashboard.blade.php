{{-- Dashboard Tab --}}
@php
    $statusLabels = [
        'draft'               => ['Draf',             'var(--dark-text-secondary)'],
        'submitted'           => ['Diajukan',          'var(--apple-blue)'],
        'under_review'        => ['Ditinjau',          'var(--apple-orange)'],
        'document_incomplete' => ['Dok. Kurang',       'var(--apple-red)'],
        'quoted'              => ['Penawaran',         'var(--apple-yellow)'],
        'quotation_accepted'  => ['Penawaran OK',      'var(--apple-yellow)'],
        'payment_pending'     => ['Tunggu Bayar',      'var(--apple-orange)'],
        'payment_verified'    => ['Bayar OK',          'var(--apple-green)'],
        'in_progress'         => ['Proses',            'var(--apple-teal)'],
        'completed'           => ['Selesai',           'var(--apple-green)'],
        'cancelled'           => ['Dibatalkan',        'var(--apple-red)'],
    ];
    $statusTotal = max(1, $applicationsByStatus->sum());
@endphp

<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Focus Cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        @php
            $focusCards = [
                ['title'=>'Antrian Tinjauan', 'value'=>$pendingApplications, 'sub'=>'Permohonan menunggu verifikasi admin. Prioritaskan yang sudah diajukan.', 'color'=>'var(--apple-orange)', 'badge'=>($totalApplications > 0 ? round(($pendingApplications/$totalApplications)*100) : 0).'%', 'badgeLabel'=>'dari total'],
                ['title'=>'Jalur Penawaran',  'value'=>$needQuotation,       'sub'=>'Permohonan tanpa penawaran harga. Percepat agar pendapatan tidak tertahan.', 'color'=>'var(--apple-blue)',   'badge'=>'Operasional', 'badgeLabel'=>null],
                ['title'=>'Pantauan Keuangan','value'=>$pendingPayments,     'sub'=>'Pembayaran menunggu verifikasi. Lihat tab Pembayaran untuk detail.', 'color'=>'var(--apple-green)', 'badge'=>'Aktif', 'badgeLabel'=>null],
            ];
        @endphp
        @foreach($focusCards as $card)
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $card['title'] }}</h3>
                <span style="padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;background:color-mix(in srgb,{{ $card['color'] }} 18%,transparent);color:{{ $card['color'] }}">{{ $card['badge'] }}</span>
            </div>
            <p style="font-size:2rem;font-weight:800;color:{{ $card['color'] }};margin:0 0 8px;line-height:1">{{ $card['value'] }}</p>
            <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0;line-height:1.4">{{ $card['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Status Distribution + Recent Applications --}}
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px">

        {{-- Status Distribution --}}
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                <div>
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Distribusi Status</p>
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Rekap per Status</h3>
                </div>
                <span style="font-size:0.7rem;color:var(--dark-text-secondary)">{{ $totalApplications }} total</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
                @forelse($applicationsByStatus as $status => $count)
                @php
                    $pct = min(100, ($count / $statusTotal) * 100);
                    $sColor = $statusLabels[$status][1] ?? 'var(--dark-text-secondary)';
                    $sLabel = $statusLabels[$status][0] ?? ucfirst(str_replace('_',' ',$status));
                @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:0.75rem;margin-bottom:4px">
                        <span style="color:var(--dark-text-secondary)">{{ $sLabel }}</span>
                        <span style="font-weight:600;color:var(--dark-text-primary)">{{ $count }}</span>
                    </div>
                    <div style="height:5px;border-radius:3px;overflow:hidden;background:color-mix(in srgb,{{ $sColor }} 12%,var(--dark-bg-secondary))">
                        <div style="height:100%;width:{{ $pct }}%;background:{{ $sColor }};border-radius:3px;transition:width .4s ease"></div>
                    </div>
                </div>
                @empty
                <p style="font-size:0.78rem;color:var(--dark-text-secondary)">Belum ada data.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Applications --}}
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
                <div>
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Aktivitas Terbaru</p>
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">10 Permohonan Terakhir</h3>
                </div>
                <a href="{{ route('admin.permits.index', ['tab' => 'applications']) }}" style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:8px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.78rem;font-weight:600;text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Lihat Semua →</a>
            </div>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead style="background:var(--dark-bg-secondary)">
                        <tr>
                            @foreach(['Nomor','Klien','Jenis Izin','Status','Tanggal'] as $col)
                            <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:left;border-bottom:1px solid var(--dark-separator)">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $app)
                        @php
                            $sColor = $statusLabels[$app->status][1] ?? 'var(--dark-text-secondary)';
                            $sLabel = $statusLabels[$app->status][0] ?? ucfirst(str_replace('_',' ',$app->status));
                        @endphp
                        <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:11px 14px">
                                <a href="{{ route('admin.permit-applications.show', $app->id) }}" style="font-size:0.82rem;font-weight:600;color:var(--apple-blue);text-decoration:none">{{ $app->application_number }}</a>
                            </td>
                            <td style="padding:11px 14px;font-size:0.82rem;color:var(--dark-text-primary)">
                                {{ $app->client->company_name ?? $app->client->name ?? 'N/A' }}
                            </td>
                            <td style="padding:11px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">
                                {{ $app->permitType->name ?? 'N/A' }}
                            </td>
                            <td style="padding:11px 14px">
                                <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sColor }} 15%,transparent);color:{{ $sColor }}">{{ $sLabel }}</span>
                            </td>
                            <td style="padding:11px 14px;font-size:0.78rem;color:var(--dark-text-secondary)">
                                {{ optional($app->created_at)->locale('id')->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding:40px;text-align:center;color:var(--dark-text-secondary);font-size:0.85rem">Belum ada permohonan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
