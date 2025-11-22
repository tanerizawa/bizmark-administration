{{-- Dashboard Tab Content --}}
<div class="space-y-5">
    {{-- Focus Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4">
        <div class="card-elevated rounded-apple-lg p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Antrian Tinjauan</h3>
                <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(255,149,0,0.18); color: rgba(255,149,0,0.9);">
                    @php
                        $reviewRatio = $totalApplications > 0 ? round(($pendingApplications / $totalApplications) * 100) : 0;
                    @endphp
                    {{ $reviewRatio }}%
                </span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $pendingApplications }}</p>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                Permohonan menunggu verifikasi admin. Prioritaskan yang telah diajukan dan sedang ditinjau.
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Jalur Penawaran</h3>
                <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(10,132,255,0.18); color: rgba(10,132,255,0.9);">Operasional</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $needQuotation }}</p>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                Permohonan tanpa penawaran harga. Percepat proses agar pendapatan tidak tertahan.
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">Pantauan Keuangan</h3>
                <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(191,90,242,0.18); color: rgba(191,90,242,0.9);">Aktif</span>
            </div>
            <p class="text-3xl font-bold text-white">{{ $pendingPayments }}</p>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                Pembayaran menunggu verifikasi. Lihat tab Pembayaran untuk detail lengkap.
            </p>
        </div>
    </div>

    {{-- Status Distribution --}}
    <div class="card-elevated rounded-apple-xl p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em]" style="color: rgba(235,235,245,0.5);">Distribusi Status</p>
                <h3 class="text-xl font-semibold text-white">Rekap Permohonan per Status</h3>
            </div>
            <span class="text-xs" style="color: rgba(235,235,245,0.65);">Total {{ $totalApplications }} permohonan</span>
        </div>
        
        @php
            $statusTotal = max(1, array_sum($applicationsByStatus->toArray() ?? []));
        @endphp
        
        <div class="space-y-4">
            @forelse($applicationsByStatus as $status => $count)
                <div>
                    <div class="flex items-center justify-between text-sm" style="color: rgba(235,235,245,0.8);">
                        <span>{{ ucfirst(str_replace('_',' ', $status)) }}</span>
                        <span class="font-semibold text-white">{{ $count }}</span>
                    </div>
                    <div class="mt-1 h-2 rounded-full bg-white/10">
                        <div class="h-full rounded-full bg-gradient-to-r from-apple-blue to-apple-green" 
                             style="width: {{ min(100, ($count / $statusTotal) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada data status.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Applications --}}
    <div class="card-elevated rounded-apple-xl p-6 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Aktivitas Terbaru</p>
                <h2 class="text-xl font-semibold text-white">Permohonan Terbaru</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">10 permohonan terakhir yang masuk ke sistem.</p>
            </div>
            <a href="{{ route('admin.permits.index', ['tab' => 'applications']) }}" class="btn-secondary-sm">
                Lihat Semua
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead style="background-color: rgba(28,28,30,0.45);">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Nomor</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Klien</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Jenis Izin</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($recentApplications as $app)
                        <tr class="hover-lift transition-apple cursor-pointer" onclick="window.location='{{ route('admin.permit-applications.show', $app->id) }}'">
                            <td class="px-4 py-2.5 text-sm font-medium text-dark-text-primary">
                                {{ $app->application_number }}
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-medium text-dark-text-primary">
                                    {{ $app->client->company_name ?? $app->client->name }}
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-sm text-dark-text-primary">
                                {{ $app->permitType->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                      style="background-color: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                                    {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-sm text-dark-text-secondary">
                                {{ $app->created_at->locale('id')->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm" style="color: rgba(235,235,245,0.65);">
                                Belum ada permohonan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
