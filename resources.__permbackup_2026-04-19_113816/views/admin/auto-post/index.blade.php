@extends('layouts.app')

@section('title', 'Auto-Post AI')

@section('content')
<div class="space-y-4">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Content Automation</p>
                <h1 class="admin-hero-title text-white">Auto-Post AI</h1>
                <p class="admin-hero-desc max-w-xl">Otomatisasi pembuatan dan publikasi artikel AI</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-robot mr-1.5"></i>{{ $config->is_enabled ? 'Aktif' : 'Nonaktif' }}</span>
                    <span><i class="fas fa-lightbulb mr-1.5"></i>{{ $stats['available_topics'] }} topics</span>
                    <span><i class="fas fa-clock mr-1.5"></i>{{ $scheduleStats['pending'] }} pending</span>
                </div>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-apple" style="background: rgba(255,255,255,0.08);">
                <button 
                    id="toggleAutoPost"
                    data-enabled="{{ $config->is_enabled ? 'true' : 'false' }}"
                    onclick="toggleAutoPost()"
                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $config->is_enabled ? 'bg-apple-blue' : 'bg-gray-600' }}">
                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $config->is_enabled ? 'translate-x-4' : 'translate-x-1' }}"></span>
                </button>
                <span class="admin-label text-white" id="toggleLabel">{{ $config->is_enabled ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </div>
    </section>

    {{-- Alert Messages - Compact --}}
    @if(session('success'))
        <div class="admin-alert flex items-center justify-between" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2" style="color: var(--apple-green);"></i>
                <span class="admin-body" style="color: var(--apple-green);">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="admin-small opacity-60 hover:opacity-100" style="color: var(--apple-green);">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="admin-alert flex items-center justify-between" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2" style="color: var(--apple-red);"></i>
                <span class="admin-body" style="color: var(--apple-red);">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="admin-small opacity-60 hover:opacity-100" style="color: var(--apple-red);">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Tab Navigation - Compact --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex gap-1 p-1.5 overflow-x-auto" role="tablist">
                <button onclick="switchTab('config')" id="tab-config" 
                        class="tab-button {{ $activeTab == 'config' ? 'active' : '' }} whitespace-nowrap">
                    <i class="fas fa-cog mr-1.5"></i>Konfigurasi
                </button>
                <button onclick="switchTab('analytics')" id="tab-analytics"
                        class="tab-button {{ $activeTab == 'analytics' ? 'active' : '' }} whitespace-nowrap">
                    <i class="fas fa-chart-line mr-1.5"></i>Analytics
                </button>
                <button onclick="switchTab('topics')" id="tab-topics"
                        class="tab-button {{ $activeTab == 'topics' ? 'active' : '' }} whitespace-nowrap">
                    <i class="fas fa-lightbulb mr-1.5"></i>Topics
                    <span class="ml-1.5 admin-badge {{ $activeTab == 'topics' ? 'bg-white text-apple-blue' : 'bg-apple-purple text-white' }}">
                        {{ $topicStats['available'] }}
                    </span>
                </button>
                <button onclick="switchTab('schedules')" id="tab-schedules"
                        class="tab-button {{ $activeTab == 'schedules' ? 'active' : '' }} whitespace-nowrap">
                    <i class="fas fa-calendar-alt mr-1.5"></i>Jadwal
                    @if($scheduleStats['pending'] > 0)
                        <span class="ml-1.5 admin-badge {{ $activeTab == 'schedules' ? 'bg-white text-apple-blue' : 'bg-yellow-500 text-white' }}">
                            {{ $scheduleStats['pending'] }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        <div class="p-4">
            {{-- Config Tab --}}
            <div id="content-config" class="tab-content {{ $activeTab != 'config' ? 'hidden' : '' }}">
                @include('admin.auto-post.tabs.config')
            </div>
            
            {{-- Analytics Tab --}}
            <div id="content-analytics" class="tab-content {{ $activeTab != 'analytics' ? 'hidden' : '' }}">
                @include('admin.auto-post.tabs.analytics')
            </div>
            
            {{-- Topics Tab --}}
            <div id="content-topics" class="tab-content {{ $activeTab != 'topics' ? 'hidden' : '' }}">
                @include('admin.auto-post.tabs.topics')
            </div>
            
            {{-- Schedules Tab --}}
            <div id="content-schedules" class="tab-content {{ $activeTab != 'schedules' ? 'hidden' : '' }}">
                @include('admin.auto-post.tabs.schedules')
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
function switchTab(tabName) {
    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
    
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Deactivate all tabs
    document.querySelectorAll('.tab-button').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName)?.classList.remove('hidden');
    
    // Activate selected tab
    document.getElementById('tab-' + tabName)?.classList.add('active');
}

function toggleAutoPost() {
    fetch('{{ route("auto-post.config.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById('toggleAutoPost');
            const label = document.getElementById('toggleLabel');
            const span = btn.querySelector('span');
            
            if (data.is_enabled) {
                btn.classList.remove('bg-gray-600');
                btn.classList.add('bg-apple-blue');
                span.classList.remove('translate-x-1');
                span.classList.add('translate-x-4');
                label.textContent = 'Aktif';
            } else {
                btn.classList.remove('bg-apple-blue');
                btn.classList.add('bg-gray-600');
                span.classList.remove('translate-x-4');
                span.classList.add('translate-x-1');
                label.textContent = 'Nonaktif';
            }
        }
    })
    .catch(() => {
        alert('Gagal mengubah status auto-post. Silakan coba lagi.');
    });
}

// Handle browser back/forward
window.addEventListener('popstate', function(event) {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'config';
    switchTab(tab);
});
</script>
@endpush

<style>
.tab-button {
    @apply px-4 py-2.5 rounded-apple font-medium;
    color: rgba(235, 235, 245, 0.6);
    background: transparent;
}

.tab-button:hover:not(.active) {
    background: rgba(255, 255, 255, 0.05);
    color: rgba(235, 235, 245, 0.8);
}

.tab-button.active {
    background: var(--apple-blue);
    color: #FFFFFF;
}
</style>
@endsection
