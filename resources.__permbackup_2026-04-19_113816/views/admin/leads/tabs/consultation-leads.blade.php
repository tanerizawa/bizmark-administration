{{-- Consultation Leads Tab Content --}}
@php
    $stats = $consultationLeadsStats ?? [
        'total' => 0, 'new' => 0, 'contacted' => 0, 'converted' => 0, 
        'pending_review' => 0, 'high_value' => 0, 'this_week' => 0, 'this_month' => 0
    ];
@endphp

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-lg font-bold text-white mb-1">{{ $stats['total'] }}</div>
        <div class="text-xs text-dark-text-secondary">Total</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-blue-400 mb-1">{{ $stats['new'] }}</div>
        <div class="text-xs text-dark-text-secondary">Baru</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-green-400 mb-1">{{ $stats['contacted'] }}</div>
        <div class="text-xs text-dark-text-secondary">Dihubungi</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-purple-400 mb-1">{{ $stats['converted'] }}</div>
        <div class="text-xs text-dark-text-secondary">Konversi</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $stats['pending_review'] }}</div>
        <div class="text-xs text-dark-text-secondary">Perlu Review</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-red-400 mb-1">{{ $stats['high_value'] }}</div>
        <div class="text-xs text-dark-text-secondary">High Value</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-indigo-400 mb-1">{{ $stats['this_week'] }}</div>
        <div class="text-xs text-dark-text-secondary">Minggu Ini</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-orange-400 mb-1">{{ $stats['this_month'] }}</div>
        <div class="text-xs text-dark-text-secondary">Bulan Ini</div>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-elevated rounded-apple-lg mb-4">
    <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
        <h3 class="text-base font-semibold text-white">Pencarian & Filter</h3>
    </div>
    <div class="p-4">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="hidden" name="tab" value="consultation-leads">
            <div class="md:col-span-2">
                <input type="text" 
                       name="search" 
                       class="w-full px-3 py-2 rounded-apple text-sm"
                       placeholder="Cari ID, email, nama, perusahaan..." 
                       value="{{ request('search') }}"
                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
            </div>

            <div>
                <select name="status" 
                        class="w-full px-3 py-2 rounded-apple text-sm"
                        style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                    <option value="">Semua Status</option>
                    <option value="auto_estimated" {{ request('status') === 'auto_estimated' ? 'selected' : '' }}>Auto Estimated</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>Quoted</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <select name="contacted" 
                        class="w-full px-3 py-2 rounded-apple text-sm"
                        style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                    <option value="">Status Kontak</option>
                    <option value="yes" {{ request('contacted') === 'yes' ? 'selected' : '' }}>Sudah Dihubungi</option>
                    <option value="no" {{ request('contacted') === 'no' ? 'selected' : '' }}>Belum Dihubungi</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.leads.index', ['tab' => 'consultation-leads']) }}" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
        
        <!-- Date Range Filter -->
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
            <input type="hidden" name="tab" value="consultation-leads">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="contacted" value="{{ request('contacted') }}">
            <div>
                <label class="text-xs text-dark-text-secondary mb-1 block">Dari Tanggal</label>
                <input type="date" 
                       name="date_from" 
                       class="w-full px-3 py-2 rounded-apple text-sm"
                       value="{{ request('date_from') }}"
                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
            </div>
            <div>
                <label class="text-xs text-dark-text-secondary mb-1 block">Sampai Tanggal</label>
                <input type="date" 
                       name="date_to" 
                       class="w-full px-3 py-2 rounded-apple text-sm"
                       value="{{ request('date_to') }}"
                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium w-full">
                    Filter Tanggal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Consultation Leads Table -->
<div class="card-elevated rounded-apple-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Lead #</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Perusahaan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Kontak</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Estimasi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-separator">
                @forelse($consultations ?? [] as $consultation)
                    <tr class="hover:bg-dark-bg-tertiary transition-colors">
                        <td class="px-4 py-3 text-sm">
                            <span class="font-mono text-dark-text-primary">#{{ $consultation->id }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-dark-text-secondary">
                            {{ $consultation->created_at->format('d M Y') }}<br>
                            <span class="text-xs">{{ $consultation->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-dark-text-primary">{{ $consultation->company_name ?: $consultation->name }}</div>
                            <div class="text-xs text-dark-text-secondary">{{ optional($consultation->kbli)->description }}</div>
                            <div class="text-xs text-dark-text-secondary mt-1">
                                @php
                                    $sizeColors = [
                                        'large' => 'bg-red-500/20 text-red-400 border-red-500',
                                        'medium' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                                        'small' => 'bg-green-500/20 text-green-400 border-green-500',
                                        'micro' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border {{ $sizeColors[$consultation->business_size] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                    {{ ucfirst($consultation->business_size) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-dark-text-primary">{{ $consultation->name }}</div>
                            <div class="text-xs text-dark-text-secondary">{{ $consultation->email }}</div>
                            <div class="text-xs text-dark-text-secondary">{{ $consultation->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-dark-text-primary">
                                {{ $consultation->auto_estimate['cost_summary']['formatted']['grand_total'] ?? '-' }}
                            </div>
                            <div class="text-xs text-dark-text-secondary">
                                Confidence: {{ number_format(($consultation->confidence_score ?? 0.5) * 100, 0) }}%
                            </div>
                            @if(isset($consultation->auto_estimate['cost_summary']['grand_total']) && $consultation->auto_estimate['cost_summary']['grand_total'] >= 10000000)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border bg-red-500/20 text-red-400 border-red-500">
                                    High Value
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $statusColors = [
                                    'auto_estimated' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                                    'reviewed' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                                    'approved' => 'bg-green-500/20 text-green-400 border-green-500',
                                    'quoted' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500',
                                    'rejected' => 'bg-red-500/20 text-red-400 border-red-500',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium border {{ $statusColors[$consultation->estimate_status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                {{ ucfirst(str_replace('_', ' ', $consultation->estimate_status)) }}
                            </span>
                            
                            @if($consultation->contacted)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border bg-green-500/20 text-green-400 border-green-500 mt-1">
                                    <i class="fas fa-phone mr-1"></i>Contacted
                                </span>
                            @endif

                            @if($consultation->converted_to_client)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border bg-purple-500/20 text-purple-400 border-purple-500 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Converted
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <a href="{{ route('admin.consultation-leads.show', $consultation) }}" class="text-apple-blue hover:text-blue-400 text-sm font-medium">
                                Lihat Detail →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-dark-text-secondary">
                            <i class="fas fa-inbox text-2xl mb-3 block opacity-30"></i>
                            <p>Tidak ada consultation leads ditemukan</p>
                            @if(request()->hasAny(['search', 'status', 'contacted', 'date_from', 'date_to']))
                                <a href="{{ route('admin.leads.index', ['tab' => 'consultation-leads']) }}" class="text-apple-blue hover:text-blue-400 text-sm mt-2 inline-block">
                                    Reset Filter
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($consultations) && $consultations->hasPages())
        <div class="px-4 py-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
            {{ $consultations->appends(array_merge(request()->all(), ['tab' => 'consultation-leads']))->links() }}
        </div>
    @endif
</div>

<!-- Info Box -->
<div class="mt-4 rounded-apple-lg p-4" style="background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-apple-blue mr-3 mt-0.5"></i>
        <div class="text-sm text-dark-text-secondary">
            <p class="font-medium text-apple-blue mb-1">Tentang Consultation Leads</p>
            <p>Data leads dari estimasi biaya perizinan AI. Gunakan fitur ini untuk tracking dan konversi leads menjadi klien terdaftar dengan proyek perizinan.</p>
        </div>
    </div>
</div>
