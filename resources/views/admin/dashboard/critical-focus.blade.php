    {{-- Critical focus section --}}
    <section class="card-elevated rounded-apple-lg p-3" role="region" aria-labelledby="critical-focus">
        <div class="flex items-center justify-between mb-2">
            <div>
                <h2 id="critical-focus" class="admin-section text-white flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-apple-red" style="font-size: 0.75rem;"></i>
                    Fokus Kritis
                </h2>
                <p class="admin-body text-dark-text-secondary">Isu mendesak, arus kas, dan persetujuan dokumen</p>
            </div>
            
            {{-- Status Badge --}}
            @if(($criticalAlerts['total_urgent'] ?? 0) > 0 || ($cashFlowStatus['status'] ?? '') === 'critical')
            <span class="px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1.5 bg-apple-red/15 text-apple-red">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                Perhatian
            </span>
            @else
            <span class="px-2 py-1 rounded-full text-xs font-semibold flex items-center gap-1.5 bg-apple-green/15 text-apple-green">
                <i class="fas fa-check-circle"></i>Stabil
            </span>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-3">
            {{-- Urgent board --}}
            <div class="card-elevated rounded-apple p-3 flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="admin-section text-white">Memerlukan Penanganan</h3>
                    <span class="admin-badge bg-apple-red/12 text-apple-red/90">
                        {{ $criticalAlerts['total_urgent'] ?? 0 }}
                    </span>
                </div>
                <div class="space-y-2 overflow-y-auto flex-1" style="max-height: 240px;">
                    @php
                        $overdueProjects = $criticalAlerts['overdue_projects'] ?? [];
                        $overdueTasks = $criticalAlerts['overdue_tasks'] ?? [];
                        $projectsCount = count($overdueProjects);
                        $tasksCount = count($overdueTasks);
                    @endphp
                    @if($projectsCount)
                        <p class="admin-small uppercase tracking-widest text-dark-text-tertiary">Proyek Terlambat</p>
                        @foreach($overdueProjects as $project)
                        <a href="{{ route('projects.show', $project) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple bg-apple-red/8">
                            <p class="admin-body font-medium text-white truncate">{{ $project->name ?? '-' }}</p>
                            <p class="admin-small text-apple-orange">
                                <i class="fas fa-exclamation-circle mr-1"></i>Terlambat {{ $project->days_overdue ?? 0 }} hari
                            </p>
                            @if($project->institution ?? null)
                            <p class="admin-small text-dark-text-tertiary">{{ $project->institution->name }}</p>
                            @endif
                        </a>
                        @endforeach
                    @endif

                    @if($tasksCount)
                        <p class="admin-small uppercase tracking-widest text-dark-text-tertiary mt-2">Tugas Terlambat</p>
                        @foreach($overdueTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="block p-2 rounded-apple hover:bg-dark-elevated-2 transition-apple bg-apple-orange/8">
                            <p class="admin-body font-medium text-white truncate">{{ $task->title ?? '-' }}</p>
                            <p class="admin-small text-apple-orange"><i class="fas fa-clock mr-1"></i>Terlambat {{ $task->days_overdue ?? 0 }} hari</p>
                            <p class="admin-small text-dark-text-tertiary">{{ $task->assignedUser->name ?? 'Belum ditugaskan' }}</p>
                        </a>
                        @endforeach
                    @endif

                    @if(!$projectsCount && !$tasksCount)
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center bg-apple-green/12">
                            <i class="fas fa-check-circle text-apple-green"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Semua Terkendali</p>
                        <p class="admin-small text-dark-text-tertiary">Tidak ada isu mendesak</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Cash flow status --}}
            <div class="card-elevated rounded-apple p-3 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="admin-section text-white">Kondisi Keuangan</h3>
                    @php $statusColor = $cashFlowStatus['status_color'] ?? '#FF3B30'; @endphp
                    <span class="admin-badge uppercase" style="background: {{ $statusColor }}20; color: {{ $statusColor }};">
                        {{ $cashFlowStatus['status'] ?? 'N/A' }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Saldo</p>
                        <p class="admin-stat text-white">Rp {{ number_format(($cashFlowStatus['current_balance'] ?? 0)/1000000, 1) }}M</p>
                    </div>
                    <div>
                        <p class="admin-small text-dark-text-tertiary">Proyeksi</p>
                        <p class="admin-stat" style="color: {{ $statusColor }};">{{ $cashFlowStatus['runway_months'] ?? 0 }} bln</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-2 rounded-apple bg-dark-separator">
                        <p class="admin-small text-dark-text-tertiary">Burn Rate</p>
                        <p class="admin-body font-semibold text-white">{{ number_format(($cashFlowStatus['monthly_burn_rate'] ?? 0) / 1000000, 1) }}M/bln</p>
                    </div>
                    <div class="p-2 rounded-apple bg-dark-separator">
                        <p class="admin-small text-dark-text-tertiary">Overdue</p>
                        @php $hasOverdue = ($cashFlowStatus['overdue_invoices'] ?? 0) > 0; @endphp
                        <p class="admin-body font-semibold {{ $hasOverdue ? 'text-apple-red' : 'text-apple-green' }}">
                            {{ $hasOverdue ? number_format(($cashFlowStatus['overdue_invoices'] ?? 0)/1000000,1).'M' : '0' }}
                        </p>
                    </div>
                </div>
                <div class="rounded-apple p-2 bg-apple-blue/12">
                    <p class="admin-small text-dark-text-secondary">
                        Prioritaskan penagihan {{ $cashFlowStatus['top_client'] ?? 'klien utama' }} untuk menjaga kas di atas 4 bulan.
                    </p>
                </div>
            </div>

            {{-- Pending approvals --}}
            <div class="card-elevated rounded-apple p-3 flex flex-col">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="admin-section text-white">Dokumen Tertunda</h3>
                    <span class="admin-badge bg-apple-purple/15 text-apple-purple/95">
                        {{ $pendingApprovals['total_pending'] ?? 0 }}
                    </span>
                </div>
                <div class="space-y-2 overflow-y-auto flex-1" style="max-height: 200px;">
                    @php $pendingDocs = $pendingApprovals['documents'] ?? []; @endphp
                    @forelse($pendingDocs as $doc)
                    <div class="p-2 rounded-apple bg-apple-purple/8">
                        <div class="flex items-center justify-between">
                            <p class="admin-body font-medium text-white truncate">{{ $doc->name ?? $doc['name'] ?? '-' }}</p>
                            <span class="admin-small px-1.5 py-0.5 rounded-full bg-apple-purple/20 text-apple-purple/95">{{ $doc->days_pending ?? $doc['days_pending'] ?? 0 }}h</span>
                        </div>
                        <p class="admin-small text-dark-text-tertiary truncate">{{ $doc->project_name ?? $doc['project_name'] ?? '' }}</p>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="admin-stat-icon mx-auto mb-2 rounded-full flex items-center justify-center bg-apple-green/12">
                            <i class="fas fa-check-circle text-apple-green"></i>
                        </div>
                        <p class="admin-body font-medium text-white">Semua Tertib</p>
                        <p class="admin-small text-dark-text-tertiary">Tidak ada dokumen menunggu</p>
                    </div>
                    @endforelse
                </div>
                @if(count($pendingDocs) > 0)
                <div class="mt-2 pt-2 border-t border-gray-700/50">
                    <a href="{{ route('documents.index') }}" class="admin-body text-apple-blue hover:underline">Lihat semua dokumen →</a>
                </div>
                @endif
            </div>
        </div>
    </section>

