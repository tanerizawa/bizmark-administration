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
    <button onclick="window.location.href='{{ route('mobile.notifications.index') }}'" 
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
    
    {{-- Swipeable Metrics Cards --}}
    <div class="relative -mx-4 mb-6">
        <div class="overflow-x-auto snap-x snap-mandatory scrollbar-hide" 
             x-ref="metricsCarousel"
             @touchstart="handleTouchStart($event)"
             @touchend="handleTouchEnd($event)">
            <div class="flex gap-4 px-4">
                
                {{-- Card 1: Urgent Alerts --}}
                <div class="flex-shrink-0 w-[85vw] snap-start">
                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-5 text-white shadow-lg"
                         onclick="window.location.href='{{ route('mobile.tasks.urgent') }}'">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-white/20 rounded-full p-2">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <span class="text-xs uppercase tracking-wider opacity-90">Urgent</span>
                        </div>
                        <div class="text-5xl font-bold mb-2">{{ $metrics['urgent_count'] }}</div>
                        <div class="text-sm opacity-90">Perlu tindakan sekarang</div>
                        <div class="mt-3 flex items-center text-xs">
                            <span class="opacity-75">Tap untuk detail</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Cash & Runway --}}
                <div class="flex-shrink-0 w-[85vw] snap-start">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-5 text-white shadow-lg"
                         onclick="window.location.href='{{ route('mobile.financial.index') }}'">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-white/20 rounded-full p-2">
                                <i class="fas fa-wallet text-2xl"></i>
                            </div>
                            <span class="text-xs uppercase tracking-wider opacity-90">Runway</span>
                        </div>
                        <div class="text-4xl font-bold mb-1">{{ $metrics['runway_months'] }} bln</div>
                        <div class="text-sm opacity-90">Rp {{ number_format($cash_pulse['balance'] / 1000000, 1) }}M</div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs px-2 py-1 bg-white/20 rounded-full">
                                {{ ucfirst($cash_pulse['status']) }}
                            </span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Pending Approvals --}}
                <div class="flex-shrink-0 w-[85vw] snap-start">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 text-white shadow-lg"
                         onclick="window.location.href='{{ route('mobile.approvals.index') }}'">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-white/20 rounded-full p-2">
                                <i class="fas fa-file-signature text-2xl"></i>
                            </div>
                            <span class="text-xs uppercase tracking-wider opacity-90">Approvals</span>
                        </div>
                        <div class="text-5xl font-bold mb-2">{{ $metrics['approvals_count'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Dokumen menunggu</div>
                        <div class="mt-3 flex items-center text-xs">
                            <span class="opacity-75">Quick approve</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Today's Tasks --}}
                <div class="flex-shrink-0 w-[85vw] snap-start">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg"
                         onclick="window.location.href='{{ route('mobile.tasks.index') }}'">
                        <div class="flex items-center justify-between mb-3">
                            <div class="bg-white/20 rounded-full p-2">
                                <i class="fas fa-tasks text-2xl"></i>
                            </div>
                            <span class="text-xs uppercase tracking-wider opacity-90">Today</span>
                        </div>
                        <div class="text-5xl font-bold mb-2">{{ $metrics['tasks_today'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">{{ $metrics['tasks_today'] ?? 0 }} hari ini • {{ $metrics['tasks_overdue'] ?? 0 }} overdue</div>
                        <div class="mt-3 flex items-center text-xs">
                            <span class="opacity-75">View all tasks</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
        {{-- Pagination Dots --}}
        <div class="flex justify-center gap-2 mt-4">
            @for($i = 0; $i < 4; $i++)
                <div class="w-2 h-2 rounded-full transition-all"
                     :class="currentMetricCard === {{ $i }} ? 'bg-blue-500 w-6' : 'bg-gray-300'"></div>
            @endfor
        </div>
    </div>

    {{-- Critical Focus Section --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-bold text-gray-900">
                <i class="fas fa-bolt text-red-500 mr-2"></i>
                Perlu Tindakan Sekarang
            </h2>
            @if(count($alerts) > 3)
                <a href="{{ route('mobile.tasks.urgent') }}" class="text-sm text-blue-600 font-medium">
                    + {{ count($alerts) - 3 }} lagi
                </a>
            @endif
        </div>

        @if(count($alerts) > 0)
            <div class="space-y-2">
                @foreach($alerts->take(3) as $alert)
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden"
                         x-data="{ isOpen: false }"
                         @swipeleft="handleSwipeLeft('{{ $alert['id'] }}')"
                         @swiperight="handleSwipeRight('{{ $alert['id'] }}')">
                        
                        <div class="p-4 flex items-start gap-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                                        {{ $alert['type'] === 'project' ? 'bg-orange-100' : 'bg-red-100' }}">
                                <i class="fas {{ $alert['type'] === 'project' ? 'fa-folder' : 'fa-tasks' }} 
                                          {{ $alert['type'] === 'project' ? 'text-orange-600' : 'text-red-600' }}"></i>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 truncate">{{ $alert['title'] }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $alert['subtitle'] }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded-full">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $alert['days_overdue'] }} hari terlambat
                                    </span>
                                </div>
                            </div>
                            
                            <button onclick="toggleOptions('{{ $alert['id'] }}')" 
                                    class="flex-shrink-0 p-2 hover:bg-gray-100 rounded-full transition-colors">
                                <i class="fas fa-ellipsis-v text-gray-400"></i>
                            </button>
                        </div>

                        {{-- Swipe hint (subtle animation) --}}
                        <div class="px-4 pb-2 text-xs text-gray-400 flex items-center justify-center gap-2">
                            <i class="fas fa-hand-point-left animate-pulse"></i>
                            <span>Swipe untuk aksi cepat</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-green-50 rounded-xl p-6 text-center">
                <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                <p class="text-sm text-green-700">Tidak ada alert kritis! 🎉</p>
            </div>
        @endif
    </div>

    {{-- Cash Pulse Widget --}}
    <div class="mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                    Kas & Runway
                </h2>
                <span class="text-xs px-3 py-1 rounded-full font-medium
                             {{ $cash_pulse['status'] === 'healthy' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ ucfirst($cash_pulse['status']) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-600 mb-1">Saldo Saat Ini</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($cash_pulse['balance'] / 1000000, 1) }}M
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 mb-1">Runway</p>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $cash_pulse['runway'] }} bln
                    </p>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="mb-3">
                <div class="h-2 bg-white/50 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-500"
                         style="width: {{ min($cash_pulse['runway'] / 12 * 100, 100) }}%"></div>
                </div>
            </div>

            <button onclick="window.location.href='{{ route('mobile.financial.index') }}'" 
                    class="w-full py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                Lihat Detail Keuangan →
            </button>
        </div>
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
                <a href="{{ route('mobile.projects.index') }}" 
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
                <a href="{{ route('mobile.financial.index') }}" 
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
            class="fixed bottom-20 right-4 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg 
                   flex items-center justify-center hover:bg-blue-700 transition-colors z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

</div>

{{-- Floating Action Button (Quick Add) --}}
<button onclick="showQuickActions()" 
        class="fixed bottom-20 right-4 w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 
               text-white rounded-full shadow-xl flex items-center justify-center 
               hover:scale-110 transition-transform z-50">
    <i class="fas fa-plus text-xl"></i>
</button>
@endsection

@push('scripts')
<script>
function dashboardMobile() {
    return {
        currentMetricCard: 0,
        touchStartX: 0,
        touchEndX: 0,
        showBackToTop: false,

        init() {
            // Pull to refresh
            this.initPullToRefresh();
            
            // Scroll detection
            window.addEventListener('scroll', () => {
                this.showBackToTop = window.scrollY > 300;
            });

            // Swipe detection for metrics carousel
            this.$refs.metricsCarousel.addEventListener('scroll', () => {
                const scrollLeft = this.$refs.metricsCarousel.scrollLeft;
                const cardWidth = this.$refs.metricsCarousel.offsetWidth * 0.85;
                this.currentMetricCard = Math.round(scrollLeft / cardWidth);
            });
        },

        handleTouchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },

        handleTouchEnd(e) {
            this.touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
        },

        handleSwipe() {
            const swipeThreshold = 50;
            const diff = this.touchStartX - this.touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    // Swipe left
                    this.nextMetricCard();
                } else {
                    // Swipe right
                    this.prevMetricCard();
                }
            }
        },

        nextMetricCard() {
            if (this.currentMetricCard < 3) {
                this.currentMetricCard++;
                this.scrollToCard(this.currentMetricCard);
            }
        },

        prevMetricCard() {
            if (this.currentMetricCard > 0) {
                this.currentMetricCard--;
                this.scrollToCard(this.currentMetricCard);
            }
        },

        scrollToCard(index) {
            const cardWidth = this.$refs.metricsCarousel.offsetWidth * 0.85 + 16; // card + gap
            this.$refs.metricsCarousel.scrollTo({
                left: cardWidth * index,
                behavior: 'smooth'
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

function showQuickActions() {
    // Show bottom sheet with quick action options
    const modal = `
        <div class="fixed inset-0 bg-black/50 z-50 flex items-end" onclick="this.remove()">
            <div class="bg-white rounded-t-2xl w-full p-6 space-y-3" onclick="event.stopPropagation()">
                <div class="w-12 h-1 bg-gray-300 rounded-full mx-auto mb-4"></div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-4">Tambah Baru</h3>
                
                <a href="{{ route('mobile.projects.create') }}" 
                   class="block p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                    <i class="fas fa-folder-plus text-blue-600 mr-3"></i>
                    <span class="font-medium text-gray-900">Proyek Baru</span>
                </a>
                
                <a href="{{ route('mobile.tasks.index') }}" 
                   class="block p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                    <i class="fas fa-plus-circle text-green-600 mr-3"></i>
                    <span class="font-medium text-gray-900">Task Baru</span>
                </a>
                
                <a href="{{ route('mobile.financial.index') }}" 
                   class="block p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                    <i class="fas fa-money-bill-wave text-purple-600 mr-3"></i>
                    <span class="font-medium text-gray-900">Catat Pembayaran</span>
                </a>

                <button onclick="this.closest('.fixed').remove()" 
                        class="w-full py-3 text-gray-600 font-medium">
                    Batal
                </button>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modal);
}

function toggleOptions(id) {
    // Show context menu for specific item
    console.log('Toggle options for:', id);
}
</script>

{{-- Custom styles for scrollbar hide --}}
<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endpush
