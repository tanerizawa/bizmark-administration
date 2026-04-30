{{-- Service Inquiries Tab Content --}}
@php
    $stats = $serviceInquiriesStats ?? [
        'total' => 0, 'new' => 0, 'analyzed' => 0, 'contacted' => 0, 
        'converted' => 0, 'high_priority' => 0, 'this_week' => 0, 'this_month' => 0
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
        <div class="text-2xl font-bold text-indigo-400 mb-1">{{ $stats['analyzed'] }}</div>
        <div class="text-xs text-dark-text-secondary">Dianalisis</div>
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
        <div class="text-2xl font-bold text-red-400 mb-1">{{ $stats['high_priority'] }}</div>
        <div class="text-xs text-dark-text-secondary">High Priority</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $stats['this_week'] }}</div>
        <div class="text-xs text-dark-text-secondary">Minggu Ini</div>
    </div>
    <div class="card-elevated rounded-apple-lg p-3">
        <div class="text-2xl font-bold text-orange-400 mb-1">{{ $stats['this_month'] }}</div>
        <div class="text-xs text-dark-text-secondary">Bulan Ini</div>
    </div>
</div>

<!-- Filter & Search Card -->
<div class="card-elevated rounded-apple-lg mb-4">
    <div class="px-4 py-3 border-b border-[rgba(84,84,88,0.65)]">
        <h3 class="text-base font-semibold text-white">Pencarian & Filter</h3>
    </div>
    <div class="p-4">
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="hidden" name="tab" value="service-inquiries">
            <div class="md:col-span-2">
                <input type="text" 
                       name="search" 
                       class="w-full px-3 py-2 rounded-apple text-sm bg-dark-bg-tertiary border border-dark-border text-dark-text-primary"
                       placeholder="Cari nomor inquiry, email, perusahaan, nama kontak..." 
                       value="{{ request('search') }}">
            </div>
            <div>
                <select name="status" 
                        class="w-full px-3 py-2 rounded-apple text-sm bg-dark-bg-tertiary border border-dark-border text-dark-text-primary">
                    <option value="">Semua Status</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Baru</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="analyzed" {{ request('status') == 'analyzed' ? 'selected' : '' }}>Dianalisis</option>
                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Dihubungi</option>
                    <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Konversi</option>
                    <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Terdaftar</option>
                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div>
                <select name="priority" 
                        class="w-full px-3 py-2 rounded-apple text-sm bg-dark-bg-tertiary border border-dark-border text-dark-text-primary">
                    <option value="">Semua Prioritas</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.leads.index', ['tab' => 'service-inquiries']) }}" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
        
        <!-- Date Range Filter -->
        <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
            <input type="hidden" name="tab" value="service-inquiries">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="priority" value="{{ request('priority') }}">
            <div>
                <label class="text-xs text-dark-text-secondary mb-1 block">Dari Tanggal</label>
                <input type="date" 
                       name="date_from" 
                       class="w-full px-3 py-2 rounded-apple text-sm bg-dark-bg-tertiary border border-dark-border text-dark-text-primary"
                       value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="text-xs text-dark-text-secondary mb-1 block">Sampai Tanggal</label>
                <input type="date" 
                       name="date_to" 
                       class="w-full px-3 py-2 rounded-apple text-sm bg-dark-bg-tertiary border border-dark-border text-dark-text-primary"
                       value="{{ request('date_to') }}">
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium w-full">
                    Filter Tanggal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Service Inquiries Table -->
<div class="card-elevated rounded-apple-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-[rgba(84,84,88,0.65)]">
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Inquiry #
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Perusahaan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Kontak
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Prioritas
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Est. Value
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse($inquiries ?? [] as $inquiry)
                    <tr class="hover:bg-dark-bg-tertiary transition-colors">
                        <td class="px-4 py-3 text-sm">
                            <span class="font-mono text-dark-text-primary">{{ $inquiry->inquiry_number }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-dark-text-secondary">
                            {{ $inquiry->created_at->format('d M Y') }}<br>
                            <span class="text-xs">{{ $inquiry->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-dark-text-primary">{{ $inquiry->company_name }}</div>
                            <div class="text-xs text-dark-text-secondary">{{ $inquiry->company_type ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-medium text-dark-text-primary">{{ $inquiry->contact_person }}</div>
                            <div class="text-xs text-dark-text-secondary">{{ $inquiry->email }}</div>
                            <div class="text-xs text-dark-text-secondary">{{ $inquiry->phone ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $statusColors = [
                                    'new' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                                    'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                                    'analyzed' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500',
                                    'contacted' => 'bg-green-500/20 text-green-400 border-green-500',
                                    'qualified' => 'bg-teal-500/20 text-teal-400 border-teal-500',
                                    'converted' => 'bg-purple-500/20 text-purple-400 border-purple-500',
                                    'registered' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500',
                                    'lost' => 'bg-red-500/20 text-red-400 border-red-500',
                                ];
                                $statusLabels = [
                                    'new' => 'Baru',
                                    'processing' => 'Diproses',
                                    'analyzed' => 'Dianalisis',
                                    'contacted' => 'Dihubungi',
                                    'qualified' => 'Qualified',
                                    'converted' => 'Konversi',
                                    'registered' => 'Terdaftar',
                                    'lost' => 'Lost',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium border {{ $statusColors[$inquiry->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                {{ $statusLabels[$inquiry->status] ?? ucfirst($inquiry->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $priorityColors = [
                                    'high' => 'bg-red-500/20 text-red-400 border-red-500',
                                    'medium' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                                    'low' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                                ];
                            @endphp
                            @if($inquiry->priority)
                                <span class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium border {{ $priorityColors[$inquiry->priority] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                    {{ ucfirst($inquiry->priority) }}
                                </span>
                            @else
                                <span class="text-dark-text-secondary text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-dark-text-primary">
                            @if($inquiry->estimated_value)
                                Rp {{ number_format($inquiry->estimated_value / 1000000, 0) }}M
                            @else
                                <span class="text-dark-text-secondary">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <a href="{{ route('admin.service-inquiries.show', $inquiry) }}" class="text-apple-blue hover:text-blue-400 text-sm font-medium">
                                Lihat Detail →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-dark-text-secondary">
                            <i class="fas fa-inbox text-2xl mb-3 block opacity-30"></i>
                            <p>Tidak ada inquiry ditemukan</p>
                            @if(request()->hasAny(['search', 'status', 'priority', 'date_from', 'date_to']))
                                <a href="{{ route('admin.leads.index', ['tab' => 'service-inquiries']) }}" class="text-apple-blue hover:text-blue-400 text-sm mt-2 inline-block">
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
    @if(isset($inquiries) && $inquiries->hasPages())
        <div class="px-4 py-3 border-t border-[rgba(84,84,88,0.65)]">
            {{ $inquiries->appends(array_merge(request()->all(), ['tab' => 'service-inquiries']))->links() }}
        </div>
    @endif
</div>

<!-- Info Box -->
<div class="mt-4 rounded-apple-lg p-4 bg-apple-blue/10 border border-apple-blue/30">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-apple-blue mr-3 mt-0.5"></i>
        <div class="text-sm text-dark-text-secondary">
            <p class="font-medium text-apple-blue mb-1">Tentang Service Inquiries</p>
            <p>Data inquiry dari formulir konsultasi gratis AI di landing page. Gunakan fitur ini untuk tracking dan konversi leads menjadi klien terdaftar.</p>
        </div>
    </div>
</div>
