@extends('mobile.layouts.app')

@section('title', 'Projects')

@section('content')
<div class="mobile-page" x-data="projectsPage()">
    <!-- Filter Tabs -->
    <div class="sticky top-14 z-10 bg-white border-b border-gray-200 safe-top">
        <div class="flex overflow-x-auto scrollbar-hide">
            <button @click="filterStatus('active')" 
                    :class="currentStatus === 'active' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm transition-colors">
                Aktif
                <span class="ml-1" x-text="`(${stats.active || 0})`"></span>
            </button>
            <button @click="filterStatus('overdue')" 
                    :class="currentStatus === 'overdue' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm transition-colors">
                Terlambat
                <span class="ml-1" x-text="`(${stats.overdue || 0})`"></span>
            </button>
            <button @click="filterStatus('completed')" 
                    :class="currentStatus === 'completed' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500'"
                    class="flex-shrink-0 px-4 py-3 border-b-2 font-medium text-sm transition-colors">
                Selesai
                <span class="ml-1" x-text="`(${stats.completed || 0})`"></span>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="p-4 bg-gray-50">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="search" 
                   x-model="searchQuery"
                   @input.debounce.300ms="search()"
                   placeholder="Cari project..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    <!-- Projects List -->
    <div class="pb-20">
        <template x-if="loading">
            <div class="p-8 text-center">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                <p class="mt-2 text-gray-500">Loading projects...</p>
            </div>
        </template>

        <template x-if="!loading && projects.length === 0">
            <div class="p-8 text-center">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Tidak ada project</p>
            </div>
        </template>

        <div class="divide-y divide-gray-100">
            <template x-for="project in projects" :key="project.id">
                <a :href="`/m/projects/${project.id}`" 
                   class="block p-4 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                    <!-- Project Header -->
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1 min-w-0 pr-3">
                            <h3 class="font-semibold text-gray-900 truncate" x-text="project.name"></h3>
                            <p class="text-sm text-gray-500 truncate mt-0.5" x-text="project.institution?.name || '-'"></p>
                        </div>
                        <span :class="getStatusColor(project.status?.name)" 
                              class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full"
                              x-text="project.status?.name || 'Unknown'"></span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                            <span>Progress</span>
                            <span x-text="`${project.progress || 0}%`"></span>
                        </div>
                        <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div :style="`width: ${project.progress || 0}%`" 
                                 class="h-full bg-blue-500 transition-all duration-300"></div>
                        </div>
                    </div>

                    <!-- Project Meta -->
                    <div class="flex items-center gap-4 text-xs text-gray-600">
                        <div class="flex items-center gap-1">
                            <i class="fas fa-calendar-alt"></i>
                            <span x-text="formatDate(project.deadline)"></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="fas fa-dollar-sign"></i>
                            <span x-text="formatCurrency(project.budget)"></span>
                        </div>
                        <template x-if="project.days_left !== undefined">
                            <div class="flex items-center gap-1" 
                                 :class="project.days_left < 0 ? 'text-red-600' : project.days_left < 7 ? 'text-yellow-600' : ''">
                                <i class="fas fa-clock"></i>
                                <span x-text="getDaysLeftText(project.days_left)"></span>
                            </div>
                        </template>
                    </div>
                </a>
            </template>
        </div>

        <!-- Load More -->
        <template x-if="hasMore && !loading">
            <div class="p-4">
                <button @click="loadMore()" 
                        class="w-full py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 active:bg-gray-300 transition-colors">
                    Load More
                </button>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script>
function projectsPage() {
    return {
        projects: @json($projects->items()),
        currentStatus: '{{ $currentStatus }}',
        searchQuery: '',
        loading: false,
        hasMore: {{ $projects->hasMorePages() ? 'true' : 'false' }},
        currentPage: {{ $projects->currentPage() }},
        stats: {
            active: 0,
            overdue: 0,
            completed: 0
        },

        init() {
            this.loadStats();
        },

        async loadStats() {
            // Load quick stats for filter badges
            try {
                const response = await fetch('/m/projects?stats=true');
                const data = await response.json();
                this.stats = data.stats || {};
            } catch (error) {
                console.error('Failed to load stats:', error);
            }
        },

        async filterStatus(status) {
            if (this.currentStatus === status) return;
            
            this.currentStatus = status;
            this.loading = true;
            
            try {
                const response = await fetch(`/m/projects?status=${status}`);
                const data = await response.json();
                this.projects = data.projects || [];
                this.hasMore = data.hasMore || false;
                this.currentPage = 1;
            } catch (error) {
                console.error('Filter error:', error);
            } finally {
                this.loading = false;
            }
        },

        async search() {
            if (this.searchQuery.length < 2) {
                return;
            }

            this.loading = true;
            
            try {
                const response = await fetch(`/m/projects/search?q=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                this.projects = data.results || [];
                this.hasMore = false;
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadMore() {
            this.loading = true;
            
            try {
                const response = await fetch(`/m/projects?status=${this.currentStatus}&page=${this.currentPage + 1}`);
                const data = await response.json();
                this.projects.push(...(data.projects || []));
                this.hasMore = data.hasMore || false;
                this.currentPage++;
            } catch (error) {
                console.error('Load more error:', error);
            } finally {
                this.loading = false;
            }
        },

        getStatusColor(status) {
            const colors = {
                'Aktif': 'bg-blue-100 text-blue-800',
                'Selesai': 'bg-green-100 text-green-800',
                'Tertunda': 'bg-yellow-100 text-yellow-800',
                'Dibatalkan': 'bg-red-100 text-red-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        },

        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', { 
                day: 'numeric', 
                month: 'short', 
                year: 'numeric' 
            });
        },

        formatCurrency(amount) {
            if (!amount) return 'Rp 0';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        },

        getDaysLeftText(days) {
            if (days < 0) return `${Math.abs(days)} hari terlambat`;
            if (days === 0) return 'Hari ini!';
            if (days === 1) return 'Besok';
            return `${days} hari lagi`;
        }
    };
}
</script>
@endpush
