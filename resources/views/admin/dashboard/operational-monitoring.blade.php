    {{-- Operational monitoring section --}}
    <section class="card-elevated rounded-apple-lg p-3 space-y-2">
        <div class="flex items-center justify-between">
            <h2 class="admin-section text-white flex items-center gap-2">
                <i class="fas fa-tasks text-apple-green" style="font-size: 0.75rem;"></i>Pemantauan Operasional
            </h2>
        </div>
        <p class="admin-body text-dark-text-secondary">Jadwal 30 hari, distribusi proyek, dan aktivitas terkini</p>

        {{-- RAG AI Quality Metrics --}}
        {{-- FIX (BUG-07): Null coalescing --}}
        @if(($ragMetrics['total_processed'] ?? 0) > 0)
        <div class="card-elevated rounded-apple p-3 space-y-2">
            <div class="flex items-center justify-between">
                <h3 class="admin-section text-white flex items-center gap-2">
                    <i class="fas fa-brain text-apple-purple/95" style="font-size: 0.65rem;"></i>Kualitas RAG AI
                </h3>
                <span class="admin-badge bg-apple-purple/15 text-apple-purple/95">{{ $ragMetrics['total_processed'] ?? 0 }} diproses</span>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div class="text-center p-2 rounded-apple bg-apple-green/8">
                    <p class="admin-stat text-white">{{ $ragMetrics['avg_confidence'] ?? 0 }}%</p>
                    <p class="admin-small text-dark-text-tertiary">Rata-rata Confidence</p>
                </div>
                <div class="text-center p-2 rounded-apple bg-apple-green/8">
                    <p class="admin-stat text-apple-green">{{ $ragMetrics['high_confidence'] ?? 0 }}</p>
                    <p class="admin-small text-dark-text-tertiary">Confidence Tinggi</p>
                </div>
                <div class="text-center p-2 rounded-apple bg-apple-red/8">
                    <p class="admin-stat text-apple-red/95">{{ $ragMetrics['low_confidence'] ?? 0 }}</p>
                    <p class="admin-small text-dark-text-tertiary">Perlu Review</p>
                </div>
            </div>
            {{-- FIX (BUG-07): Null coalescing for collection --}}
            @php $recentLeads = $ragMetrics['recent'] ?? collect(); @endphp
            @if($recentLeads->count() > 0)
            <div class="space-y-1 overflow-y-auto" style="max-height: 120px;">
                @foreach($recentLeads as $lead)
                <a href="{{ route('admin.consultation-leads.show', $lead->id) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple bg-white/3">
                    <div class="flex items-center justify-between">
                        <p class="admin-body font-medium text-white truncate">{{ $lead->name ?? '-' }} — {{ $lead->company_name ?? '-' }}</p>
                        <span class="admin-small px-1.5 py-0.5 rounded-full" style="background: rgba({{ ($lead->rag_confidence ?? 0) >= 0.7 ? '52,199,89' : (($lead->rag_confidence ?? 0) >= 0.4 ? '255,204,0' : '255,59,48') }},0.2); color: rgba({{ ($lead->rag_confidence ?? 0) >= 0.7 ? '52,199,89' : (($lead->rag_confidence ?? 0) >= 0.4 ? '255,204,0' : '255,59,48') }},0.95);">{{ round(($lead->rag_confidence ?? 0) * 100) }}%</span>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        <div class="grid grid-cols-3 gap-2">
            {{-- Timeline --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">30 Hari Mendatang</h3>
                    {{-- FIX (BUG-07): Null coalescing --}}
                    <span class="admin-small text-dark-text-tertiary">{{ $thisWeek['period_start'] ?? '-' }} – {{ $thisWeek['period_end'] ?? '-' }}</span>
                </div>
                <div class="space-y-1 overflow-y-auto" style="max-height: 220px;">
                    {{-- FIX (BUG-07): Null coalescing --}}
                    @if(($thisWeek['total_items'] ?? 0) > 0)
                        @foreach(($thisWeek['tasks'] ?? []) as $task)
                        <a href="{{ route('projects.show', $task['project_id'] ?? 0) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple" style="background: rgba({{ ($task['is_past'] ?? false) ? '255,59,48' : (($task['is_today'] ?? false) ? '255,204,0' : '52,199,89') }},0.08);">
                            <div class="flex items-start gap-2">
                                <div class="w-1.5 h-1.5 rounded-full mt-1.5" style="background: {{ $task['priority_color'] ?? '#888' }};"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="admin-body font-medium text-white truncate">{{ $task['title'] ?? '-' }}</p>
                                    <p class="admin-small text-dark-text-tertiary">{{ $task['project'] ?? '' }}</p>
                                    <div class="flex items-center gap-2 mt-0.5 admin-small">
                                        <span style="color: {{ $task['priority_color'] ?? '#888' }};"><i class="fas fa-clock mr-1"></i>{{ $task['deadline_formatted'] ?? '' }}</span>
                                        @if($task['is_past'] ?? false)
                                        <span class="px-1.5 py-0.5 rounded-full bg-apple-red/20 text-apple-red/90" style="font-size: 10px;">Terlambat {{ $task['days_until'] ?? 0 }}h</span>
                                        @elseif($task['is_today'] ?? false)
                                        <span class="px-1.5 py-0.5 rounded-full bg-apple-yellow/20 text-apple-yellow" style="font-size: 10px;">Hari ini</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                        @foreach(($thisWeek['projects'] ?? []) as $project)
                        <a href="{{ route('projects.show', $project['id'] ?? 0) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple bg-apple-blue/8">
                            <p class="admin-body font-medium text-white">{{ $project['name'] ?? '-' }}</p>
                            <p class="admin-small text-dark-text-tertiary">{{ $project['deadline_formatted'] ?? 'Belum ada tenggat' }}</p>
                            <p class="admin-small text-apple-blue"><i class="fas fa-flag mr-1"></i>{{ ($project['is_past'] ?? false) ? 'Terlambat ' . ($project['days_until'] ?? 0) . ' hari' : ($project['days_until'] ?? 0) . ' hari lagi' }}</p>
                        </a>
                        @endforeach
                    @else
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center bg-apple-blue/12">
                            <i class="fas fa-calendar-check text-apple-blue" style="font-size: 0.7rem;"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Jadwal Kosong</p>
                        <p class="admin-small text-dark-text-tertiary">Tidak ada agenda dalam 30 hari</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Project status distribution --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Distribusi Proyek</h3>
                    {{-- FIX (BUG-07): Null coalescing --}}
                    <span class="admin-badge bg-white/8 text-white/70">{{ $projectStatusDistribution['total'] ?? 0 }}</span>
                </div>
                <div class="space-y-2">
                    @forelse(($projectStatusDistribution['groups'] ?? []) as $statusGroup)
                    <div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $statusGroup['color'] ?? '#888' }};"></span>
                                <p class="admin-body font-medium text-white">{{ $statusGroup['label'] ?? '-' }}</p>
                            </div>
                            <span class="admin-body font-bold text-dark-text-secondary">{{ $statusGroup['count'] ?? 0 }}</span>
                        </div>
                        <div class="ml-4 mt-0.5 space-y-0.5 max-h-16 overflow-y-auto">
                            @foreach(($statusGroup['projects'] ?? []) as $proj)
                            <a href="{{ route('projects.show', $proj['id'] ?? 0) }}" class="block admin-small text-dark-text-tertiary hover:text-white truncate">• {{ $proj['name'] ?? '-' }}</a>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="admin-small text-dark-text-tertiary">Belum ada proyek aktif</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent activity --}}
            <div class="card-elevated rounded-apple p-3 flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="admin-section text-white">Aktivitas Terkini</h3>
                    {{-- FIX (BUG-07): Null coalescing --}}
                    <span class="admin-small text-dark-text-tertiary">{{ count($recentActivities ?? []) }} aktivitas</span>
                </div>
                <div class="space-y-1 overflow-y-auto flex-1" style="max-height: 220px;">
                    @forelse(($recentActivities ?? []) as $activity)
                    <div class="p-2 rounded-apple flex items-start gap-2 hover:bg-dark-elevated-2 transition-apple bg-white/3">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background: {{ $activity['icon_bg'] ?? 'rgba(10,132,255,0.2)' }};">
                            <i class="fas {{ $activity['icon'] ?? 'fa-info' }}" style="font-size: 0.55rem; color: {{ $activity['icon_color'] ?? '#0A84FF' }};"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="admin-body text-white truncate">{{ $activity['title'] ?? '-' }}</p>
                            <div class="flex items-center gap-2 admin-small text-dark-text-tertiary">
                                <span>{{ $activity['project'] ?? $activity['type'] ?? '' }}</span>
                                <span>•</span>
                                <span>{{ $activity['time_ago'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center bg-white/5">
                            <i class="fas fa-history text-dark-text-tertiary" style="font-size: 0.7rem;"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Tidak Ada Aktivitas</p>
                        <p class="admin-small text-dark-text-tertiary">Belum ada aktivitas tercatat</p>
                    </div>
                    @endforelse
                </div>
                @if(count($recentActivities ?? []) > 0)
                <div class="mt-2 pt-2 border-t border-gray-700/50">
                    <a href="{{ route('projects.index') }}" class="admin-small text-apple-blue hover:underline">Lihat semua aktivitas →</a>
                </div>
                @endif
            </div>
        </div>
    </section>

