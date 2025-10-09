<!-- Permits Tab Content -->
<div class="card-elevated rounded-apple-lg p-4 permits-tab-no-hover">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-base font-semibold" style="color: #FFFFFF;">
                <i class="fas fa-certificate mr-2 text-apple-blue-dark"></i>Izin & Prasyarat Proyek
            </h3>
            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                Kelola izin dan dependensi yang diperlukan proyek
            </p>
        </div>
        
        @if($project->permits->count() === 0)
            <div class="flex gap-2">
                <button onclick="event.stopPropagation(); showTemplateModal()" 
                        class="px-4 py-2 rounded-lg transition-colors" 
                        style="background: rgba(255, 149, 0, 0.2); color: rgba(255, 149, 0, 1);">
                    <i class="fas fa-layer-group mr-2"></i>Gunakan Template
                </button>
                <button onclick="event.stopPropagation(); showAddPermitModal()" 
                        class="px-4 py-2 rounded-lg transition-colors" 
                        style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                    <i class="fas fa-plus mr-2"></i>Tambah Izin Manual
                </button>
            </div>
        @else
            <button onclick="event.stopPropagation(); showAddPermitModal()" 
                    class="px-4 py-2 rounded-lg transition-colors" 
                    style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                <i class="fas fa-plus mr-2"></i>Tambah Izin
            </button>
        @endif
    </div>

    @if($project->permits->count() > 0)
        <!-- Goal Permit Summary -->
        @php
            $goalPermit = $project->permits->where('is_goal_permit', true)->first();
        @endphp
        @if($goalPermit)
            @php
                // Status mapping (support uppercase dari DB)
                $statusLower = strtolower($goalPermit->status);
                $goalStatusColors = [
                    'not_started' => 'rgba(142, 142, 147, 1)',
                    'in_progress' => 'rgba(10, 132, 255, 1)',
                    'submitted' => 'rgba(48, 209, 88, 1)',
                    'under_review' => 'rgba(255, 149, 0, 1)',
                    'revision_required' => 'rgba(255, 204, 0, 1)',
                    'approved' => 'rgba(52, 199, 89, 1)',
                    'rejected' => 'rgba(255, 59, 48, 1)',
                    'on_hold' => 'rgba(175, 82, 222, 1)',
                    'cancelled' => 'rgba(142, 142, 147, 1)',
                ];
                $goalStatusLabels = [
                    'not_started' => 'Belum Dimulai',
                    'in_progress' => 'Dalam Proses',
                    'submitted' => 'Sudah Diajukan',
                    'under_review' => 'Dalam Review',
                    'revision_required' => 'Perlu Revisi',
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                    'on_hold' => 'Ditunda',
                    'cancelled' => 'Dibatalkan',
                ];
                $goalStatusColor = $goalStatusColors[$statusLower] ?? 'rgba(142, 142, 147, 1)';
                $goalStatusLabel = $goalStatusLabels[$statusLower] ?? $goalPermit->status;
            @endphp
            <div class="mb-3 p-3 rounded-lg" style="background: rgba(10, 132, 255, 0.1); border: 2px solid rgba(10, 132, 255, 0.3);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold mb-1" style="color: rgba(10, 132, 255, 1);">
                            <i class="fas fa-flag mr-1"></i>IZIN TUJUAN
                        </p>
                        <p class="text-base font-bold" style="color: #FFFFFF;">
                            {{ $goalPermit->permitType->name }}
                        </p>
                        <p class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.6);">
                            {{ $goalPermit->institutionName }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" 
                              style="background: {{ $goalStatusColor }}20; color: {{ $goalStatusColor }};">
                            {{ $goalStatusLabel }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
            <div class="p-3 rounded-lg" style="background: rgba(58, 58, 60, 0.3);">
                <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Total Izin</p>
                <p class="text-xl font-bold" style="color: #FFFFFF;">{{ $statistics['total'] ?? 0 }}</p>
            </div>
            <div class="p-3 rounded-lg" style="background: rgba(52, 199, 89, 0.1);">
                <p class="text-xs mb-1" style="color: rgba(52, 199, 89, 0.8);">Selesai</p>
                <p class="text-xl font-bold" style="color: rgba(52, 199, 89, 1);">
                    {{ $statistics['completed'] ?? 0 }}
                </p>
                <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                    {{ $statistics['completion_rate'] ?? 0 }}%
                </p>
            </div>
            <div class="p-3 rounded-lg" style="background: rgba(255, 149, 0, 0.1);">
                <p class="text-xs mb-1" style="color: rgba(255, 149, 0, 0.8);">Dalam Proses</p>
                <p class="text-xl font-bold" style="color: rgba(255, 149, 0, 1);">
                    {{ $statistics['in_progress'] ?? 0 }}
                </p>
            </div>
            <div class="p-3 rounded-lg" style="background: rgba(142, 142, 147, 0.1);">
                <p class="text-xs mb-1" style="color: rgba(142, 142, 147, 0.8);">Belum Dimulai</p>
                <p class="text-xl font-bold" style="color: rgba(142, 142, 147, 1);">
                    {{ $statistics['not_started'] ?? 0 }}
                </p>
            </div>
        </div>

        <!-- Permit Flow Diagram -->
        <div id="permits-sortable" class="space-y-2">
            <h4 class="text-xs font-semibold mb-2" style="color: rgba(235, 235, 245, 0.8);">
                <i class="fas fa-sitemap mr-1"></i>Alur Izin & Dependensi
                <span class="ml-2 text-xs" style="color: rgba(235, 235, 245, 0.5);">(Drag untuk mengubah urutan)</span>
            </h4>

            @php
                $nonGoalPermits = $project->permits->where('is_goal_permit', false)->sortBy('sequence_order');
                $goalPermit = $project->permits->where('is_goal_permit', true)->first();
            @endphp

            {{-- Non-Goal Permits (Draggable) --}}
            @foreach($nonGoalPermits as $permit)
                <div class="relative" 
                     data-permit-id="{{ $permit->id }}"
                     data-sequence="{{ $permit->sequence_order }}"
                     data-permit-name="{{ $permit->permitType->name }}"
                     data-institution="{{ $permit->institutionName }}"
                     data-status="{{ $permit->status }}"
                     data-can-start="{{ $permit->canStart() ? 'true' : 'false' }}"
                     data-blockers="{{ json_encode($permit->getBlockers()) }}"
                     data-start-date="{{ $permit->start_date?->format('Y-m-d') ?? '' }}"
                     data-end-date="{{ $permit->end_date?->format('Y-m-d') ?? '' }}"
                     data-notes="{{ $permit->notes ?? '' }}">
                    <!-- Permit Card -->
                    <div class="permit-card p-2 rounded-lg" 
                         style="background: rgba(58, 58, 60, 0.5); transition: all 0.2s ease;">
                        
                        <div class="flex items-start gap-2">
                            <!-- Drag Handle -->
                            <div class="drag-handle flex-shrink-0 cursor-move opacity-50 hover:opacity-100 transition-opacity" 
                                 style="color: rgba(235, 235, 245, 0.6);" 
                                 title="Drag untuk mengubah urutan">
                                <i class="fas fa-grip-vertical text-base"></i>
                            </div>
                            
                            <!-- Sequence Badge -->
                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" 
                                 style="background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);">
                                {{ $loop->iteration }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <!-- Permit Header -->
                                <div class="flex items-start justify-between mb-1">
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-sm font-semibold truncate" style="color: #FFFFFF;">
                                            {{ $permit->permitType->name }}
                                        </h5>
                                        <p class="text-xs mt-0.5 truncate" style="color: rgba(235, 235, 245, 0.6);">
                                            {{ $permit->institutionName }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <!-- Status Badge -->
                                        @php
                                            $permitStatusLower = strtolower($permit->status);
                                            $statusColors = [
                                                'not_started' => 'rgba(142, 142, 147, 1)',
                                                'in_progress' => 'rgba(10, 132, 255, 1)',
                                                'submitted' => 'rgba(48, 209, 88, 1)',
                                                'under_review' => 'rgba(255, 149, 0, 1)',
                                                'revision_required' => 'rgba(255, 204, 0, 1)',
                                                'approved' => 'rgba(52, 199, 89, 1)',
                                                'rejected' => 'rgba(255, 59, 48, 1)',
                                                'on_hold' => 'rgba(175, 82, 222, 1)',
                                                'cancelled' => 'rgba(142, 142, 147, 1)',
                                            ];
                                            $statusLabels = [
                                                'not_started' => 'Belum Dimulai',
                                                'in_progress' => 'Dalam Proses',
                                                'submitted' => 'Sudah Diajukan',
                                                'under_review' => 'Dalam Review',
                                                'revision_required' => 'Perlu Revisi',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                'on_hold' => 'Ditunda',
                                                'cancelled' => 'Dibatalkan',
                                            ];
                                            $statusColor = $statusColors[$permitStatusLower] ?? 'rgba(142, 142, 147, 1)';
                                            $statusLabel = $statusLabels[$permitStatusLower] ?? $permit->status;
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full whitespace-nowrap" 
                                              style="background: {{ $statusColor }}20; color: {{ $statusColor }};">
                                            {{ $statusLabel }}
                                        </span>
                                        
                                        <!-- Actions -->
                                        <div class="flex gap-0.5">
                                            <button onclick="event.stopPropagation(); showManageDependenciesModal({{ $permit->id }})" 
                                                    class="p-1.5 rounded transition-colors"
                                                    style="color: rgba(175, 82, 222, 1);" 
                                                    onmouseover="this.style.background='rgba(175, 82, 222, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Kelola Dependensi">
                                                <i class="fas fa-project-diagram text-xs"></i>
                                            </button>
                                            <button onclick="event.stopPropagation(); updatePermitStatus({{ $permit->id }})" 
                                                    class="p-1.5 rounded transition-colors"
                                                    style="color: rgba(10, 132, 255, 1);" 
                                                    onmouseover="this.style.background='rgba(10, 132, 255, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Update Status">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button onclick="event.stopPropagation(); deletePermit({{ $permit->id }})" 
                                                    class="p-1.5 rounded transition-colors"
                                                    style="color: rgba(255, 59, 48, 1);" 
                                                    onmouseover="this.style.background='rgba(255, 59, 48, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Hapus Izin">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alert System: Warnings & Notifications -->
                                @php
                                    $alerts = [];
                                    $now = \Carbon\Carbon::now();
                                    
                                    // Check for blocked permits
                                    if(strtolower($permit->status) === 'not_started' && !$permit->canStart()) {
                                        $alerts[] = [
                                            'type' => 'blocked',
                                            'color' => 'rgba(255, 59, 48, 1)',
                                            'bg' => 'rgba(255, 59, 48, 0.1)',
                                            'icon' => 'fa-lock',
                                            'text' => 'Diblokir: ' . implode(', ', $permit->getBlockers())
                                        ];
                                    }
                                    
                                    // Check for expiring deadlines (within 7 days)
                                    if($permit->target_date && strtolower($permit->status) !== 'approved') {
                                        $daysUntil = $now->diffInDays($permit->target_date, false);
                                        if($daysUntil < 0) {
                                            $alerts[] = [
                                                'type' => 'overdue',
                                                'color' => 'rgba(255, 59, 48, 1)',
                                                'bg' => 'rgba(255, 59, 48, 0.1)',
                                                'icon' => 'fa-exclamation-triangle',
                                                'text' => 'Terlambat ' . abs($daysUntil) . ' hari'
                                            ];
                                        } elseif($daysUntil <= 7) {
                                            $alerts[] = [
                                                'type' => 'expiring',
                                                'color' => 'rgba(255, 149, 0, 1)',
                                                'bg' => 'rgba(255, 149, 0, 0.1)',
                                                'icon' => 'fa-clock',
                                                'text' => 'Deadline dalam ' . $daysUntil . ' hari'
                                            ];
                                        }
                                    }
                                    
                                    // Check for expiring valid_until (permits that expire)
                                    if($permit->valid_until && strtolower($permit->status) === 'approved') {
                                        $daysUntilExpiry = $now->diffInDays($permit->valid_until, false);
                                        if($daysUntilExpiry < 0) {
                                            $alerts[] = [
                                                'type' => 'expired',
                                                'color' => 'rgba(255, 59, 48, 1)',
                                                'bg' => 'rgba(255, 59, 48, 0.1)',
                                                'icon' => 'fa-times-circle',
                                                'text' => 'Izin kadaluarsa sejak ' . abs($daysUntilExpiry) . ' hari lalu'
                                            ];
                                        } elseif($daysUntilExpiry <= 30) {
                                            $alerts[] = [
                                                'type' => 'expiring_permit',
                                                'color' => 'rgba(255, 149, 0, 1)',
                                                'bg' => 'rgba(255, 149, 0, 0.1)',
                                                'icon' => 'fa-exclamation-circle',
                                                'text' => 'Izin akan kadaluarsa dalam ' . $daysUntilExpiry . ' hari'
                                            ];
                                        }
                                    }
                                @endphp

                                @if(count($alerts) > 0)
                                    <div class="mt-2 space-y-1.5">
                                        @foreach($alerts as $alert)
                                            <div class="p-1.5 rounded-lg text-xs flex items-start gap-1.5" 
                                                 style="background: {{ $alert['bg'] }}; color: {{ $alert['color'] }};">
                                                <i class="fas {{ $alert['icon'] }} mt-0.5 text-xs"></i>
                                                <span>{{ $alert['text'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Can Start Check -->
                                @if(strtolower($permit->status) === 'not_started')
                                    @if($permit->canStart())
                                        <div class="mt-2 p-1.5 rounded-lg text-xs" 
                                             style="background: rgba(52, 199, 89, 0.1); color: rgba(52, 199, 89, 1);">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Siap dimulai - Semua prasyarat terpenuhi
                                        </div>
                                    @else
                                        <div class="mt-2 p-1.5 rounded-lg text-xs" 
                                             style="background: rgba(255, 59, 48, 0.1); color: rgba(255, 59, 48, 1);">
                                            <i class="fas fa-lock mr-1"></i>
                                            Menunggu prasyarat: 
                                            @foreach($permit->getBlockers() as $blocker)
                                                <strong>{{ $blocker }}</strong>{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                <!-- Dependencies -->
                                @if($permit->dependencies->count() > 0)
                                    <div class="mt-2 pt-2" style="border-top: 1px solid rgba(58, 58, 60, 0.8);">
                                        <p class="text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-link mr-1"></i>PRASYARAT:
                                        </p>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($permit->dependencies as $dep)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full group" 
                                                      style="background: {{ $dep->can_proceed_without ? 'rgba(255, 149, 0, 0.2)' : 'rgba(255, 59, 48, 0.2)' }}; 
                                                             color: {{ $dep->can_proceed_without ? 'rgba(255, 149, 0, 1)' : 'rgba(255, 59, 48, 1)' }};">
                                                    @if(!$dep->can_proceed_without)
                                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                                    @else
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                    @endif
                                                    {{ $dep->dependsOnPermit->permitType->name }}
                                                    ({{ $dep->can_proceed_without ? 'Opsional' : 'Wajib' }})
                                                    <button onclick="removeDependency({{ $permit->id }}, {{ $dep->id }}, event)" 
                                                            class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                                            title="Hapus dependensi">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Timeline -->
                                @if($permit->start_date || $permit->end_date)
                                    <div class="mt-2 grid grid-cols-2 gap-3 text-xs">
                                        @if($permit->start_date)
                                            <div>
                                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Mulai:</p>
                                                <p style="color: rgba(235, 235, 245, 0.8);">
                                                    {{ $permit->start_date->format('d M Y') }}
                                                </p>
                                            </div>
                                        @endif
                                        @if($permit->end_date)
                                            <div>
                                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Selesai:</p>
                                                <p style="color: rgba(235, 235, 245, 0.8);">
                                                    {{ $permit->end_date->format('d M Y') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Notes -->
                                @if($permit->notes)
                                    <div class="mt-2 p-2 rounded-lg text-xs" style="background: rgba(58, 58, 60, 0.5);">
                                        <p class="text-xs font-semibold mb-1" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-sticky-note mr-1"></i>Catatan:
                                        </p>
                                        <p style="color: rgba(235, 235, 245, 0.8);">{{ $permit->notes }}</p>
                                    </div>
                                @endif

                                <!-- Documents Section -->
                                <div class="mt-2 pt-2" style="border-top: 1px solid rgba(58, 58, 60, 0.8);">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <p class="text-xs font-semibold" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-paperclip mr-1"></i>DOKUMEN ({{ $permit->documents->count() }})
                                        </p>
                                        <button onclick="event.stopPropagation(); showUploadModal({{ $permit->id }})" 
                                                class="px-3 py-1 rounded-lg text-xs font-medium transition-all"
                                                style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);"
                                                onmouseover="this.style.background='rgba(10, 132, 255, 0.3)'"
                                                onmouseout="this.style.background='rgba(10, 132, 255, 0.2)'"
                                                title="Upload Dokumen">
                                            <i class="fas fa-upload mr-1"></i>Upload
                                        </button>
                                    </div>
                                    
                                    @if($permit->documents->count() > 0)
                                        <div class="space-y-1.5">
                                            @foreach($permit->documents as $doc)
                                                <div class="flex items-center justify-between p-2 rounded-lg group" 
                                                     style="background: rgba(58, 58, 60, 0.5);">
                                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                                        <i class="fas {{ 
                                                            str_contains($doc->file_type, 'pdf') ? 'fa-file-pdf' : 
                                                            (str_contains($doc->file_type, 'image') ? 'fa-file-image' : 
                                                            (str_contains($doc->file_type, 'word') || str_contains($doc->file_type, 'document') ? 'fa-file-word' : 
                                                            'fa-file')) 
                                                        }}" style="color: rgba(10, 132, 255, 1);"></i>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs truncate" style="color: rgba(235, 235, 245, 0.9);">
                                                                {{ $doc->original_filename }}
                                                            </p>
                                                            <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                                                {{ $doc->file_size_formatted }} • {{ $doc->created_at->format('d M Y H:i') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <a href="{{ route('permits.documents.download', ['project' => $project->id, 'document' => $doc->id]) }}" 
                                                           class="p-2 rounded transition-colors"
                                                           style="color: rgba(10, 132, 255, 1);"
                                                           onmouseover="this.style.background='rgba(10, 132, 255, 0.1)'"
                                                           onmouseout="this.style.background='transparent'"
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button onclick="event.stopPropagation(); deleteDocument({{ $doc->id }})" 
                                                                class="p-2 rounded transition-colors"
                                                                style="color: rgba(255, 59, 48, 1);"
                                                                onmouseover="this.style.background='rgba(255, 59, 48, 0.1)'"
                                                                onmouseout="this.style.background='transparent'"
                                                                title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-center py-2" style="color: rgba(235, 235, 245, 0.5);">
                                            Belum ada dokumen
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Arrow to next -->
                    @if(!$loop->last)
                        <div class="flex justify-center my-2">
                            <i class="fas fa-arrow-down text-2xl" style="color: rgba(235, 235, 245, 0.3);"></i>
                        </div>
                    @endif
                </div>
            @endforeach

            {{-- Arrow to Goal Permit --}}
            @if($goalPermit && $nonGoalPermits->count() > 0)
                <div class="flex justify-center my-2">
                    <i class="fas fa-arrow-down text-2xl" style="color: rgba(235, 235, 245, 0.3);"></i>
                </div>
            @endif

            {{-- Goal Permit Section (Non-draggable) --}}
            @if($goalPermit)
                @php $permit = $goalPermit; @endphp
                <div class="relative" 
                     data-permit-id="{{ $permit->id }}"
                     data-sequence="{{ $permit->sequence_order }}"
                     data-permit-name="{{ $permit->permitType->name }}"
                     data-institution="{{ $permit->institutionName }}"
                     data-status="{{ $permit->status }}"
                     data-can-start="{{ $permit->canStart() ? 'true' : 'false' }}"
                     data-blockers="{{ json_encode($permit->getBlockers()) }}"
                     data-start-date="{{ $permit->start_date?->format('Y-m-d') ?? '' }}"
                     data-end-date="{{ $permit->end_date?->format('Y-m-d') ?? '' }}"
                     data-notes="{{ $permit->notes ?? '' }}">
                    <!-- Goal Permit Card (dengan ring blue) -->
                    <div class="permit-card p-2 rounded-lg ring-2 ring-blue-500" 
                         style="background: rgba(58, 58, 60, 0.5); transition: all 0.2s ease;">
                        
                        <div class="flex items-start gap-2">
                            <!-- Lock Icon (instead of drag handle) -->
                            <div class="flex-shrink-0 opacity-30" 
                                 style="color: rgba(235, 235, 245, 0.6);" 
                                 title="Goal permit tidak dapat dipindahkan">
                                <i class="fas fa-lock text-base"></i>
                            </div>
                            
                            <!-- Goal Badge -->
                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" 
                                 style="background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);">
                                <i class="fas fa-flag"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <!-- Permit Header -->
                                <div class="flex items-start justify-between mb-1">
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-sm font-semibold truncate" style="color: #FFFFFF;">
                                            {{ $permit->permitType->name }}
                                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full" 
                                                  style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                                                <i class="fas fa-flag mr-1"></i>TUJUAN
                                            </span>
                                        </h5>
                                        <p class="text-xs mt-0.5 truncate" style="color: rgba(235, 235, 245, 0.6);">
                                            {{ $permit->institutionName }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <!-- Status Badge -->
                                        @php
                                            $permitStatusLower = strtolower($permit->status);
                                            $statusColors = [
                                                'not_started' => 'rgba(142, 142, 147, 1)',
                                                'in_progress' => 'rgba(10, 132, 255, 1)',
                                                'submitted' => 'rgba(48, 209, 88, 1)',
                                                'under_review' => 'rgba(255, 149, 0, 1)',
                                                'revision_required' => 'rgba(255, 204, 0, 1)',
                                                'approved' => 'rgba(52, 199, 89, 1)',
                                                'rejected' => 'rgba(255, 59, 48, 1)',
                                                'on_hold' => 'rgba(175, 82, 222, 1)',
                                                'cancelled' => 'rgba(142, 142, 147, 1)',
                                            ];
                                            $statusLabels = [
                                                'not_started' => 'Belum Dimulai',
                                                'in_progress' => 'Dalam Proses',
                                                'submitted' => 'Sudah Diajukan',
                                                'under_review' => 'Dalam Review',
                                                'revision_required' => 'Perlu Revisi',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                'on_hold' => 'Ditunda',
                                                'cancelled' => 'Dibatalkan',
                                            ];
                                            $statusColor = $statusColors[$permitStatusLower] ?? 'rgba(142, 142, 147, 1)';
                                            $statusLabel = $statusLabels[$permitStatusLower] ?? $permit->status;
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full whitespace-nowrap" 
                                              style="background: {{ $statusColor }}20; color: {{ $statusColor }};">
                                            {{ $statusLabel }}
                                        </span>
                                        
                                        <!-- Actions -->
                                        <div class="flex gap-0.5">
                                            <button onclick="event.stopPropagation(); showManageDependenciesModal({{ $permit->id }})" 
                                                    class="p-1.5 rounded transition-colors"
                                                    style="color: rgba(175, 82, 222, 1);" 
                                                    onmouseover="this.style.background='rgba(175, 82, 222, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Kelola Dependensi">
                                                <i class="fas fa-project-diagram text-xs"></i>
                                            </button>
                                            <button onclick="event.stopPropagation(); updatePermitStatus({{ $permit->id }})" 
                                                    class="p-1.5 rounded transition-colors"
                                                    style="color: rgba(10, 132, 255, 1);" 
                                                    onmouseover="this.style.background='rgba(10, 132, 255, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Update Status">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <button onclick="event.stopPropagation(); deletePermit({{ $permit->id }})" 
                                                    class="p-1.5 rounded transition-colors"
                                                    style="color: rgba(255, 59, 48, 1);" 
                                                    onmouseover="this.style.background='rgba(255, 59, 48, 0.1)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    title="Hapus Izin">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alert System: Warnings & Notifications -->
                                @php
                                    $alerts = [];
                                    $now = \Carbon\Carbon::now();
                                    
                                    // Check for blocked permits
                                    if(strtolower($permit->status) === 'not_started' && !$permit->canStart()) {
                                        $alerts[] = [
                                            'type' => 'blocked',
                                            'color' => 'rgba(255, 59, 48, 1)',
                                            'bg' => 'rgba(255, 59, 48, 0.1)',
                                            'icon' => 'fa-lock',
                                            'text' => 'Diblokir: ' . implode(', ', $permit->getBlockers())
                                        ];
                                    }
                                    
                                    // Check for expiring deadlines (within 7 days)
                                    if($permit->target_date && strtolower($permit->status) !== 'approved') {
                                        $daysUntil = $now->diffInDays($permit->target_date, false);
                                        if($daysUntil < 0) {
                                            $alerts[] = [
                                                'type' => 'overdue',
                                                'color' => 'rgba(255, 59, 48, 1)',
                                                'bg' => 'rgba(255, 59, 48, 0.1)',
                                                'icon' => 'fa-exclamation-triangle',
                                                'text' => 'Terlambat ' . abs($daysUntil) . ' hari'
                                            ];
                                        } elseif($daysUntil <= 7) {
                                            $alerts[] = [
                                                'type' => 'expiring',
                                                'color' => 'rgba(255, 149, 0, 1)',
                                                'bg' => 'rgba(255, 149, 0, 0.1)',
                                                'icon' => 'fa-clock',
                                                'text' => 'Deadline dalam ' . $daysUntil . ' hari'
                                            ];
                                        }
                                    }
                                    
                                    // Check for expiring valid_until (permits that expire)
                                    if($permit->valid_until && strtolower($permit->status) === 'approved') {
                                        $daysUntilExpiry = $now->diffInDays($permit->valid_until, false);
                                        if($daysUntilExpiry < 0) {
                                            $alerts[] = [
                                                'type' => 'expired',
                                                'color' => 'rgba(255, 59, 48, 1)',
                                                'bg' => 'rgba(255, 59, 48, 0.1)',
                                                'icon' => 'fa-times-circle',
                                                'text' => 'Izin kadaluarsa sejak ' . abs($daysUntilExpiry) . ' hari lalu'
                                            ];
                                        } elseif($daysUntilExpiry <= 30) {
                                            $alerts[] = [
                                                'type' => 'expiring_permit',
                                                'color' => 'rgba(255, 149, 0, 1)',
                                                'bg' => 'rgba(255, 149, 0, 0.1)',
                                                'icon' => 'fa-exclamation-circle',
                                                'text' => 'Izin akan kadaluarsa dalam ' . $daysUntilExpiry . ' hari'
                                            ];
                                        }
                                    }
                                @endphp

                                @if(count($alerts) > 0)
                                    <div class="mt-2 space-y-1.5">
                                        @foreach($alerts as $alert)
                                            <div class="p-1.5 rounded-lg text-xs flex items-start gap-1.5" 
                                                 style="background: {{ $alert['bg'] }}; color: {{ $alert['color'] }};">
                                                <i class="fas {{ $alert['icon'] }} mt-0.5 text-xs"></i>
                                                <span>{{ $alert['text'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Can Start Check -->
                                @if(strtolower($permit->status) === 'not_started')
                                    @if($permit->canStart())
                                        <div class="mt-2 p-1.5 rounded-lg text-xs" 
                                             style="background: rgba(52, 199, 89, 0.1); color: rgba(52, 199, 89, 1);">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Siap dimulai - Semua prasyarat terpenuhi
                                        </div>
                                    @else
                                        <div class="mt-2 p-1.5 rounded-lg text-xs" 
                                             style="background: rgba(255, 59, 48, 0.1); color: rgba(255, 59, 48, 1);">
                                            <i class="fas fa-lock mr-1"></i>
                                            Menunggu prasyarat: 
                                            @foreach($permit->getBlockers() as $blocker)
                                                <strong>{{ $blocker }}</strong>{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </div>
                                    @endif
                                @endif

                                <!-- Dependencies -->
                                @if($permit->dependencies->count() > 0)
                                    <div class="mt-2 pt-2" style="border-top: 1px solid rgba(58, 58, 60, 0.8);">
                                        <p class="text-xs font-semibold mb-1.5" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-link mr-1"></i>PRASYARAT:
                                        </p>
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($permit->dependencies as $dep)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full group" 
                                                      style="background: {{ $dep->can_proceed_without ? 'rgba(255, 149, 0, 0.2)' : 'rgba(255, 59, 48, 0.2)' }}; 
                                                             color: {{ $dep->can_proceed_without ? 'rgba(255, 149, 0, 1)' : 'rgba(255, 59, 48, 1)' }};">
                                                    @if(!$dep->can_proceed_without)
                                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                                    @else
                                                        <i class="fas fa-info-circle mr-1"></i>
                                                    @endif
                                                    {{ $dep->dependsOnPermit->permitType->name }}
                                                    ({{ $dep->can_proceed_without ? 'Opsional' : 'Wajib' }})
                                                    <button onclick="removeDependency({{ $permit->id }}, {{ $dep->id }}, event)" 
                                                            class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                                            title="Hapus dependensi">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Timeline -->
                                @if($permit->start_date || $permit->end_date)
                                    <div class="mt-2 grid grid-cols-2 gap-3 text-xs">
                                        @if($permit->start_date)
                                            <div>
                                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Mulai:</p>
                                                <p style="color: rgba(235, 235, 245, 0.8);">
                                                    {{ $permit->start_date->format('d M Y') }}
                                                </p>
                                            </div>
                                        @endif
                                        @if($permit->end_date)
                                            <div>
                                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Selesai:</p>
                                                <p style="color: rgba(235, 235, 245, 0.8);">
                                                    {{ $permit->end_date->format('d M Y') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Notes -->
                                @if($permit->notes)
                                    <div class="mt-2 p-2 rounded-lg text-xs" style="background: rgba(58, 58, 60, 0.5);">
                                        <p class="text-xs font-semibold mb-1" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-sticky-note mr-1"></i>Catatan:
                                        </p>
                                        <p style="color: rgba(235, 235, 245, 0.8);">{{ $permit->notes }}</p>
                                    </div>
                                @endif

                                <!-- Documents Section -->
                                <div class="mt-2 pt-2" style="border-top: 1px solid rgba(58, 58, 60, 0.8);">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <p class="text-xs font-semibold" style="color: rgba(235, 235, 245, 0.6);">
                                            <i class="fas fa-paperclip mr-1"></i>DOKUMEN ({{ $permit->documents->count() }})
                                        </p>
                                        <button onclick="event.stopPropagation(); showUploadModal({{ $permit->id }})" 
                                                class="px-3 py-1 rounded-lg text-xs font-medium transition-all"
                                                style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);"
                                                onmouseover="this.style.background='rgba(10, 132, 255, 0.3)'"
                                                onmouseout="this.style.background='rgba(10, 132, 255, 0.2)'"
                                                title="Upload Dokumen">
                                            <i class="fas fa-upload mr-1"></i>Upload
                                        </button>
                                    </div>
                                    
                                    @if($permit->documents->count() > 0)
                                        <div class="space-y-1.5">
                                            @foreach($permit->documents as $doc)
                                                <div class="flex items-center justify-between p-2 rounded-lg group" 
                                                     style="background: rgba(58, 58, 60, 0.5);">
                                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                                        <i class="fas {{ 
                                                            str_contains($doc->file_type, 'pdf') ? 'fa-file-pdf' : 
                                                            (str_contains($doc->file_type, 'image') ? 'fa-file-image' : 
                                                            (str_contains($doc->file_type, 'word') || str_contains($doc->file_type, 'document') ? 'fa-file-word' : 
                                                            'fa-file')) 
                                                        }}" style="color: rgba(10, 132, 255, 1);"></i>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs truncate" style="color: rgba(235, 235, 245, 0.9);">
                                                                {{ $doc->original_filename }}
                                                            </p>
                                                            <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">
                                                                {{ $doc->file_size_formatted }} • {{ $doc->created_at->format('d M Y H:i') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <a href="{{ route('permits.documents.download', ['project' => $project->id, 'document' => $doc->id]) }}" 
                                                           class="p-2 rounded transition-colors"
                                                           style="color: rgba(10, 132, 255, 1);"
                                                           onmouseover="this.style.background='rgba(10, 132, 255, 0.1)'"
                                                           onmouseout="this.style.background='transparent'"
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button onclick="event.stopPropagation(); deleteDocument({{ $doc->id }})" 
                                                                class="p-2 rounded transition-colors"
                                                                style="color: rgba(255, 59, 48, 1);"
                                                                onmouseover="this.style.background='rgba(255, 59, 48, 0.1)'"
                                                                onmouseout="this.style.background='transparent'"
                                                                title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-xs text-center py-2" style="color: rgba(235, 235, 245, 0.5);">
                                            Belum ada dokumen
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-certificate text-6xl mb-4" style="color: rgba(235, 235, 245, 0.3);"></i>
            <h4 class="text-xl font-semibold mb-2" style="color: #FFFFFF;">Belum Ada Izin</h4>
            <p class="mb-6" style="color: rgba(235, 235, 245, 0.6);">
                Tambahkan izin yang diperlukan untuk proyek ini
            </p>
            <div class="flex justify-center gap-4">
                <button onclick="event.stopPropagation(); showTemplateModal()" 
                        class="px-6 py-3 rounded-lg font-medium transition-colors" 
                        style="background: rgba(255, 149, 0, 0.2); color: rgba(255, 149, 0, 1);">
                    <i class="fas fa-layer-group mr-2"></i>Gunakan Template
                </button>
                <button onclick="event.stopPropagation(); showAddPermitModal()" 
                        class="px-6 py-3 rounded-lg font-medium transition-colors" 
                        style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                    <i class="fas fa-plus mr-2"></i>Tambah Izin Manual
                </button>
            </div>
        </div>
    @endif
</div>

<!-- Template Selection Modal -->
<div id="template-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" style="display: none;">
    <div class="bg-[#1e1e1e] rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-[#1e1e1e] border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Pilih Template Izin</h3>
                <button onclick="event.stopPropagation(); closeTemplateModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('projects.permits.apply-template', $project) }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4 mb-6">
                @forelse($permitTemplates as $template)
                    <label class="block cursor-pointer">
                        <input type="radio" name="template_id" value="{{ $template->id }}" required 
                               class="hidden peer" onchange="selectTemplate({{ $template->id }})">
                        <div class="border-2 border-gray-700 rounded-lg p-4 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 hover:border-gray-600 transition">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-white mb-1">{{ $template->name }}</h4>
                                    <p class="text-sm text-gray-400">{{ $template->description }}</p>
                                </div>
                                <div class="ml-4 text-right">
                                    <div class="text-sm text-gray-400">{{ $template->items->count() }} izin</div>
                                    @if($template->estimated_days)
                                        <div class="text-sm text-gray-400">~{{ $template->estimated_days }} hari</div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Template Preview -->
                            <div id="preview-{{ $template->id }}" class="hidden mt-4 pt-4 border-t border-gray-700">
                                <div class="text-sm text-gray-300 space-y-2">
                                    @foreach($template->items->sortBy('sequence_order') as $item)
                                        <div class="flex items-center">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-700 text-gray-300 text-xs mr-2">
                                                {{ $item->sequence_order }}
                                            </span>
                                            <span>{{ $item->permitType->name }}</span>
                                            @if($item->dependencies->count() > 0)
                                                <span class="ml-2 text-xs text-gray-500">
                                                    (setelah: {{ $item->dependencies->pluck('dependsOnItem.sequence_order')->join(', ') }})
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </label>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>Belum ada template tersedia</p>
                    </div>
                @endforelse
            </div>

            @if($permitTemplates->count() > 0)
                <!-- Replace Option -->
                <div class="bg-gray-800/50 rounded-lg p-4 mb-6">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="replace_existing" value="1" class="mt-1 mr-3">
                        <div>
                            <div class="text-white font-medium">Ganti Semua Izin yang Ada</div>
                            <div class="text-sm text-gray-400 mt-1">
                                Jika dicentang, semua izin yang sudah ada akan dihapus dan diganti dengan template ini.
                                Jika tidak, template akan ditambahkan ke izin yang sudah ada.
                            </div>
                        </div>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="button" onclick="event.stopPropagation(); closeTemplateModal()" 
                            class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="fas fa-check mr-2"></i>Terapkan Template
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<!-- Add Permit Modal -->
<div id="add-permit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" style="display: none;">
    <div class="bg-[#1e1e1e] rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-[#1e1e1e] border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Tambah Izin Baru</h3>
                <button onclick="event.stopPropagation(); closeAddPermitModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('projects.permits.store', $project) }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Permit Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Jenis Izin <span class="text-red-500">*</span>
                    </label>
                    <select name="permit_type_id" required 
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- Pilih Jenis Izin --</option>
                        @foreach($permitTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->institution->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Is Goal Permit -->
                <div>
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_goal_permit" value="1" 
                               class="w-5 h-5 text-blue-600 bg-gray-800 border-gray-700 rounded focus:ring-blue-500">
                        <span class="ml-3 text-sm text-gray-300">
                            <i class="fas fa-star text-yellow-500 mr-1"></i>
                            Tandai sebagai Izin Utama/Tujuan Akhir
                        </span>
                    </label>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Catatan
                    </label>
                    <textarea name="notes" rows="3" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                              placeholder="Tambahkan catatan khusus untuk izin ini..."></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="event.stopPropagation(); closeAddPermitModal()" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Tambah Izin
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Status Modal -->
<div id="update-status-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" style="display: none;">
    <div class="bg-[#1e1e1e] rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-[#1e1e1e] border-b border-gray-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-white">Update Status Izin</h3>
                <button onclick="event.stopPropagation(); closeUpdateStatusModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="update-status-form" method="POST" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <!-- Permit Info (will be filled by JS) -->
                <div id="permit-info" class="bg-gray-800/50 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <span id="permit-sequence" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-600 text-white font-semibold mr-3"></span>
                        <div>
                            <div id="permit-name" class="text-white font-medium"></div>
                            <div id="permit-institution" class="text-sm text-gray-400"></div>
                        </div>
                    </div>
                </div>

                <!-- Dependency Warning (conditional) -->
                <div id="dependency-warning" class="hidden bg-red-900/20 border border-red-700 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <div class="text-red-400 font-medium mb-1">Peringatan: Prasyarat Belum Terpenuhi</div>
                            <div class="text-sm text-gray-300 mb-2">Izin ini membutuhkan prasyarat berikut yang belum selesai:</div>
                            <ul id="blocker-list" class="list-disc list-inside text-sm text-gray-400 space-y-1"></ul>
                            <div class="mt-3 text-sm text-gray-400">
                                Jika Anda tetap ingin melanjutkan, silakan berikan alasan override di bawah.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Status Display -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Status Saat Ini</label>
                    <div id="current-status" class="px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-gray-400"></div>
                </div>

                <!-- New Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Status Baru <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="new-status" required 
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                            onchange="checkOverrideNeeded()">
                        <option value="">-- Pilih Status --</option>
                        <option value="NOT_STARTED">Belum Dimulai</option>
                        <option value="IN_PROGRESS">Dalam Proses</option>
                        <option value="SUBMITTED">Sudah Diajukan</option>
                        <option value="UNDER_REVIEW">Dalam Review</option>
                        <option value="REVISION_REQUIRED">Perlu Revisi</option>
                        <option value="APPROVED">Disetujui</option>
                        <option value="REJECTED">Ditolak</option>
                        <option value="ON_HOLD">Ditunda</option>
                        <option value="CANCELLED">Dibatalkan</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tanggal Mulai</label>
                        <input type="date" name="start_date" 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Tanggal Selesai</label>
                        <input type="date" name="end_date" 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <!-- Override Reason (conditional) -->
                <div id="override-section" class="hidden">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Alasan Override <span class="text-red-500">*</span>
                    </label>
                    <textarea name="override_reason" id="override-reason" rows="4" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                              placeholder="Jelaskan mengapa izin ini perlu dilanjutkan meskipun prasyarat belum terpenuhi..."></textarea>
                    <input type="hidden" name="can_proceed_without" id="can-proceed-without" value="0">
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Catatan</label>
                    <textarea name="notes" id="permit-notes" rows="3" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500"
                              placeholder="Tambahkan catatan tambahan..."></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="event.stopPropagation(); closeUpdateStatusModal()" 
                        class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Upload Document Modal -->
<div id="upload-document-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" style="display: none;">
    <div class="rounded-lg shadow-xl max-w-lg w-full mx-4" style="background: rgba(30, 30, 30, 1);">
        <div class="sticky top-0 px-6 py-4" style="background: rgba(30, 30, 30, 1); border-bottom: 1px solid rgba(58, 58, 60, 1);">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold" style="color: #FFFFFF;">
                    <i class="fas fa-upload mr-2"></i>Upload Dokumen
                </h3>
                <button onclick="closeUploadModal()" style="color: rgba(142, 142, 147, 1);" 
                        onmouseover="this.style.color='rgba(255, 255, 255, 1)'"
                        onmouseout="this.style.color='rgba(142, 142, 147, 1)'">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="upload-document-form" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Permit Info -->
                <div class="p-4 rounded-lg" style="background: rgba(58, 58, 60, 0.5);">
                    <div class="flex items-center">
                        <span id="upload-permit-sequence" class="inline-flex items-center justify-center w-8 h-8 rounded-full font-semibold mr-3" 
                              style="background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);"></span>
                        <div>
                            <div id="upload-permit-name" class="font-medium" style="color: #FFFFFF;"></div>
                            <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Upload dokumen pendukung</div>
                        </div>
                    </div>
                </div>

                <!-- File Input -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Pilih File
                    </label>
                    <input type="file" 
                           id="document-file" 
                           name="document"
                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                           required
                           class="w-full px-4 py-2 rounded-lg border text-sm"
                           style="background: rgba(58, 58, 60, 1); border-color: rgba(99, 99, 102, 1); color: #FFFFFF;">
                    <p class="text-xs mt-2" style="color: rgba(235, 235, 245, 0.5);">
                        Format: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)
                    </p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="document-description" 
                              name="description"
                              rows="3"
                              class="w-full px-4 py-2 rounded-lg border text-sm"
                              style="background: rgba(58, 58, 60, 1); border-color: rgba(99, 99, 102, 1); color: #FFFFFF;"
                              placeholder="Keterangan tentang dokumen ini..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4" style="border-top: 1px solid rgba(58, 58, 60, 1);">
                <button type="button" 
                        onclick="closeUploadModal()"
                        class="px-6 py-2 rounded-lg text-sm font-medium transition-colors"
                        style="background: rgba(58, 58, 60, 1); color: rgba(235, 235, 245, 0.8);"
                        onmouseover="this.style.background='rgba(72, 72, 74, 1)'"
                        onmouseout="this.style.background='rgba(58, 58, 60, 1)'">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2 rounded-lg text-sm font-medium transition-colors"
                        style="background: rgba(10, 132, 255, 1); color: #FFFFFF;"
                        onmouseover="this.style.background='rgba(0, 122, 255, 1)'"
                        onmouseout="this.style.background='rgba(10, 132, 255, 1)'">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Manage Dependencies Modal -->
<div id="manage-dependencies-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" style="display: none;">
    <div class="rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" 
         style="background: #1C1C1E;">
        <div class="sticky top-0 border-b px-4 py-3" 
             style="background: #1C1C1E; border-color: rgba(58, 58, 60, 0.8);">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-semibold" style="color: #FFFFFF;">
                        <i class="fas fa-project-diagram mr-2" style="color: rgba(10, 132, 255, 1);"></i>
                        Kelola Dependensi Izin
                    </h3>
                    <span id="dep-count-badge" class="px-2 py-0.5 text-xs font-semibold rounded-full" 
                          style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1); display: none;">
                        0 Prasyarat
                    </span>
                </div>
                <button onclick="event.stopPropagation(); closeManageDependenciesModal()" 
                        class="p-1.5 rounded-lg transition-colors" 
                        style="color: rgba(235, 235, 245, 0.6);" 
                        onmouseover="this.style.background='rgba(255, 59, 48, 0.1)'; this.style.color='rgba(255, 59, 48, 1)'"
                        onmouseout="this.style.background='transparent'; this.style.color='rgba(235, 235, 245, 0.6)'">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <form id="add-dependency-form" method="POST" class="p-4">
            @csrf
            
            <div class="space-y-3">
                <!-- Current Permit Info -->
                <div id="dep-permit-info" class="rounded-lg p-3" style="background: rgba(58, 58, 60, 0.5);">
                    <div class="flex items-center gap-2">
                        <span id="dep-permit-sequence" class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold" 
                              style="background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);"></span>
                        <div class="flex-1 min-w-0">
                            <div id="dep-permit-name" class="text-sm font-semibold truncate" style="color: #FFFFFF;"></div>
                            <div class="text-xs mt-0.5" style="color: rgba(235, 235, 245, 0.6);">
                                Tambahkan prasyarat yang harus dipenuhi sebelum izin ini dapat diproses
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Dependencies List -->
                <div id="current-dependencies" class="rounded-lg p-3" style="background: rgba(58, 58, 60, 0.5);">
                    <h4 class="text-xs font-semibold mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        <i class="fas fa-link mr-1"></i>PRASYARAT YANG ADA:
                    </h4>
                    <div id="dependencies-list" class="space-y-1.5">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Add New Dependency -->
                <div class="rounded-lg p-3" style="background: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);">
                    <h4 class="text-xs font-semibold mb-2" style="color: rgba(10, 132, 255, 1);">
                        <i class="fas fa-plus mr-1"></i>TAMBAH PRASYARAT BARU:
                    </h4>
                    
                    <div class="grid grid-cols-3 gap-2">
                        <!-- Select Permit -->
                        <div class="col-span-2">
                            <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.8);">
                                Izin Prasyarat <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <select name="depends_on_permit_id" id="dep-select" required 
                                    class="w-full rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2" 
                                    style="background: rgba(58, 58, 60, 0.8); border: 1px solid rgba(84, 84, 88, 0.65); color: #FFFFFF; focus:ring-color: rgba(10, 132, 255, 0.5);">
                                <option value="">-- Pilih Izin --</option>
                                <!-- Will be populated by JavaScript -->
                            </select>
                        </div>
                        
                        <!-- Dependency Type -->
                        <div>
                            <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.8);">
                                Jenis <span style="color: rgba(255, 59, 48, 1);">*</span>
                            </label>
                            <select name="can_proceed_without" required 
                                    class="w-full rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2" 
                                    style="background: rgba(58, 58, 60, 0.8); border: 1px solid rgba(84, 84, 88, 0.65); color: #FFFFFF;">
                                <option value="0">Wajib</option>
                                <option value="1">Opsional</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-2 p-2 rounded text-xs" style="background: rgba(10, 132, 255, 0.05); color: rgba(235, 235, 245, 0.7);">
                        <i class="fas fa-info-circle mr-1" style="color: rgba(10, 132, 255, 1);"></i>
                        <strong>Wajib:</strong> Prasyarat harus selesai. <strong>Opsional:</strong> Direkomendasikan tapi bisa dilewati.
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2 mt-4">
                <button type="button" onclick="event.stopPropagation(); closeManageDependenciesModal()" 
                        class="flex-1 px-3 py-1.5 rounded-lg text-sm font-medium transition-all" 
                        style="background: rgba(142, 142, 147, 0.3); color: rgba(235, 235, 245, 0.9);" 
                        onmouseover="this.style.background='rgba(142, 142, 147, 0.4)'"
                        onmouseout="this.style.background='rgba(142, 142, 147, 0.3)'">
                    <i class="fas fa-times mr-1"></i>Tutup
                </button>
                <button type="submit" 
                        class="flex-1 px-3 py-1.5 rounded-lg text-sm font-medium transition-all" 
                        style="background: rgba(10, 132, 255, 1); color: #FFFFFF;" 
                        onmouseover="this.style.background='rgba(10, 132, 255, 0.8)'"
                        onmouseout="this.style.background='rgba(10, 132, 255, 1)'">
                    <i class="fas fa-plus mr-1"></i>Tambah Prasyarat
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<style>
/* OVERRIDE: Disable card-elevated hover effect for permits tab container only */
.permits-tab-no-hover.card-elevated:hover {
    background-color: #1C1C1E !important;  /* Keep original - no change on hover */
    border-color: rgba(84, 84, 88, 0.65) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48) !important;
    transform: none !important;  /* No lift effect */
}

/* Sortable Styles */
.sortable-ghost {
    opacity: 0.4;
    background: rgba(10, 132, 255, 0.2);
}

.sortable-chosen {
    background: rgba(10, 132, 255, 0.1);
}

.sortable-drag {
    opacity: 1;
    cursor: grabbing !important;
}

/* Fix hover isolation - prevent hover from affecting entire section */
#permits-sortable > [data-permit-id] {
    isolation: isolate;
}

#permits-sortable button {
    position: relative;
    z-index: 1;
}

/* Hover effect for individual permit cards */
.permit-card:hover {
    background: rgba(58, 58, 60, 0.7) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    transform: translateY(-2px);
}

/* Animations */
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slide-out {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}

.animate-slide-out {
    animation: slide-out 0.3s ease-in;
}

/* Drag Handle Hover */
.drag-handle:hover {
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
}
</style>

<script>
console.log('🚀 Permits Tab JavaScript Loading...');

// Store permit data for modals
let currentPermitData = null;
let allPermits = @json($project->permits->sortBy('sequence_order')->values());

console.log('📊 All Permits Loaded:', allPermits.length);

// ===== MODAL FUNCTIONS (Exposed to window for global access) =====

function updatePermitStatusImpl(permitId) {
    // Get permit data from DOM
    const permitCard = document.querySelector(`[data-permit-id="${permitId}"]`);
    if (!permitCard) {
        alert('Permit data not found');
        return;
    }
    
    // Extract permit data from data attributes
    currentPermitData = {
        id: permitId,
        sequence: permitCard.dataset.sequence || '',
        name: permitCard.dataset.permitName || '',
        institution: permitCard.dataset.institution || '',
        currentStatus: permitCard.dataset.status || '',
        canStart: permitCard.dataset.canStart === 'true',
        blockers: permitCard.dataset.blockers ? JSON.parse(permitCard.dataset.blockers) : [],
        startDate: permitCard.dataset.startDate || '',
        endDate: permitCard.dataset.endDate || '',
        notes: permitCard.dataset.notes || ''
    };
    
    // Populate modal
    document.getElementById('permit-sequence').textContent = currentPermitData.sequence;
    document.getElementById('permit-name').textContent = currentPermitData.name;
    document.getElementById('permit-institution').textContent = currentPermitData.institution;
    document.getElementById('current-status').textContent = getStatusLabel(currentPermitData.currentStatus);
    
    // Set form action
    document.getElementById('update-status-form').action = `/permits/${permitId}/status`;
    
    // Pre-fill dates and notes
    document.querySelector('[name="start_date"]').value = currentPermitData.startDate;
    document.querySelector('[name="end_date"]').value = currentPermitData.endDate;
    document.getElementById('permit-notes').value = currentPermitData.notes;
    
    // Show/hide dependency warning
    if (!currentPermitData.canStart && currentPermitData.blockers.length > 0) {
        const blockerList = document.getElementById('blocker-list');
        blockerList.innerHTML = '';
        currentPermitData.blockers.forEach(blocker => {
            const li = document.createElement('li');
            li.textContent = blocker;
            blockerList.appendChild(li);
        });
        document.getElementById('dependency-warning').classList.remove('hidden');
    } else {
        document.getElementById('dependency-warning').classList.add('hidden');
    }
    
    // Show modal
    const modal = document.getElementById('update-status-modal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeUpdateStatusModal() {
    const modal = document.getElementById('update-status-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('update-status-form').reset();
    document.getElementById('override-section').classList.add('hidden');
    currentPermitData = null;
}

function checkOverrideNeeded() {
    const newStatus = document.getElementById('new-status').value.toUpperCase();
    const overrideSection = document.getElementById('override-section');
    const overrideReason = document.getElementById('override-reason');
    const canProceedInput = document.getElementById('can-proceed-without');
    
    // Status yang butuh dependency check
    const statusesNeedingCheck = ['IN_PROGRESS', 'SUBMITTED', 'UNDER_REVIEW', 'APPROVED'];
    
    if (!currentPermitData) return;
    
    // Check if override needed
    const needsOverride = !currentPermitData.canStart && 
                         statusesNeedingCheck.includes(newStatus) &&
                         currentPermitData.blockers.length > 0;
    
    if (needsOverride) {
        overrideSection.classList.remove('hidden');
        overrideReason.required = true;
        canProceedInput.value = '1';
    } else {
        overrideSection.classList.add('hidden');
        overrideReason.required = false;
        overrideReason.value = '';
        canProceedInput.value = '0';
    }
}

function getStatusLabel(status) {
    const statusKey = String(status || '').toUpperCase();
    const labels = {
        'NOT_STARTED': 'Belum Dimulai',
        'IN_PROGRESS': 'Dalam Proses',
        'SUBMITTED': 'Sudah Diajukan',
        'UNDER_REVIEW': 'Dalam Review',
        'REVISION_REQUIRED': 'Perlu Revisi',
        'APPROVED': 'Disetujui',
        'REJECTED': 'Ditolak',
        'ON_HOLD': 'Ditunda',
        'CANCELLED': 'Dibatalkan',
        'EXISTING': 'Sudah Ada',
        'WAITING_DOC': 'Menunggu Dokumen'
    };
    return labels[statusKey] || status;
}

function deletePermit(permitId) {
    if (!confirm('Yakin ingin menghapus izin ini?\n\nPerhatian: Jika izin ini menjadi prasyarat izin lain, penghapusan akan gagal.')) {
        return;
    }
    
    // Create form and submit with redirect back to permits tab
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/permits/${permitId}`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Add hidden input to redirect back to permits tab after deletion
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="redirect_to_tab" value="permits">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

// ===== DEPENDENCY MANAGEMENT =====
function showManageDependenciesModalImpl(permitId) {
    const permit = allPermits.find(p => p.id === permitId);
    if (!permit) {
        alert('Permit data not found');
        return;
    }
    
    currentPermitData = permit;
    
    // FIX #2: Fallback to DOM if permit_type relation not loaded
    const permitCard = document.querySelector(`[data-permit-id="${permitId}"]`);
    const permitName = permit.permit_type?.name || permitCard?.dataset.permitName || 'Unknown';
    
    document.getElementById('dep-permit-sequence').textContent = permit.sequence_order || '?';
    document.getElementById('dep-permit-name').textContent = permitName;
    
    // Set form action
    document.getElementById('add-dependency-form').action = `/permits/${permitId}/dependencies`;
    
    // Get existing dependency IDs to exclude from dropdown
    const existingDepIds = (permit.dependencies || []).map(d => d.depends_on_permit_id);
    
    // Populate available permits with better filtering
    const depSelect = document.getElementById('dep-select');
    depSelect.innerHTML = '<option value="">-- Pilih Izin --</option>';
    
    let availableCount = 0;
    
    allPermits.forEach(p => {
        // FILTER LOGIC:
        // 1. Tidak boleh memilih diri sendiri (self-dependency)
        if (p.id === permit.id) return;
        
        // 2. Tidak boleh memilih goal permit (goal adalah tujuan akhir, bukan prasyarat)
        if (p.is_goal_permit) return;
        
        // 3. Tidak boleh memilih permit yang sudah jadi dependency
        if (existingDepIds.includes(p.id)) return;
        
        // 4. Hanya tampilkan permits yang sequence_order-nya lebih kecil (prasyarat harus sebelumnya)
        // ATAU jika sequence_order null/sama, tampilkan semua non-goal permits
        if (permit.sequence_order && p.sequence_order && p.sequence_order >= permit.sequence_order) return;
        
        availableCount++;
        
        const option = document.createElement('option');
        option.value = p.id;
        
        // FIX #2: Fallback to DOM if relation not loaded
        const pName = p.permit_type?.name || document.querySelector(`[data-permit-id="${p.id}"]`)?.dataset.permitName || 'Unknown';
        
        // Get status for visual indicator
        const status = p.status || 'NOT_STARTED';
        const statusIcon = {
            'APPROVED': '✓',
            'EXISTING': '✓',
            'IN_PROGRESS': '⏳',
            'SUBMITTED': '📤',
            'UNDER_REVIEW': '👁',
            'NOT_STARTED': '○',
            'REJECTED': '✗',
            'CANCELLED': '✗'
        }[status] || '○';
        
        const statusLabel = {
            'APPROVED': 'Disetujui',
            'EXISTING': 'Sudah Ada',
            'IN_PROGRESS': 'Proses',
            'SUBMITTED': 'Diajukan',
            'UNDER_REVIEW': 'Review',
            'NOT_STARTED': 'Belum',
            'REJECTED': 'Ditolak',
            'CANCELLED': 'Batal'
        }[status] || status;
        
        option.textContent = `${statusIcon} ${p.sequence_order || '?'}. ${pName} (${statusLabel})`;
        
        // Add warning untuk permits yang belum APPROVED/EXISTING
        if (!['APPROVED', 'EXISTING'].includes(status)) {
            option.style.color = 'rgba(255, 149, 0, 1)';
        }
        
        depSelect.appendChild(option);
    });
    
    // Show warning jika tidak ada permits yang bisa dipilih
    if (availableCount === 0) {
        const option = document.createElement('option');
        option.disabled = true;
        option.textContent = '⚠️ Tidak ada izin yang dapat dijadikan prasyarat';
        option.style.color = 'rgba(255, 149, 0, 1)';
        depSelect.appendChild(option);
    }
    
    // Show current dependencies
    const depsList = document.getElementById('dependencies-list');
    depsList.innerHTML = '';
    
    const depsCount = (permit.dependencies || []).length;
    const countBadge = document.getElementById('dep-count-badge');
    
    if (depsCount > 0) {
        countBadge.textContent = `${depsCount} Prasyarat`;
        countBadge.style.display = 'inline-flex';
        
        permit.dependencies.forEach(dep => {
            // Guard against missing relations
            if (!dep.depends_on_permit) return;
            
            const depItem = document.createElement('div');
            depItem.className = 'flex items-center justify-between p-2 rounded-lg transition-all';
            depItem.style.background = 'rgba(58, 58, 60, 0.7)';
            
            const depName = dep.depends_on_permit.permit_type?.name || 'Unknown';
            const depSequence = dep.depends_on_permit.sequence_order || '?';
            const depStatus = dep.depends_on_permit.status || 'NOT_STARTED';
            const isCompleted = ['APPROVED', 'EXISTING'].includes(depStatus);
            
            depItem.innerHTML = `
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold flex-shrink-0" 
                          style="background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);">
                        ${depSequence}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium truncate" style="color: #FFFFFF;">${depName}</div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs px-1.5 py-0.5 rounded-full" 
                                  style="background: ${dep.can_proceed_without ? 'rgba(255, 149, 0, 0.2)' : 'rgba(255, 59, 48, 0.2)'}; 
                                         color: ${dep.can_proceed_without ? 'rgba(255, 149, 0, 1)' : 'rgba(255, 59, 48, 1)'};">  
                                ${dep.can_proceed_without ? '⚠️ Opsional' : '🔒 Wajib'}
                            </span>
                            <span class="text-xs px-1.5 py-0.5 rounded-full" 
                                  style="background: ${isCompleted ? 'rgba(52, 199, 89, 0.2)' : 'rgba(142, 142, 147, 0.2)'}; 
                                         color: ${isCompleted ? 'rgba(52, 199, 89, 1)' : 'rgba(142, 142, 147, 1)'};">
                                ${isCompleted ? '✓ Selesai' : '○ Belum'}
                            </span>
                        </div>
                    </div>
                </div>
                <button onclick="removeDependencyFromModal(${dep.id}, event)" 
                        class="p-1.5 rounded transition-colors flex-shrink-0"
                        style="color: rgba(255, 59, 48, 1);"
                        onmouseover="this.style.background='rgba(255, 59, 48, 0.2)'"
                        onmouseout="this.style.background='transparent'"
                        title="Hapus">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            `;
            depsList.appendChild(depItem);
        });
    } else {
        countBadge.style.display = 'none';
        depsList.innerHTML = '<div class="text-center text-xs py-3" style="color: rgba(235, 235, 245, 0.5);">Belum ada prasyarat</div>';
    }
    
    // Show modal
    const modal = document.getElementById('manage-dependencies-modal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeManageDependenciesModal() {
    const modal = document.getElementById('manage-dependencies-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('add-dependency-form').reset();
    currentPermitData = null;
}

function removeDependency(permitId, dependencyId, event) {
    event.stopPropagation();
    
    if (!confirm('Yakin ingin menghapus dependensi ini?')) {
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/permits/${permitId}/dependencies/${dependencyId}`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

function removeDependencyFromModal(dependencyId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    if (!confirm('Yakin ingin menghapus prasyarat ini?')) {
        return;
    }
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/permits/${currentPermitData.id}/dependencies/${dependencyId}`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="DELETE">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

// ===== DRAG AND DROP REORDERING =====
function initializePermitsSortable() {
    console.log('🎯 Initializing Drag and Drop...');
    
    // Check if SortableJS is loaded
    if (typeof Sortable === 'undefined') {
        console.error('❌ SortableJS not loaded!');
        return;
    }
    
    const sortableContainer = document.getElementById('permits-sortable');
    console.log('📦 Sortable Container:', sortableContainer);
    
    if (sortableContainer) {
        // Get all permit items
        const permitItems = sortableContainer.querySelectorAll('[data-permit-id]');
        console.log('📋 Permit Items Found:', permitItems.length);
        
        if (permitItems.length > 0) {
            try {
                new Sortable(sortableContainer, {
                    animation: 200,
                    handle: '.drag-handle',
                    draggable: '[data-permit-id]', // FIX #1: Only permit cards draggable (not h4, arrows)
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    
                    onEnd: function(evt) {
                        console.log('🔄 Drag ended, saving new order...');
                        // Get new order
                        const newOrder = [];
                        const items = sortableContainer.querySelectorAll('[data-permit-id]');
                        
                        items.forEach((item, index) => {
                            const permitId = item.dataset.permitId;
                            newOrder.push({
                                id: parseInt(permitId),  // FIX: Backend expects 'id' not 'permit_id'
                                sequence_order: index + 1
                            });
                        });
                        
                        console.log('📊 New Order:', newOrder);
                        
                        // Update sequence badges visually
                        items.forEach((item, index) => {
                            const badge = item.querySelector('.flex-shrink-0.w-10.h-10');
                            if (badge) {
                                badge.textContent = index + 1;
                            }
                        });
                        
                        // Send to server
                        saveNewOrder(newOrder);
                    }
                });
                console.log('✅ Sortable initialized successfully!');
            } catch (error) {
                console.error('❌ Error initializing Sortable:', error);
            }
        } else {
            console.log('ℹ️ No permit items to sort');
        }
    } else {
        console.log('ℹ️ Sortable container not found (may not be visible yet)');
    }
}

function saveNewOrder(newOrder) {
    console.log('💾 Saving new order to server...');
    
    // FIX #5: Validate CSRF token existence with proper error handling
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) {
        console.error('❌ CSRF meta tag not found!');
        showNotification('Error: CSRF token tidak ditemukan. Halaman perlu dimuat ulang.', 'error');
        return;
    }
    
    const csrfToken = csrfMeta.content;
    const projectId = {{ $project->id }};
    
    if (!csrfToken) {
        console.error('❌ CSRF token is empty!');
        showNotification('CSRF token kosong', 'error');
        return;
    }
    
    console.log('🔑 CSRF Token:', csrfToken);
    console.log('📦 Project ID:', projectId);
    console.log('📊 Order Data:', newOrder);
    
    fetch(`/projects/${projectId}/permits/reorder`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ permits: newOrder })
    })
    .then(response => {
        console.log('📡 Response received:', response.status);
        
        // FIX #4: Check content-type before parsing to avoid 204/empty response errors
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else if (response.ok) {
            // Success but no JSON content (e.g., 204 No Content)
            return { success: true };
        } else {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
    })
    .then(data => {
        console.log('✅ Response data:', data);
        if (data.success) {
            showNotification('Urutan izin berhasil diperbarui', 'success');
        } else {
            showNotification('Gagal memperbarui urutan: ' + (data.message || 'Unknown error'), 'error');
            setTimeout(() => location.reload(), 2000);
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        showNotification('Gagal memperbarui urutan: ' + error.message, 'error');
        setTimeout(() => location.reload(), 2000);
    });
}


// ===== DOCUMENT UPLOAD FUNCTIONS =====
let currentUploadPermitId = null;

function showUploadModal(permitId) {
    console.log('Opening Upload Modal for permit:', permitId);
    const permitCard = document.querySelector(`[data-permit-id="${permitId}"]`);
    if (!permitCard) {
        alert('Permit data not found');
        return;
    }
    
    currentUploadPermitId = permitId;
    
    // Set permit info in modal
    document.getElementById('upload-permit-sequence').textContent = permitCard.dataset.sequence;
    document.getElementById('upload-permit-name').textContent = permitCard.dataset.permitName;
    
    // Show modal
    const modal = document.getElementById('upload-document-modal');
    if (!modal) {
        console.error('Upload modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    
    // Setup form submission
    const form = document.getElementById('upload-document-form');
    form.onsubmit = function(e) {
        e.preventDefault();
        uploadDocument();
    };
}

function closeUploadModal() {
    console.log('Closing Upload Modal');
    const modal = document.getElementById('upload-document-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
    
    // Reset form
    document.getElementById('upload-document-form').reset();
    currentUploadPermitId = null;
}

function uploadDocument() {
    if (!currentUploadPermitId) {
        showNotification('Permit ID tidak ditemukan', 'error');
        return;
    }
    
    const fileInput = document.getElementById('document-file');
    const descriptionInput = document.getElementById('document-description');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        showNotification('Pilih file terlebih dahulu', 'error');
        return;
    }
    
    const file = fileInput.files[0];
    
    // Validate file size (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Ukuran file maksimal 5MB', 'error');
        return;
    }
    
    // Validate file type
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Format file tidak didukung. Gunakan PDF, DOC, DOCX, JPG, atau PNG', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('document', file);
    formData.append('description', descriptionInput.value);
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Show loading
    showNotification('Uploading...', 'info');
    
    fetch(`/projects/{{ $project->id }}/permits/${currentUploadPermitId}/documents`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Dokumen berhasil diupload!', 'success');
            closeUploadModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Gagal upload dokumen: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal upload dokumen: ' + error.message, 'error');
    });
}

function deleteDocument(documentId) {
    if (!confirm('Yakin hapus dokumen ini?')) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    fetch(`/projects/{{ $project->id }}/permits/documents/${documentId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Dokumen berhasil dihapus!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Gagal hapus dokumen: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Gagal hapus dokumen: ' + error.message, 'error');
    });
}

// Expose implementation functions to global scope
window.updatePermitStatusImpl = updatePermitStatusImpl;
window.showManageDependenciesModalImpl = showManageDependenciesModalImpl;
window.initializePermitsSortable = initializePermitsSortable;

// Try to initialize sortable if permits tab is already visible
if (document.getElementById('permits-sortable')) {
    initializePermitsSortable();
}

// Add form validation for dependencies
document.addEventListener('DOMContentLoaded', function() {
    const depForm = document.getElementById('add-dependency-form');
    if (depForm) {
        depForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedPermitId = parseInt(document.getElementById('dep-select').value);
            const canProceedWithout = document.querySelector('[name="can_proceed_without"]').value;
            
            if (!selectedPermitId) {
                showNotification('Pilih izin prasyarat terlebih dahulu', 'error');
                return false;
            }
            
            if (!currentPermitData) {
                showNotification('Data permit tidak ditemukan', 'error');
                return false;
            }
            
            // Check for circular dependency
            const selectedPermit = allPermits.find(p => p.id === selectedPermitId);
            if (selectedPermit && selectedPermit.dependencies) {
                const willCreateCircular = checkCircularDependency(currentPermitData.id, selectedPermitId);
                if (willCreateCircular) {
                    showNotification('⚠️ Tidak dapat menambahkan: akan membuat circular dependency!', 'error');
                    return false;
                }
            }
            
            // Show warning if selected permit is not completed yet
            if (selectedPermit && !['APPROVED', 'EXISTING'].includes(selectedPermit.status)) {
                const confirmMsg = canProceedWithout === '0' 
                    ? `Prasyarat yang dipilih belum selesai (${getStatusLabel(selectedPermit.status)}). Izin ini tidak akan bisa dimulai hingga prasyarat selesai. Lanjutkan?`
                    : `Prasyarat yang dipilih belum selesai (${getStatusLabel(selectedPermit.status)}). Tetap menambahkan sebagai opsional?`;
                    
                if (!confirm(confirmMsg)) {
                    return false;
                }
            }
            
            // If all validation passed, submit the form
            this.submit();
        });
    }
});

// Helper function to check circular dependency
function checkCircularDependency(currentPermitId, targetPermitId) {
    // Check if targetPermit depends on currentPermit (directly or indirectly)
    const visited = new Set();
    
    function hasDependencyOn(permitId, searchFor) {
        if (visited.has(permitId)) return false; // Prevent infinite loop
        visited.add(permitId);
        
        const permit = allPermits.find(p => p.id === permitId);
        if (!permit || !permit.dependencies) return false;
        
        for (const dep of permit.dependencies) {
            if (dep.depends_on_permit_id === searchFor) {
                return true; // Direct dependency found
            }
            // Check recursively
            if (hasDependencyOn(dep.depends_on_permit_id, searchFor)) {
                return true;
            }
        }
        
        return false;
    }
    
    // If targetPermit (or its dependencies) already depends on currentPermit,
    // adding currentPermit -> targetPermit will create a circle
    return hasDependencyOn(targetPermitId, currentPermitId);
}

function getStatusLabel(status) {
    const statusKey = String(status || '').toUpperCase();
    const labels = {
        'NOT_STARTED': 'Belum Dimulai',
        'IN_PROGRESS': 'Dalam Proses',
        'WAITING_DOC': 'Menunggu Dokumen',
        'SUBMITTED': 'Sudah Diajukan',
        'UNDER_REVIEW': 'Dalam Review',
        'APPROVED': 'Disetujui',
        'EXISTING': 'Sudah Ada',
        'REJECTED': 'Ditolak',
        'CANCELLED': 'Dibatalkan'
    };
    return labels[statusKey] || status;
}

</script>
@endpush

