@extends('client.layouts.app')

@section('title', 'Dashboard Client')

@push('styles')
<style>
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
        'Selesai' => 'bg-green-100 text-green-800',
        'Dalam Proses' => 'bg-blue-100 text-blue-800',
        'Sedang Diproses' => 'bg-blue-100 text-blue-800',
        'Draft' => 'bg-gray-100 text-gray-700',
        'Dokumen Kurang' => 'bg-yellow-100 text-yellow-800',
    ];
@endphp

<div class="space-y-8">
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Welcome / CTA -->
    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-2xl shadow-lg text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.toptal.com/designers/subtlepatterns/uploads/dot-grid.png')]"></div>
        <div class="relative flex flex-col lg:flex-row gap-6 items-center p-6 lg:p-8">
            <div class="flex-1 space-y-4">
                <p class="text-sm uppercase tracking-[0.35em] text-white/70">Ringkasan Hari Ini</p>
                <h1 class="text-2xl lg:text-3xl font-bold leading-snug">
                    Hai {{ $client->name }}, lihat progres terbaru izin usaha Anda dan lanjutkan langkah berikutnya.
                </h1>
                <div class="flex flex-wrap gap-3 text-sm">
                    <span class="px-3 py-1 rounded-full bg-white/15 border border-white/30">
                        {{ $activeProjects }} Proyek aktif
                    </span>
                    <span class="px-3 py-1 rounded-full bg-white/15 border border-white/30">
                        {{ $completedProjects }} Telah selesai
                    </span>
                    <span class="px-3 py-1 rounded-full bg-white/15 border border-white/30">
                        {{ $upcomingDeadlines->count() }} deadline dekat
                    </span>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('client.applications.create') }}" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-semibold px-5 py-3 rounded-xl shadow">
                        <i class="fas fa-plus"></i> Ajukan permohonan baru
                    </a>
                    <a href="{{ route('client.services.index') }}" class="inline-flex items-center gap-2 bg-white/15 backdrop-blur px-5 py-3 rounded-xl border border-white/30 font-semibold">
                        <i class="fas fa-layer-group"></i> Jelajahi rekomendasi
                    </a>
                </div>
            </div>
            <div class="w-full lg:w-auto bg-white/10 rounded-2xl border border-white/20 p-5 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.3em] text-white/70 mb-3">Progress Track</p>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm text-white/80">
                            <span>Dokumen terkumpul</span>
                            <span>{{ min(100, ($pendingDocuments ? 100 - $pendingDocuments * 10 : 100)) }}%</span>
                        </div>
                        <div class="h-2 bg-white/20 rounded-full mt-1">
                            <div class="h-2 rounded-full bg-lime-300" style="width: {{ min(100, ($pendingDocuments ? 100 - $pendingDocuments * 10 : 100)) }}%;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm text-white/80">
                            <span>Permohonan aktif</span>
                            <span>{{ $submittedCount }} proses</span>
                        </div>
                        <div class="h-2 bg-white/20 rounded-full mt-1">
                            <div class="h-2 rounded-full bg-yellow-300" style="width: {{ min(100, $submittedCount ? ($submittedCount / max(1, $activeProjects + $submittedCount)) * 100 : 0) }}%;"></div>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-white/70 mt-4">Terakhir diperbarui {{ now()->diffForHumans(null, true) }}</p>
            </div>
        </div>
    </div>

    <!-- Metrics row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Proyek Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $activeProjects }}</p>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fas fa-folder-tree"></i>
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-3">Sedang berlangsung & dalam pengawalan tim Bizmark.</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Selesai</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $completedProjects }}</p>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fas fa-flag-checkered"></i>
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-3">Sudah tuntas dan siap Anda ekspansi.</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Total Investasi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">Rp {{ number_format($totalInvested, 0, ',', '.') }}</p>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fas fa-wallet"></i>
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-3">Nilai estimasi dari seluruh proyek berjalan.</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Deadline Minggu Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $upcomingDeadlines->count() }}</p>
                </div>
                <span class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fas fa-bell"></i>
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-3">Pastikan dokumen Anda siap tepat waktu.</p>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('client.documents.index') }}" class="bg-white rounded-2xl border border-gray-100 p-5 flex items-start gap-4 hover:border-indigo-200 transition">
            <span class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fas fa-upload"></i>
            </span>
            <div>
                <p class="text-sm text-gray-500">Dokumen</p>
                <p class="text-lg font-semibold text-gray-900">Lengkapi berkas terbaru</p>
                <p class="text-sm text-gray-500 mt-1">Unggah keperluan OSS / teknis sebelum batas waktu.</p>
            </div>
        </a>
        <a href="{{ route('client.applications.index') }}" class="bg-white rounded-2xl border border-gray-100 p-5 flex items-start gap-4 hover:border-indigo-200 transition">
            <span class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-file-signature"></i>
            </span>
            <div>
                <p class="text-sm text-gray-500">Permohonan</p>
                <p class="text-lg font-semibold text-gray-900">Pantau status pengajuan</p>
                <p class="text-sm text-gray-500 mt-1">Lihat daftar pengajuan dan catatan tim.</p>
            </div>
        </a>
        <a href="{{ route('client.projects.index') }}" class="bg-white rounded-2xl border border-gray-100 p-5 flex items-start gap-4 hover:border-indigo-200 transition">
            <span class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-clipboard-list"></i>
            </span>
            <div>
                <p class="text-sm text-gray-500">Proyek</p>
                <p class="text-lg font-semibold text-gray-900">Lihat detail pengerjaan</p>
                <p class="text-sm text-gray-500 mt-1">Progress harian, tim PIC, dan catatan inspeksi.</p>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Project overview -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 xl:col-span-2">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Ringkasan Proyek</h3>
                    <p class="text-sm text-gray-500 mt-1">4 proyek terbaru dengan status terkini.</p>
                </div>
                <a href="{{ route('client.projects.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
            <div>
                @forelse($projects->take(4) as $project)
                <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-base font-semibold text-gray-900 truncate">{{ $project->name }}</p>
                        <p class="text-sm text-gray-500">
                            {{ $project->permitApplication->permitType->name ?? 'Jenis izin belum ditetapkan' }}
                        </p>
                    </div>
                    <div class="text-right text-sm text-gray-500 hidden lg:block">
                        <p>{{ optional($project->updated_at)->diffForHumans() }}</p>
                        <p>Project ID #{{ $project->id }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$project->status->name ?? ''] ?? 'bg-blue-50 text-blue-700' }}">
                        {{ $project->status->name ?? 'Belum ada status' }}
                    </span>
                    <a href="{{ route('client.projects.show', $project->id) }}" class="ml-4 text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                        Detail
                    </a>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">
                    Belum ada proyek tercatat. Mulai permohonan pertama Anda.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent documents -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Dokumen Terbaru</h3>
                    <p class="text-sm text-gray-500 mt-1">Unggahan terakhir 14 hari.</p>
                </div>
                <a href="{{ route('client.documents.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Kelola</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentDocuments as $document)
                <div class="px-6 py-4 flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500">
                        <i class="fas fa-file-alt"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $document->document_name }}</p>
                        <p class="text-xs text-gray-500">{{ optional($document->created_at)->diffForHumans() }}</p>
                    </div>
                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                        Unduh
                    </a>
                </div>
                @empty
                <div class="p-6 text-center text-gray-500">Belum ada dokumen diunggah.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Deadlines timeline -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Timeline Deadline (7 Hari)</h3>
                <p class="text-sm text-gray-500 mt-1">Prioritaskan task dengan label merah.</p>
            </div>
            <a href="{{ route('client.projects.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Kelola Task</a>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($upcomingDeadlines as $task)
            <div class="px-6 py-4 flex items-start gap-4">
                <div class="w-10">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $loop->first ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $loop->iteration }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900">{{ $task->name }}</p>
                    <p class="text-sm text-gray-500">{{ $task->project->name ?? 'Tanpa nama proyek' }}</p>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-50 text-amber-700">
                            Jatuh tempo {{ optional($task->due_date)->diffForHumans() }}
                        </span>
                        @if($task->assigned_to)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                            PIC: {{ $task->assigned_to }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <p>{{ optional($task->due_date)->format('d M Y') }}</p>
                    <p>{{ optional($task->due_date)->format('H:i') }}</p>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">Tidak ada deadline dalam 7 hari ke depan.</div>
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
