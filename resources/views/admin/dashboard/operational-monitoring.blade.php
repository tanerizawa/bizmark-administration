{{-- Operational Monitoring Section --}}
<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">

    {{-- Section Header --}}
    <div style="padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Operasional</p>
        <h2 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0;display:flex;align-items:center;gap:8px">
            <i class="fas fa-tasks" style="font-size:0.8rem;color:var(--apple-green)"></i>
            Pemantauan Operasional
        </h2>
    </div>

    {{-- RAG AI Quality Metrics (only if data exists) --}}
    @if(($ragMetrics['total_processed'] ?? 0) > 0)
    <div style="padding:14px 20px;border-bottom:1px solid var(--dark-separator);background:color-mix(in srgb,var(--apple-purple) 6%,var(--dark-bg-secondary))">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <h3 style="font-size:0.8rem;font-weight:700;color:var(--dark-text-primary);margin:0;display:flex;align-items:center;gap:7px">
                <i class="fas fa-brain" style="color:var(--apple-purple);font-size:0.75rem"></i>Kualitas RAG AI
            </h3>
            <span style="padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)">{{ $ragMetrics['total_processed'] ?? 0 }} diproses</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
            <div style="text-align:center;padding:10px;border-radius:10px;background:color-mix(in srgb,var(--apple-blue) 10%,var(--dark-bg-tertiary))">
                <p style="font-size:1.05rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $ragMetrics['avg_confidence'] ?? 0 }}%</p>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:3px 0 0">Rata-rata Confidence</p>
            </div>
            <div style="text-align:center;padding:10px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 10%,var(--dark-bg-tertiary))">
                <p style="font-size:1.05rem;font-weight:700;color:var(--apple-green);margin:0">{{ $ragMetrics['high_confidence'] ?? 0 }}</p>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:3px 0 0">Confidence Tinggi</p>
            </div>
            <div style="text-align:center;padding:10px;border-radius:10px;background:color-mix(in srgb,var(--apple-red) 10%,var(--dark-bg-tertiary))">
                <p style="font-size:1.05rem;font-weight:700;color:var(--apple-red);margin:0">{{ $ragMetrics['low_confidence'] ?? 0 }}</p>
                <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:3px 0 0">Perlu Review</p>
            </div>
        </div>
        @php $recentLeads = $ragMetrics['recent'] ?? collect(); @endphp
        @if($recentLeads->count() > 0)
        <div style="display:flex;flex-direction:column;gap:4px;margin-top:10px;max-height:100px;overflow-y:auto">
            @foreach($recentLeads as $lead)
            @php
                $conf = $lead->rag_confidence ?? 0;
                $confColor = $conf >= 0.7 ? 'var(--apple-green)' : ($conf >= 0.4 ? 'var(--apple-yellow)' : 'var(--apple-red)');
            @endphp
            <a href="{{ route('admin.consultation-leads.show', $lead->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:7px 10px;border-radius:8px;background:var(--dark-bg-tertiary);text-decoration:none" onmouseover="this.style.opacity=.8" onmouseout="this.style.opacity=1">
                <p style="font-size:0.78rem;font-weight:500;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;margin-right:10px">{{ $lead->name ?? '-' }} — {{ $lead->company_name ?? '-' }}</p>
                <span style="font-size:0.7rem;font-weight:600;padding:2px 7px;border-radius:8px;background:color-mix(in srgb,{{ $confColor }} 20%,transparent);color:{{ $confColor }};flex-shrink:0">{{ round($conf * 100) }}%</span>
            </a>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- 3-col grid --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0">

        {{-- Col 1: 30-Day Timeline --}}
        <div style="padding:16px 20px;border-right:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">30 Hari Mendatang</h3>
                <span style="font-size:0.65rem;color:var(--dark-text-secondary)">{{ $thisWeek['period_start'] ?? '-' }} – {{ $thisWeek['period_end'] ?? '-' }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:5px;max-height:260px;overflow-y:auto">
                @if(($thisWeek['total_items'] ?? 0) > 0)
                    @foreach(($thisWeek['tasks'] ?? []) as $task)
                    @php
                        $tBg = ($task['is_past'] ?? false) ? 'var(--apple-red)' : (($task['is_today'] ?? false) ? 'var(--apple-yellow)' : 'var(--apple-green)');
                    @endphp
                    <a href="{{ route('projects.show', $task['project_id'] ?? 0) }}" style="display:block;padding:9px 12px;border-radius:10px;background:color-mix(in srgb,{{ $tBg }} 8%,var(--dark-bg-tertiary));text-decoration:none">
                        <div style="display:flex;align-items:flex-start;gap:8px">
                            <div style="width:6px;height:6px;border-radius:50%;background:{{ $task['priority_color'] ?? 'var(--dark-text-secondary)' }};flex-shrink:0;margin-top:6px"></div>
                            <div style="flex:1;min-width:0">
                                <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $task['title'] ?? '-' }}</p>
                                <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:1px 0 0">{{ $task['project'] ?? '' }}</p>
                                <div style="display:flex;align-items:center;gap:6px;margin-top:3px">
                                    <span style="font-size:0.68rem;color:{{ $task['priority_color'] ?? 'var(--dark-text-secondary)' }}"><i class="fas fa-clock" style="margin-right:3px"></i>{{ $task['deadline_formatted'] ?? '' }}</span>
                                    @if($task['is_past'] ?? false)
                                    <span style="font-size:0.62rem;padding:1px 6px;border-radius:6px;background:color-mix(in srgb,var(--apple-red) 20%,transparent);color:var(--apple-red)">Terlambat {{ $task['days_until'] ?? 0 }}h</span>
                                    @elseif($task['is_today'] ?? false)
                                    <span style="font-size:0.62rem;padding:1px 6px;border-radius:6px;background:color-mix(in srgb,var(--apple-yellow) 20%,transparent);color:var(--apple-yellow)">Hari ini</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                    @foreach(($thisWeek['projects'] ?? []) as $project)
                    <a href="{{ route('projects.show', $project['id'] ?? 0) }}" style="display:block;padding:9px 12px;border-radius:10px;background:color-mix(in srgb,var(--apple-blue) 8%,var(--dark-bg-tertiary));text-decoration:none">
                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $project['name'] ?? '-' }}</p>
                        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:1px 0 0">{{ $project['deadline_formatted'] ?? 'Belum ada tenggat' }}</p>
                        <p style="font-size:0.68rem;color:var(--apple-blue);margin:1px 0 0"><i class="fas fa-flag" style="margin-right:3px"></i>{{ ($project['is_past'] ?? false) ? 'Terlambat '.($project['days_until'] ?? 0).' hari' : ($project['days_until'] ?? 0).' hari lagi' }}</p>
                    </a>
                    @endforeach
                @else
                <div style="text-align:center;padding:32px 0">
                    <div style="width:38px;height:38px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                        <i class="fas fa-calendar-check" style="color:var(--apple-blue);font-size:0.9rem"></i>
                    </div>
                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">Jadwal Kosong</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Tidak ada agenda dalam 30 hari</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Col 2: Project Distribution --}}
        <div style="padding:16px 20px;border-right:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Distribusi Proyek</h3>
                <span style="padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary)">{{ $projectStatusDistribution['total'] ?? 0 }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
                @forelse(($projectStatusDistribution['groups'] ?? []) as $statusGroup)
                <div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
                        <div style="display:flex;align-items:center;gap:7px">
                            <span style="width:8px;height:8px;border-radius:50%;background:{{ $statusGroup['color'] ?? 'var(--dark-text-secondary)' }};flex-shrink:0"></span>
                            <span style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary)">{{ $statusGroup['label'] ?? '-' }}</span>
                        </div>
                        <span style="font-size:0.9rem;font-weight:700;color:var(--dark-text-secondary)">{{ $statusGroup['count'] ?? 0 }}</span>
                    </div>
                    <div style="padding-left:15px;display:flex;flex-direction:column;gap:2px;max-height:64px;overflow-y:auto">
                        @foreach(($statusGroup['projects'] ?? []) as $proj)
                        <a href="{{ route('projects.show', $proj['id'] ?? 0) }}" style="font-size:0.7rem;color:var(--dark-text-secondary);text-decoration:none;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">· {{ $proj['name'] ?? '-' }}</a>
                        @endforeach
                    </div>
                </div>
                @empty
                <p style="font-size:0.78rem;color:var(--dark-text-secondary)">Belum ada proyek aktif</p>
                @endforelse
            </div>
        </div>

        {{-- Col 3: Recent Activity --}}
        <div style="padding:16px 20px;display:flex;flex-direction:column">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Aktivitas Terkini</h3>
                <span style="font-size:0.65rem;color:var(--dark-text-secondary)">{{ count($recentActivities ?? []) }} aktivitas</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:5px;max-height:270px;overflow-y:auto;flex:1">
                @forelse(($recentActivities ?? []) as $activity)
                <div style="display:flex;align-items:flex-start;gap:10px;padding:9px 12px;border-radius:10px;background:var(--dark-bg-tertiary)">
                    <div style="width:28px;height:28px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:{{ $activity['icon_bg'] ?? 'rgba(10,132,255,0.2)' }}">
                        <i class="fas {{ $activity['icon'] ?? 'fa-info' }}" style="font-size:0.6rem;color:{{ $activity['icon_color'] ?? 'var(--apple-blue)' }}"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $activity['title'] ?? '-' }}</p>
                        <div style="display:flex;align-items:center;gap:6px;font-size:0.68rem;color:var(--dark-text-secondary);margin-top:2px">
                            <span>{{ $activity['project'] ?? $activity['type'] ?? '' }}</span>
                            <span>·</span>
                            <span>{{ $activity['time_ago'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:32px 0">
                    <div style="width:38px;height:38px;border-radius:50%;background:var(--dark-bg-tertiary);display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                        <i class="fas fa-history" style="color:var(--dark-text-secondary);font-size:0.9rem"></i>
                    </div>
                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">Tidak Ada Aktivitas</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Belum ada aktivitas tercatat</p>
                </div>
                @endforelse
            </div>
            @if(count($recentActivities ?? []) > 0)
            <div style="padding-top:10px;margin-top:8px;border-top:1px solid var(--dark-separator)">
                <a href="{{ route('projects.index') }}" style="font-size:0.78rem;color:var(--apple-blue);text-decoration:none" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">Lihat semua aktivitas →</a>
            </div>
            @endif
        </div>

    </div>
</div>
