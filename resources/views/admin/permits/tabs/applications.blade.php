{{-- Applications Tab Content --}}
<div class="space-y-5">
    {{-- Search & Filter --}}
    <div class="card-elevated rounded-apple-lg p-5 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Pencarian</p>
                <h2 class="text-lg font-semibold text-white">Cari Permohonan Izin</h2>
            </div>
            <span class="text-xs" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan {{ $applications->total() ?? 0 }} hasil
            </span>
        </div>
        
        <form method="GET" action="{{ route('admin.permits.index') }}" class="space-y-3" data-auto-submit>
            <input type="hidden" name="tab" value="applications">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nomor/nama klien..." 
                           class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Status</label>
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
                    <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.65);">Jenis Izin</label>
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
                <thead style="background-color: rgba(28,28,30,0.45);">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Nomor</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Klien</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Jenis Izin</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Tanggal</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($applications as $app)
                        <tr class="hover-lift transition-apple">
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-semibold text-dark-text-primary">{{ $app->application_number }}</div>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="text-sm font-medium text-dark-text-primary">
                                    {{ $app->client->company_name ?? $app->client->name }}
                                </div>
                                @if($app->assignedUser)
                                    <div class="text-xs text-dark-text-secondary mt-1">
                                        <i class="fas fa-user-tag mr-1"></i>{{ $app->assignedUser->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-sm text-dark-text-primary">
                                {{ $app->permitType->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'submitted' => 'rgba(10,132,255,0.15)|rgba(10,132,255,1)',
                                        'under_review' => 'rgba(255,159,10,0.15)|rgba(255,159,10,1)',
                                        'quoted' => 'rgba(175,82,222,0.15)|rgba(175,82,222,1)',
                                        'payment_verified' => 'rgba(52,199,89,0.15)|rgba(52,199,89,1)',
                                        'in_progress' => 'rgba(90,200,250,0.15)|rgba(90,200,250,1)',
                                        'completed' => 'rgba(52,199,89,0.15)|rgba(52,199,89,1)',
                                    ];
                                    $colors = explode('|', $statusColors[$app->status] ?? 'rgba(128,128,128,0.15)|rgba(128,128,128,1)');
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-apple"
                                      style="background-color: {{ $colors[0] }}; color: {{ $colors[1] }};">
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
                                       class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple" 
                                       style="background-color: rgba(90, 200, 250, 0.15); color: var(--apple-teal); border: 1px solid rgba(90, 200, 250, 0.25);">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-6xl mb-6" style="color: rgba(235, 235, 245, 0.3);"></i>
                                    <h3 class="text-xl font-semibold mb-2" style="color: #FFFFFF;">Belum Ada Permohonan</h3>
                                    <p class="mb-6" style="color: rgba(235, 235, 245, 0.6);">
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
        <div class="rounded-apple-lg px-4 py-3" style="background-color: #2C2C2E; border: 1px solid rgba(84, 84, 88, 0.65); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);">
            {{ $applications->appends(['tab' => 'applications'])->links('pagination::tailwind') }}
        </div>
    @endif
</div>
