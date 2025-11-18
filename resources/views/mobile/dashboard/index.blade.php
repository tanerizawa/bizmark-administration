{{-- 
    Mobile Dashboard View
    Path: resources/views/mobile/dashboard/index.blade.php
    
    Features:
    - Mobile-first responsive design
    - Swipeable metric cards
    - Pull-to-refresh
    - Progressive disclosure
    - Touch-optimized interactions
--}}

@extends('mobile.layouts.app')

@section('title', 'Dashboard')

@section('header-actions')
    {{-- Notification badge --}}
    <button onclick="window.location.href='{{ mobile_route('notifications.index') }}'" 
            class="relative p-2 rounded-full hover:bg-white/10 transition-colors">
        <i class="fas fa-bell text-white"></i>
        @if($metrics['urgent_count'] > 0)
            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                {{ $metrics['urgent_count'] }}
            </span>
        @endif
    </button>
@endsection

@section('content')
<div class="pb-20" x-data="dashboardMobile()">
    
    {{-- Metrics Grid 2x2 - Compact & Clean --}}
    <div class="grid grid-cols-2 gap-2 mb-4">
        
        {{-- Card 1: Urgent Alerts --}}
        <div onclick="window.location.href='{{ mobile_route('tasks.urgent') }}'"
             class="bg-white border-2 border-gray-200 rounded-lg p-3 shadow-sm active:scale-95 transition-transform cursor-pointer">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600 font-medium">Urgent</div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $metrics['urgent_count'] }}</div>
        </div>

        {{-- Card 2: Cash & Runway --}}
        <div onclick="window.location.href='{{ mobile_route('financial.index') }}'"
             class="bg-white border-2 border-gray-200 rounded-lg p-3 shadow-sm active:scale-95 transition-transform cursor-pointer">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-green-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600 font-medium">Cash Runway</div>
            </div>
            <div class="text-xl font-bold text-gray-900">{{ $metrics['runway_months'] }} bulan</div>
            <div class="text-xs text-gray-500 mt-1">Rp {{ number_format($cash_pulse['balance'] / 1000000, 1) }}M</div>
        </div>

        {{-- Card 3: Pending Approvals --}}
        <div onclick="window.location.href='{{ mobile_route('approvals.index') }}'"
             class="bg-white border-2 border-gray-200 rounded-lg p-3 shadow-sm active:scale-95 transition-transform cursor-pointer">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-signature text-blue-600 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600 font-medium">Approvals</div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $metrics['approvals_count'] ?? 0 }}</div>
        </div>

        {{-- Card 4: Today's Tasks --}}
        <div onclick="window.location.href='{{ mobile_route('tasks.index') }}'"
             class="bg-white border-2 border-gray-200 rounded-lg p-3 shadow-sm active:scale-95 transition-transform cursor-pointer">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-circle-check text-gray-700 text-sm"></i>
                </div>
                <div class="text-xs text-gray-600 font-medium">Tasks Today</div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $metrics['tasks_today'] ?? 0 }}</div>
            @if(($metrics['tasks_overdue'] ?? 0) > 0)
            <div class="text-xs text-red-600 mt-1 font-medium">{{ $metrics['tasks_overdue'] }} overdue</div>
            @endif
        </div>

    </div>

    {{-- Critical Focus Section --}}
    <div class="mb-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-sm font-bold text-gray-900">
                <i class="fas fa-bolt text-red-500 mr-1 text-xs"></i>
                Perlu Tindakan
            </h2>
            @if(count($alerts) > 3)
                <a href="{{ mobile_route('tasks.urgent') }}" class="text-xs text-[#0a66c2] font-medium">
                    +{{ count($alerts) - 3 }} lagi
                </a>
            @endif
        </div>

        @if(count($alerts) > 0)
            <div class="space-y-2">
                @foreach($alerts->take(3) as $alert)
                    <div class="bg-white border border-red-200 rounded-lg p-3 active:bg-gray-50 transition-colors cursor-pointer"
                         onclick="window.location.href='{{ $alert['link'] ?? '#' }}'">
                        
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                                <i class="fas {{ $alert['type'] === 'project' ? 'fa-folder' : 'fa-circle-check' }} 
                                          text-red-600 text-sm"></i>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-gray-900 text-sm truncate">{{ $alert['title'] }}</h3>
                                <p class="text-xs text-gray-600 mt-0.5">{{ $alert['subtitle'] }}</p>
                                <div class="mt-1.5">
                                    <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded font-medium">
                                        {{ $alert['days_overdue'] }} hari
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <i class="fas fa-check-circle text-2xl text-gray-400 mb-1"></i>
                <p class="text-sm text-gray-600">Tidak ada alert kritis</p>
            </div>
        @endif
    </div>

    {{-- Today's Agenda --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-900">
                <i class="fas fa-calendar-day text-blue-600 mr-2"></i>
                Agenda Hari Ini
            </h2>
            <span class="text-xs text-gray-500">{{ now()->format('d M Y') }}</span>
        </div>

        @if(count($agenda) > 0)
            <div class="space-y-2">
                @foreach($agenda as $item)
                    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3"
                         onclick="window.location.href='{{ $item['link'] }}'">
                        <div class="text-center flex-shrink-0 w-12">
                            <div class="text-xs text-gray-500">{{ $item['time'] }}</div>
                            <div class="text-2xl">{{ $item['icon'] }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 truncate">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $item['project'] }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-xl p-6 text-center">
                <i class="fas fa-calendar-check text-3xl text-gray-400 mb-2"></i>
                <p class="text-sm text-gray-600">Tidak ada agenda hari ini</p>
            </div>
        @endif
    </div>

    {{-- Collapsible Sections --}}
    <div class="space-y-3">
        
        {{-- Active Projects --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ expanded: false }">
            <button @click="expanded = !expanded" 
                    class="w-full px-4 py-3 flex items-center justify-between text-left">
                <div class="flex items-center gap-2">
                    <i class="fas fa-folder-open text-blue-600"></i>
                    <span class="font-semibold text-gray-900">Proyek Aktif</span>
                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">
                        {{ $projectStats['active'] }}
                    </span>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="expanded && 'rotate-180'"></i>
            </button>
            <div x-show="expanded" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:leave="transition ease-in duration-150"
                 class="px-4 pb-3">
                <a href="{{ mobile_route('projects.index') }}" 
                   class="block py-2 text-sm text-blue-600 font-medium">
                    Lihat semua proyek →
                </a>
            </div>
        </div>

        {{-- Pending Payments --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ expanded: false }">
            <button @click="expanded = !expanded" 
                    class="w-full px-4 py-3 flex items-center justify-between text-left">
                <div class="flex items-center gap-2">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                    <span class="font-semibold text-gray-900">Pending Payment</span>
                    <span class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded-full font-medium">
                        {{ $paymentStats['pending'] }}
                    </span>
                </div>
                <i class="fas fa-chevron-down transition-transform" 
                   :class="expanded && 'rotate-180'"></i>
            </button>
            <div x-show="expanded" 
                 x-transition
                 class="px-4 pb-3">
                <a href="{{ mobile_route('financial.index') }}" 
                   class="block py-2 text-sm text-blue-600 font-medium">
                    Lihat pending payments →
                </a>
            </div>
        </div>

    </div>

    {{-- Back to Top Button (appears on scroll) --}}
    <button x-show="showBackToTop" 
            @click="scrollToTop()"
            x-transition
            class="fixed bottom-24 right-4 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg 
                   flex items-center justify-center hover:bg-blue-700 transition-colors z-40">
        <i class="fas fa-arrow-up"></i>
    </button>

</div>

@endsection

@push('scripts')
<script>
function dashboardMobile() {
    return {
        showBackToTop: false,

        init() {
            // Pull to refresh
            this.initPullToRefresh();
            
            // Scroll detection
            window.addEventListener('scroll', () => {
                this.showBackToTop = window.scrollY > 300;
            });
        },

        handleSwipeLeft(id) {
            // Show quick actions (Archive, Delete, etc.)
            console.log('Swipe left:', id);
            // Implementation for showing action buttons
        },

        handleSwipeRight(id) {
            // Mark as done or complete
            console.log('Swipe right:', id);
            this.markAsDone(id);
        },

        markAsDone(id) {
            // API call to mark as done
            fetch(`/m/tasks/${id}/complete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success feedback
                    this.showToast('✓ Item selesai!', 'success');
                    // Refresh data
                    setTimeout(() => window.location.reload(), 1000);
                }
            });
        },

        initPullToRefresh() {
            let startY = 0;
            let currentY = 0;
            let pulling = false;

            document.addEventListener('touchstart', (e) => {
                if (window.scrollY === 0) {
                    startY = e.touches[0].pageY;
                    pulling = true;
                }
            });

            document.addEventListener('touchmove', (e) => {
                if (!pulling) return;
                currentY = e.touches[0].pageY;
                const pullDistance = currentY - startY;

                if (pullDistance > 80) {
                    // Show refresh indicator
                    this.showRefreshIndicator();
                }
            });

            document.addEventListener('touchend', () => {
                if (!pulling) return;
                const pullDistance = currentY - startY;

                if (pullDistance > 80) {
                    this.refreshData();
                }
                pulling = false;
            });
        },

        refreshData() {
            // Show loading
            this.showToast('Memuat data...', 'info');

            // Fetch fresh data
            fetch('/mobile/dashboard/refresh')
                .then(response => response.json())
                .then(data => {
                    this.showToast('✓ Data diperbarui!', 'success');
                    setTimeout(() => window.location.reload(), 500);
                });
        },

        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        },

        showToast(message, type) {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 
                              ${type === 'success' ? 'bg-green-500' : 'bg-blue-500'} text-white`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    }
}

function toggleOptions(id) {
    // Show context menu for specific item
    console.log('Toggle options for:', id);
}
</script>
@endpush
