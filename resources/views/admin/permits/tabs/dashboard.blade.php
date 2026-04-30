{{-- Dashboard Tab Content --}}
<div class="space-y-5">
    {{-- Focus Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-4">
        <x-ui.card variant="flat" padding="sm" class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Antrian Tinjauan</h3>
                <x-ui.badge variant="warning" size="sm">
                    @php
                        $reviewRatio = $totalApplications > 0 ? round(($pendingApplications / $totalApplications) * 100) : 0;
                    @endphp
                    {{ $reviewRatio }}%
                </x-ui.badge>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $pendingApplications }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Permohonan menunggu verifikasi admin. Prioritaskan yang telah diajukan dan sedang ditinjau.
            </p>
        </x-ui.card>

        <x-ui.card variant="flat" padding="sm" class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Jalur Penawaran</h3>
                <x-ui.badge variant="info" size="sm">Operasional</x-ui.badge>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $needQuotation }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Permohonan tanpa penawaran harga. Percepat proses agar pendapatan tidak tertahan.
            </p>
        </x-ui.card>

        <x-ui.card variant="flat" padding="sm" class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pantauan Keuangan</h3>
                <x-ui.badge variant="primary" size="sm">Aktif</x-ui.badge>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $pendingPayments }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Pembayaran menunggu verifikasi. Lihat tab Pembayaran untuk detail lengkap.
            </p>
        </x-ui.card>
    </div>

    {{-- Status Distribution --}}
    <x-ui.card variant="flat" padding="md" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400">Distribusi Status</p>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Rekap Permohonan per Status</h3>
            </div>
            <span class="text-xs text-gray-500 dark:text-gray-400">Total {{ $totalApplications }} permohonan</span>
        </div>
        
        @php
            $statusTotal = max(1, array_sum($applicationsByStatus->toArray() ?? []));
        @endphp
        
        <div class="space-y-4">
            @forelse($applicationsByStatus as $status => $count)
                @php
                    $pct = min(100, ($count / $statusTotal) * 100);
                @endphp
                <div>
                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>{{ ucfirst(str_replace('_',' ', $status)) }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                    </div>
                    <div class="mt-1 h-2 rounded-full bg-gray-200 dark:bg-white/10" role="progressbar" aria-valuenow="{{ $count }}" aria-valuemin="0" aria-valuemax="{{ $statusTotal }}">
                        <div class="h-full rounded-full bg-gradient-to-r from-[var(--apple-blue)] to-[var(--apple-green)] transition-all duration-500"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada data status.</p>
            @endforelse
        </div>
    </x-ui.card>

    {{-- Recent Applications --}}
    <x-ui.card variant="flat" padding="md" class="space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-gray-500 dark:text-gray-400">Aktivitas Terbaru</p>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Permohonan Terbaru</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">10 permohonan terakhir yang masuk ke sistem.</p>
            </div>
            <x-ui.button variant="outline" size="sm" :href="route('admin.permits.index', ['tab' => 'applications'])">
                Lihat Semua
            </x-ui.button>
        </div>
        
        <x-ui.table
            :columns="[
                ['key' => 'number', 'label' => 'Nomor'],
                ['key' => 'client', 'label' => 'Klien'],
                ['key' => 'permit_type', 'label' => 'Jenis Izin'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'date', 'label' => 'Tanggal'],
            ]"
            :rows="$recentApplications"
            :striped="true"
            :hoverable="true"
            variant="compact"
            empty-message="Belum ada permohonan"
        >
            {{-- Application number --}}
            <x-slot:cell-number="{ row }">
                <span class="text-sm font-medium text-gray-900 dark:text-white cursor-pointer"
                      @click="window.location='{{ route('admin.permit-applications.show', $row->id) }}'">
                    {{ $row->application_number }}
                </span>
            </x-slot:cell-number>

            {{-- Client --}}
            <x-slot:cell-client="{ row }">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $row->client->company_name ?? $row->client->name }}
                </span>
            </x-slot:cell-client>

            {{-- Permit type --}}
            <x-slot:cell-permit_type="{ row }">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ $row->permitType->name ?? 'N/A' }}
                </span>
            </x-slot:cell-permit_type>

            {{-- Status --}}
            <x-slot:cell-status="{ row }">
                @php
                    $statusVariant = match($row->status) {
                        'draft' => 'neutral',
                        'submitted', 'under_review' => 'warning',
                        'document_incomplete' => 'danger',
                        'quoted', 'quotation_accepted', 'payment_verified', 'completed' => 'success',
                        'payment_pending' => 'warning',
                        'in_progress' => 'info',
                        default => 'neutral',
                    };
                @endphp
                <x-ui.badge :variant="$statusVariant" size="sm">
                    {{ ucfirst(str_replace('_', ' ', $row->status)) }}
                </x-ui.badge>
            </x-slot:cell-status>

            {{-- Date --}}
            <x-slot:cell-date="{ row }">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $row->created_at->locale('id')->diffForHumans() }}
                </span>
            </x-slot:cell-date>
        </x-ui.table>
    </x-ui.card>
</div>
