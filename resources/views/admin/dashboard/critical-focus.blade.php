{{-- Critical Focus Section --}}
@php
    $statusColor = $cashFlowStatus['status_color'] ?? '#FF3B30';
    $overdueProjects = $criticalAlerts['overdue_projects'] ?? [];
    $overdueTasks    = $criticalAlerts['overdue_tasks'] ?? [];
    $pendingDocs     = $pendingApprovals['documents'] ?? [];
    $totalUrgent     = $criticalAlerts['total_urgent'] ?? 0;
    $cashStatus      = $cashFlowStatus['status'] ?? 'unknown';
    $isAlert         = $totalUrgent > 0 || $cashStatus === 'critical';

    // Only show "Fokus Kritis" (alarming framing) when there are actual issues.
    // When stable, soften the label so it doesn't create false urgency.
    $sectionLabel = $isAlert ? 'Prioritas Utama' : 'Ikhtisar';
    $sectionTitle = $isAlert ? 'Fokus Kritis' : 'Semua Terkendali';
    $sectionIcon  = $isAlert ? 'fa-exclamation-circle' : 'fa-shield-alt';
    $sectionIconColor = $isAlert ? 'var(--apple-red)' : 'var(--apple-green)';
@endphp

<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
    {{-- Section Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">{{ $sectionLabel }}</p>
            <h2 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0;display:flex;align-items:center;gap:8px">
                <i class="fas {{ $sectionIcon }}" style="font-size:0.8rem;color:{{ $sectionIconColor }}"></i>
                {{ $sectionTitle }}
            </h2>
        </div>
        <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:0.75rem;font-weight:600;background:color-mix(in srgb,{{ $isAlert ? 'var(--apple-red)' : 'var(--apple-green)' }} 15%,transparent);color:{{ $isAlert ? 'var(--apple-red)' : 'var(--apple-green)' }}">
            <i class="fas {{ $isAlert ? 'fa-exclamation-triangle' : 'fa-check-circle' }}"></i>
            {{ $isAlert ? 'Perhatian' : 'Stabil' }}
        </span>
    </div>

    {{-- 3-col grid --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0">

        {{-- Col 1: Urgent Items --}}
        <div style="padding:16px 20px;border-right:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Memerlukan Penanganan</h3>
                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;padding:0 6px;border-radius:11px;font-size:0.7rem;font-weight:700;background:color-mix(in srgb,var(--apple-red) 20%,transparent);color:var(--apple-red)">{{ $totalUrgent }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;max-height:260px;overflow-y:auto">
                @if(count($overdueProjects))
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Proyek Terlambat</p>
                    @foreach($overdueProjects as $project)
                    <a href="{{ route('projects.show', $project) }}" style="display:block;padding:10px 12px;border-radius:10px;background:color-mix(in srgb,var(--apple-red) 8%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-red) 20%,var(--dark-separator));text-decoration:none">
                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $project->name ?? '-' }}</p>
                        <p style="font-size:0.7rem;color:var(--apple-orange);margin:2px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>Terlambat {{ $project->days_overdue ?? 0 }} hari</p>
                        @if($project->institution ?? null)
                        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:1px 0 0">{{ $project->institution->name }}</p>
                        @endif
                    </a>
                    @endforeach
                @endif
                @if(count($overdueTasks))
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:8px 0 4px">Tugas Terlambat</p>
                    @foreach($overdueTasks as $task)
                    <a href="{{ route('tasks.show', $task) }}" style="display:block;padding:10px 12px;border-radius:10px;background:color-mix(in srgb,var(--apple-orange) 8%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-orange) 20%,var(--dark-separator));text-decoration:none">
                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $task->title ?? '-' }}</p>
                        <p style="font-size:0.7rem;color:var(--apple-orange);margin:2px 0 0"><i class="fas fa-clock" style="margin-right:4px"></i>Terlambat {{ $task->days_overdue ?? 0 }} hari</p>
                        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:1px 0 0">{{ $task->assignedUser->name ?? 'Belum ditugaskan' }}</p>
                    </a>
                    @endforeach
                @endif
                @if(!count($overdueProjects) && !count($overdueTasks))
                <div style="text-align:center;padding:28px 0">
                    <div style="width:36px;height:36px;border-radius:50%;background:color-mix(in srgb,var(--apple-green) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                        <i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.9rem"></i>
                    </div>
                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">Semua Terkendali</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Tidak ada isu mendesak</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Col 2: Cash Flow Status --}}
        <div style="padding:16px 20px;border-right:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Kondisi Keuangan</h3>
                <span style="padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:700;text-transform:uppercase;background:{{ $statusColor }}20;color:{{ $statusColor }}">
                    {{ $cashStatus }}
                </span>
            </div>
            {{-- Main metrics --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
                <div style="padding:12px;border-radius:12px;background:color-mix(in srgb,var(--dark-bg-tertiary) 80%,transparent);border:1px solid var(--dark-separator)">
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Saldo</p>
                    <p style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Rp {{ number_format(($cashFlowStatus['current_balance'] ?? 0)/1000000,1) }}M</p>
                </div>
                <div style="padding:12px;border-radius:12px;background:color-mix(in srgb,{{ $statusColor }} 10%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,{{ $statusColor }} 25%,var(--dark-separator))">
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Proyeksi</p>
                    @php $runwayVal = $cashFlowStatus['runway_months'] ?? 0; $burnRate = $cashFlowStatus['monthly_burn_rate'] ?? 0; $balance = $cashFlowStatus['current_balance'] ?? 0; @endphp
                    <p style="font-size:1.1rem;font-weight:700;color:{{ $statusColor }};margin:3px 0 0">{{ ($runwayVal == 0 && $balance <= 0 && $burnRate == 0) ? 'N/A' : $runwayVal.' bln' }}</p>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px">
                <div style="padding:10px 12px;border-radius:10px;background:var(--dark-bg-tertiary)">
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Burn Rate</p>
                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:2px 0 0">{{ number_format(($cashFlowStatus['monthly_burn_rate'] ?? 0)/1000000,1) }}M/bln</p>
                </div>
                @php $hasOverdue = ($cashFlowStatus['overdue_invoices'] ?? 0) > 0; @endphp
                <div style="padding:10px 12px;border-radius:10px;background:var(--dark-bg-tertiary)">
                    <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:0">Overdue</p>
                    <p style="font-size:0.85rem;font-weight:600;color:{{ $hasOverdue ? 'var(--apple-red)' : 'var(--apple-green)' }};margin:2px 0 0">
                        {{ $hasOverdue ? 'Rp '.number_format(($cashFlowStatus['overdue_invoices'] ?? 0)/1000000,1).'M' : '—' }}
                    </p>
                </div>
            </div>
            <div style="padding:10px 14px;border-radius:10px;background:color-mix(in srgb,var(--apple-blue) 10%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator))">
                <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">
                    <i class="fas fa-lightbulb" style="color:var(--apple-blue);margin-right:5px"></i>Prioritaskan penagihan klien utama untuk menjaga kas di atas 4 bulan.
                </p>
            </div>
        </div>

        {{-- Col 3: Pending Approvals --}}
        <div style="padding:16px 20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Dokumen Tertunda</h3>
                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:22px;padding:0 6px;border-radius:11px;font-size:0.7rem;font-weight:700;background:color-mix(in srgb,var(--apple-purple) 20%,transparent);color:var(--apple-purple)">{{ $pendingApprovals['total_pending'] ?? 0 }}</span>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;max-height:220px;overflow-y:auto">
                @forelse($pendingDocs as $doc)
                <div style="padding:10px 12px;border-radius:10px;background:color-mix(in srgb,var(--apple-purple) 8%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-purple) 20%,var(--dark-separator))">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:160px">{{ $doc->name ?? $doc['name'] ?? '-' }}</p>
                        <span style="font-size:0.68rem;font-weight:600;padding:2px 7px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 25%,transparent);color:var(--apple-purple)">{{ $doc->days_pending ?? $doc['days_pending'] ?? 0 }}h</span>
                    </div>
                    <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:2px 0 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $doc->project_name ?? $doc['project_name'] ?? '' }}</p>
                </div>
                @empty
                <div style="text-align:center;padding:28px 0">
                    <div style="width:36px;height:36px;border-radius:50%;background:color-mix(in srgb,var(--apple-green) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                        <i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.9rem"></i>
                    </div>
                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">Semua Tertib</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Tidak ada dokumen menunggu</p>
                </div>
                @endforelse
            </div>
            @if(count($pendingDocs) > 0)
            <div style="padding-top:10px;margin-top:8px;border-top:1px solid var(--dark-separator)">
                <a href="{{ route('documents.index') }}" style="font-size:0.78rem;color:var(--apple-blue);text-decoration:none" onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">Lihat semua dokumen →</a>
            </div>
            @endif
        </div>

    </div>
</div>
