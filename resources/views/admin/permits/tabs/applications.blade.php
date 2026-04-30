{{-- Applications Tab Content --}}
<div class="space-y-5">
    {{-- Search & Filter --}}
    <div class="card-elevated rounded-apple-lg p-5 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-dark-text-tertiary">Pencarian</p>
                <h2 class="text-sm font-semibold text-white">Cari Permohonan Izin</h2>
            </div>
            <span class="text-xs text-dark-text-secondary">
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan {{ $applications->total() ?? 0 }} hasil
            </span>
        </div>
        
        <form method="GET" action="{{ route('admin.permits.index') }}" class="space-y-3" data-auto-submit>
            <input type="hidden" name="tab" value="applications">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-dark-text-secondary">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nomor/nama klien..." 
                           class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-dark-text-secondary">Status</label>
                    <select name="status" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5 text-dark-text-secondary">Jenis Izin</label>
                    <select name="permit_type" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                        <option value="">Semua Jenis</option>
                        @foreach($permitTypes as $type)
                            <option value="{{ $type->id }}" {{ request('permit_type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary-sm flex-1">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.permits.index', ['tab' => 'applications']) }}" class="btn-secondary-sm flex-1">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Applications Table --}}
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead class="bg-[rgba(44,44,46,0.65)]">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Nomor</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Klien</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Jenis Izin</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Tanggal</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-dark-bg-secondary">
                    @forelse($applications as $app)
                        <tr class="hover-lift transition-apple">
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-semibold text-dark-text-primary">{{ $app->application_number }}</div>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-medium text-dark-text-primary">
                                    {{ $app->client->company_name ?? $app->client->name }}
                                </div>
                                @if($app->reviewer)
                                    <div class="text-xs text-dark-text-secondary mt-1">
                                        <i class="fas fa-user-tag mr-1"></i>{{ $app->reviewer->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-sm text-dark-text-primary">
                                {{ $app->permitType->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'submitted' => 'var(--neuro-primary)|var(--neuro-primary)',
                                        'under_review' => 'var(--neuro-warning)|var(--neuro-warning)',
                                        'quoted' => 'var(--neuro-secondary)|var(--neuro-secondary)',
                                        'payment_verified' => 'var(--neuro-success)|var(--neuro-success)',
                                        'in_progress' => 'var(--neuro-info)|var(--neuro-info)',
                                        'completed' => 'var(--neuro-success)|var(--neuro-success)',
                                    ];
                                    $status = $statusColors[$app->status] ?? 'var(--text-dark-tertiary)|var(--text-dark-tertiary)';
                                    $colors = explode('|', $status);
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                      style="background: {{ $colors[0] }}; color: {{ $colors[1] }};">
                                    {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-sm text-dark-text-secondary">
                                    {{ $app->created_at->locale('id')->isoFormat('D MMM Y') }}
                                </div>
                                <div class="text-xs text-dark-text-tertiary">
                                    {{ $app->created_at->locale('id')->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <a href="{{ route('admin.permit-applications.show', $app->id) }}"
                                       class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple bg-apple-teal/20 text-apple-teal border border-apple-teal/30">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl mb-6 text-dark-text-tertiary"></i>
                                    <h3 class="text-base font-semibold mb-2 text-white">Belum Ada Permohonan</h3>
                                    <p class="mb-6 text-dark-text-secondary">
                                        Permohonan izin dari klien akan muncul di sini
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($applications->hasPages())
        <div class="rounded-apple-lg px-4 py-3 bg-dark-bg-tertiary border border-white/20 shadow-soft">
            {{ $applications->appends(['tab' => 'applications'])->links('pagination::tailwind') }}
        </div>
    @endif
</div>
