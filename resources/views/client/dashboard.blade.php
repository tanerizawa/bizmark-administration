@extends('client.layouts.app')

@section('title', 'Dashboard Client')

@push('styles')
<style>
    /* Active/touch feedback */
    .active\:scale-95:active {
        transform: scale(0.95);
    }
    
    .active\:bg-gray-50:active {
        background-color: #f9fafb;
    }
    
    /* Pull-to-refresh indicator */
    .pull-to-refresh {
        position: absolute;
        top: -60px;
        left: 0;
        right: 0;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        transition: top 0.3s ease;
        z-index: 10;
    }
    
    @media (prefers-color-scheme: dark) {
        .pull-to-refresh {
            background: #1f2937;
        }
    }
    
    .pull-to-refresh.active {
        top: 0;
    }
    
    .pull-to-refresh i {
        font-size: 24px;
        color: #4F46E5;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
@php
    $statusColors = [
        'Selesai' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'Dalam Proses' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'Sedang Diproses' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'Draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'Dokumen Kurang' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    ];

    // Calculate document completion based on uploaded vs required
    $totalDocuments = $client->projects()
        ->with('documents')
        ->get()
        ->pluck('documents')
        ->flatten()
        ->count();
    
    $uploadedDocuments = $client->projects()
        ->with('documents')
        ->get()
        ->pluck('documents')
        ->flatten()
        ->filter(function($doc) {
            return !empty($doc->file_path);
        })
        ->count();
    
    $documentCompletion = $totalDocuments > 0 
        ? round(($uploadedDocuments / $totalDocuments) * 100) 
        : 0;
    
    $totalTrackedApplications = max(1, $activeProjects + $submittedCount);
    $submissionProgress = min(100, round(($submittedCount / $totalTrackedApplications) * 100));
@endphp

<div class="space-y-0">
    <!-- Mobile Compact Header (PWA only) - LinkedIn Style -->
    <div class="lg:hidden bg-[#0a66c2] text-white p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-xs text-white/70 leading-tight">Selamat datang kembali</p>
                <h1 class="text-lg sm:text-xl font-bold leading-tight">{{ $client->name }}</h1>
            </div>
            @if($upcomingDeadlines->count() > 0)
            <a href="#deadlines" class="flex items-center gap-1.5 bg-white/20 backdrop-blur px-4 py-2.5 min-h-[44px] rounded-full active:scale-95 transition-transform">
                <i class="fas fa-bell text-sm"></i>
                <span class="text-xs font-semibold">{{ $upcomingDeadlines->count() }} urgent</span>
            </a>
            @endif
        </div>
        
        <!-- Compact Stats Grid (LinkedIn Style) -->
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-white/10 backdrop-blur px-4 py-3 hover:bg-white/15 transition-colors">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs text-white/70 leading-tight">Proyek Aktif</p>
                    <i class="fas fa-folder-open text-white/50 text-xs"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $activeProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-4 py-3 hover:bg-white/15 transition-colors">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs text-white/70 leading-tight">Selesai</p>
                    <i class="fas fa-check-circle text-emerald-400 text-xs"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $completedProjects }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-4 py-3 hover:bg-white/15 transition-colors">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs text-white/70 leading-tight">Deadline 7 hari</p>
                    <i class="fas fa-clock text-amber-400 text-xs"></i>
                </div>
                <p class="text-2xl font-bold leading-tight">{{ $upcomingDeadlines->count() }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur px-4 py-3 hover:bg-white/15 transition-colors">
                <div class="flex items-center justify-between mb-1">
                    <p class="text-xs text-white/70 leading-tight">Total Investasi</p>
                    <i class="fas fa-wallet text-purple-400 text-xs"></i>
                </div>
                <p class="text-lg font-bold leading-tight">Rp {{ number_format($totalInvested / 1000000, 1) }}M</p>
            </div>
        </div>
        
        <!-- Progress Summary (LinkedIn Style) -->
        <div class="mt-4 pt-4 border-t border-white/20">
            <div class="flex items-center justify-between text-sm leading-normal">
                <span class="text-white/80 flex items-center gap-2">
                    <i class="fas fa-file-alt text-xs"></i> Dokumen terupload
                </span>
                <span class="font-semibold">{{ $uploadedDocuments }}/{{ $totalDocuments }}</span>
            </div>
            <div class="flex items-center justify-between text-sm leading-normal mt-2">
                <span class="text-white/80 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i> Permohonan aktif
                </span>
                <span class="font-semibold">{{ $submittedCount }}</span>
            </div>
        </div>
        
        <!-- Quick Actions Mobile -->
        <div class="mt-4 pt-4 border-t border-white/20 grid grid-cols-3 gap-2">
            <a href="{{ route('client.applications.create') }}" class="flex flex-col items-center gap-1.5 px-3 py-2.5 bg-white/10 backdrop-blur rounded-lg active:scale-95 transition-transform">
                <i class="fas fa-plus text-lg"></i>
                <span class="text-xs font-medium">Ajukan</span>
            </a>
            <a href="{{ route('client.documents.index') }}" class="flex flex-col items-center gap-1.5 px-3 py-2.5 bg-white/10 backdrop-blur rounded-lg active:scale-95 transition-transform">
                <i class="fas fa-folder text-lg"></i>
                <span class="text-xs font-medium">Dokumen</span>
            </a>
            <a href="{{ route('client.projects.index') }}" class="flex flex-col items-center gap-1.5 px-3 py-2.5 bg-white/10 backdrop-blur rounded-lg active:scale-95 transition-transform">
                <i class="fas fa-briefcase text-lg"></i>
                <span class="text-xs font-medium">Proyek</span>
            </a>
        </div>
    </div>

    <!-- Desktop Hero - LinkedIn Style (hidden on mobile) -->
    <div class="hidden lg:block bg-[#0a66c2] border-b border-gray-200 dark:border-gray-700 text-white">
        <div class="px-6 lg:px-8 py-8">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold leading-tight mb-2">
                            Hai, {{ $client->name }}
                        </h1>
                        <p class="text-base text-white/90 leading-normal">
                            Pantau progres izin usaha dan kelola proyek Anda
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('client.applications.create') }}" class="inline-flex items-center gap-2 bg-white text-[#0a66c2] font-semibold px-5 py-3 rounded-lg hover:shadow-lg active:scale-95 transition-all">
                            <i class="fas fa-plus"></i> Ajukan Permohonan
                        </a>
                        <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/30 px-5 py-3 rounded-lg hover:bg-white/20 active:scale-95 transition-all">
                            <i class="fas fa-layer-group"></i> Jelajahi Layanan
                        </a>
                    </div>
                </div>
                
                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 hover:bg-white/15 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Proyek Aktif</p>
                            <i class="fas fa-folder-open text-white/50"></i>
                        </div>
                        <p class="text-3xl font-bold leading-tight">{{ $activeProjects }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 hover:bg-white/15 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Selesai</p>
                            <i class="fas fa-check-circle text-emerald-400"></i>
                        </div>
                        <p class="text-3xl font-bold leading-tight">{{ $completedProjects }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 hover:bg-white/15 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Deadline 7 Hari</p>
                            <i class="fas fa-clock text-amber-400"></i>
                        </div>
                        <p class="text-3xl font-bold leading-tight">{{ $upcomingDeadlines->count() }}</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur border border-white/20 px-5 py-4 hover:bg-white/15 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs uppercase tracking-wider text-white/70 leading-tight">Total Investasi</p>
                            <i class="fas fa-wallet text-purple-400"></i>
                        </div>
                        <p class="text-xl font-bold leading-tight">Rp {{ number_format($totalInvested / 1000000, 1) }}M</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LinkedIn Style: Full-width Cards with Minimal Spacing -->
    <div class="space-y-1 lg:mt-1">
        <!-- Project overview - Full Width Card -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Ringkasan Proyek</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">4 proyek terbaru dengan status terkini</p>
                </div>
                <a href="{{ route('client.projects.index') }}" class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Lihat Semua <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
            <div>
                @forelse($projects->take(4) as $project)
                <!-- Mobile & Desktop Unified Version -->
                <a href="{{ route('client.projects.show', $project->id) }}" class="flex items-center gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors">
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-semibold text-gray-900 dark:text-white leading-tight">{{ $project->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-normal">{{ $project->permitApplication->permitType->name ?? 'Jenis izin belum ditetapkan' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 leading-tight">{{ optional($project->updated_at)->diffForHumans() }}</p>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-semibold rounded-full {{ $statusColors[$project->status->name ?? ''] ?? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }} whitespace-nowrap">
                        {{ $project->status->name ?? 'Belum ada status' }}
                    </span>
                    <i class="fas fa-chevron-right text-gray-400 text-xs hidden sm:block"></i>
                </a>
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <i class="fas fa-folder-open text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-normal">Belum ada proyek. <a href="{{ route('client.applications.create') }}" class="text-[#0a66c2] font-medium hover:underline">Ajukan permohonan pertama</a></p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Documents Section - Full Width Card -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Dokumen Terbaru</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Unggahan terakhir 14 hari</p>
                </div>
                <a href="{{ route('client.documents.index') }}" class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Kelola <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
            <div>
                @forelse($recentDocuments as $document)
                <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="flex items-center gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 active:bg-gray-100 dark:active:bg-gray-700 transition-colors">
                    <span class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 flex-shrink-0">
                        <i class="fas fa-file-alt"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-semibold text-gray-900 dark:text-white leading-tight">{{ $document->document_name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-normal">{{ optional($document->created_at)->diffForHumans() }}</p>
                    </div>
                    <i class="fas fa-download text-[#0a66c2] text-sm"></i>
                </a>
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <i class="fas fa-file text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-normal">Belum ada dokumen diunggah</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Deadlines Timeline - Full Width Card -->
        <div class="bg-white dark:bg-gray-800 border-y border-gray-200 dark:border-gray-700">
            <div class="p-4 lg:p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-base lg:text-lg font-semibold text-gray-900 dark:text-white leading-tight">Timeline Deadline (7 Hari)</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 leading-normal">Prioritaskan task dengan label merah</p>
                </div>
                <a href="{{ route('client.projects.index') }}" class="text-sm font-medium text-[#0a66c2] hover:text-[#004182] px-3 py-2 min-h-[44px] flex items-center active:scale-95 transition-transform">
                    Kelola Task <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
            <div>
                @forelse($upcomingDeadlines as $task)
                <div class="flex items-start gap-3 px-4 lg:px-5 py-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <span class="flex-shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold {{ $loop->first ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ $loop->iteration }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-semibold text-gray-900 dark:text-white leading-tight">{{ $task->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-normal">{{ $task->project->name ?? 'Tanpa nama proyek' }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-medium">
                                <i class="fas fa-clock mr-1.5 text-xs"></i>
                                {{ optional($task->due_date)->format('d M Y') }}
                            </span>
                            @if($task->assigned_to)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-medium">
                                <i class="fas fa-user mr-1.5 text-xs"></i>
                                {{ $task->assigned_to }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right text-xs text-gray-400 dark:text-gray-500 hidden lg:block">
                        {{ optional($task->due_date)->diffForHumans() }}
                    </div>
                </div>
                @empty
                <div class="px-4 lg:px-5 py-12 text-center">
                    <i class="fas fa-calendar-check text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-normal">Tidak ada deadline dalam 7 hari ke depan</p>
                </div>
                @endforelse
        </div>
    </div>
</div>

<!-- Notification Prompt Component -->
<div data-has-applications="{{ $projects->count() > 0 ? 'true' : 'false' }}">
    @include('client.components.notification-prompt')
</div>

@endsection

@push('scripts')
<script>
    // Pull-to-refresh functionality for mobile
    if ('ontouchstart' in window && window.innerWidth < 1024) {
        let startY = 0;
        let currentY = 0;
        let pulling = false;
        
        const mainContent = document.querySelector('main');
        const threshold = 80;
        
        // Create pull indicator
        const indicator = document.createElement('div');
        indicator.className = 'pull-to-refresh';
        indicator.innerHTML = '<i class="fas fa-sync-alt"></i>';
        mainContent.insertBefore(indicator, mainContent.firstChild);
        
        mainContent.addEventListener('touchstart', (e) => {
            if (mainContent.scrollTop === 0) {
                startY = e.touches[0].clientY;
                pulling = true;
            }
        }, { passive: true });
        
        mainContent.addEventListener('touchmove', (e) => {
            if (!pulling) return;
            
            currentY = e.touches[0].clientY;
            const diff = currentY - startY;
            
            if (diff > 0 && mainContent.scrollTop === 0) {
                const pull = Math.min(diff, threshold * 1.5);
                indicator.style.top = `${pull - 60}px`;
                
                if (pull >= threshold) {
                    indicator.classList.add('active');
                }
            }
        }, { passive: true });
        
        mainContent.addEventListener('touchend', async () => {
            if (!pulling) return;
            
            const diff = currentY - startY;
            
            if (diff > threshold && mainContent.scrollTop === 0) {
                // Show loading state
                indicator.style.top = '0';
                indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                // Simulate refresh (reload page)
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                // Reset
                indicator.style.top = '-60px';
                indicator.classList.remove('active');
            }
            
            pulling = false;
            startY = 0;
            currentY = 0;
        });
    }
    
    // Haptic feedback for touch interactions (if supported)
    function triggerHaptic(type = 'light') {
        if ('vibrate' in navigator) {
            const patterns = {
                light: [10],
                medium: [20],
                success: [10, 50, 10]
            };
            navigator.vibrate(patterns[type]);
        }
    }
    
    // Add haptic feedback to buttons
    document.querySelectorAll('button, a.btn-primary, a.btn-secondary').forEach(el => {
        el.addEventListener('click', () => triggerHaptic('light'));
    });
</script>
@endpush
