{{-- Service Cost Requests Tab Content --}}
@php
    $stats = $serviceCostRequestsStats ?? [
        'total' => 0,
        'pending' => 0,
        'reviewing' => 0,
        'quoted' => 0,
        'accepted' => 0,
        'rejected' => 0,
        'this_week' => 0,
        'this_month' => 0,
    ];
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-lg font-bold text-white mb-1">{{ $stats['total'] }}</div>
        <div class="text-xs text-dark-text-secondary">Total</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $stats['pending'] }}</div>
        <div class="text-xs text-dark-text-secondary">Pending</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-blue-400 mb-1">{{ $stats['reviewing'] }}</div>
        <div class="text-xs text-dark-text-secondary">Reviewing</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-indigo-400 mb-1">{{ $stats['quoted'] }}</div>
        <div class="text-xs text-dark-text-secondary">Quoted</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-green-400 mb-1">{{ $stats['accepted'] }}</div>
        <div class="text-xs text-dark-text-secondary">Accepted</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-red-400 mb-1">{{ $stats['rejected'] }}</div>
        <div class="text-xs text-dark-text-secondary">Rejected</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-orange-400 mb-1">{{ $stats['this_week'] }}</div>
        <div class="text-xs text-dark-text-secondary">Minggu Ini</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-purple-400 mb-1">{{ $stats['this_month'] }}</div>
        <div class="text-xs text-dark-text-secondary">Bulan Ini</div>
    </div>
</div>

<div class="card-elevated rounded-apple-lg mb-4">
    <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
        <h3 class="text-base font-semibold text-white">Pencarian & Filter</h3>
    </div>
    <div class="p-4">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="hidden" name="tab" value="service-cost-requests">
            <div class="md:col-span-2">
                <input type="text"
                       name="search"
                       class="w-full px-3 py-2 rounded-apple text-sm"
                       placeholder="Cari nomor permohonan, nama, email, telepon..."
                       value="{{ request('search') }}"
                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
            </div>
            <div>
                <select name="status"
                        class="w-full px-3 py-2 rounded-apple text-sm"
                        style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewing" {{ request('status') == 'reviewing' ? 'selected' : '' }}>Reviewing</option>
                    <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <select name="applicant_type"
                        class="w-full px-3 py-2 rounded-apple text-sm"
                        style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                    <option value="">Semua Pemohon</option>
                    <option value="perorangan" {{ request('applicant_type') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                    <option value="badan" {{ request('applicant_type') == 'badan' ? 'selected' : '' }}>Badan Usaha</option>
                </select>
            </div>
            <div class="flex gap-2 md:col-span-2 lg:col-span-5">
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.leads.index', ['tab' => 'service-cost-requests']) }}" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card-elevated rounded-apple-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Request #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Pemohon</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Kontak</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-separator">
                @forelse($serviceCostRequests ?? [] as $item)
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                            'reviewing' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                            'quoted' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500',
                            'accepted' => 'bg-green-500/20 text-green-400 border-green-500',
                            'rejected' => 'bg-red-500/20 text-red-400 border-red-500',
                            'cancelled' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                        ];
                    @endphp
                    <tr class="hover:bg-dark-bg-tertiary transition-colors">
                        <td class="px-4 py-3 text-sm font-mono text-dark-text-primary">{{ $item->request_number }}</td>
                        <td class="px-4 py-3 text-sm text-dark-text-secondary">
                            {{ $item->created_at->format('d M Y') }}<br>
                            <span class="text-xs">{{ $item->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-dark-text-primary">{{ $item->display_name }}</div>
                            <div class="text-xs text-dark-text-secondary">{{ $item->applicant_type === 'badan' ? 'Badan Usaha' : 'Perorangan' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-dark-text-secondary">
                            <div>{{ $item->email }}</div>
                            <div class="text-xs">{{ $item->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-dark-text-secondary">
                            {{ \App\Models\ServiceCostRequest::getServiceCategories()[$item->service_category] ?? $item->service_category }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium border {{ $statusColors[$item->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                {{ $item->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <a href="{{ route('admin.service-cost-requests.show', $item->request_number) }}" class="text-apple-blue hover:text-blue-400 text-sm font-medium">
                                Lihat Detail →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-dark-text-secondary">
                            <i class="fas fa-inbox text-2xl mb-3 block opacity-30"></i>
                            <p>Tidak ada data permohonan biaya ditemukan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($serviceCostRequests) && $serviceCostRequests->hasPages())
        <div class="px-4 py-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
            {{ $serviceCostRequests->appends(array_merge(request()->all(), ['tab' => 'service-cost-requests']))->links() }}
        </div>
    @endif
</div>

<div class="mt-4 rounded-apple-lg p-4" style="background-color: rgba(255, 149, 0, 0.08); border: 1px solid rgba(255, 149, 0, 0.3);">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-orange-400 mr-3 mt-0.5"></i>
        <div class="text-sm text-dark-text-secondary">
            <p class="font-medium text-orange-300 mb-1">Tentang Permohonan Biaya</p>
            <p>Data dari formulir /permohonan masuk ke tab ini untuk review cepat oleh tim admin.</p>
        </div>
    </div>
</div>
