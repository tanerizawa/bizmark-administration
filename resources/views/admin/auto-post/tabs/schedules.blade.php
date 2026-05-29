{{-- Status Stats --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <a href="{{ route('auto-post.index', ['tab' => 'schedules']) }}" 
       class="rounded-apple-lg p-4 transition-apple hover:bg-white/5 {{ !request('schedule_status') ? 'ring-2 ring-apple-blue' : '' }}"
    style="background: var(--dark-bg-tertiary); border: 1px solid rgba(255,255,255,0.06);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-dark-text-secondary">All</p>
                <p class="text-lg font-bold text-white mt-1">
                    {{ ($scheduleStats['pending'] ?? 0) + ($scheduleStats['processing'] ?? 0) + ($scheduleStats['completed'] ?? 0) + ($scheduleStats['failed'] ?? 0) }}
                </p>
            </div>
            <i class="fas fa-list text-xl text-dark-text-tertiary"></i>
        </div>
    </a>
    
    <a href="{{ route('auto-post.index', ['tab' => 'schedules', 'schedule_status' => 'pending']) }}" 
       class="rounded-apple-lg p-4 transition-apple hover:bg-white/5 {{ request('schedule_status') === 'pending' ? 'ring-2 ring-apple-yellow' : '' }}"
    style="background: rgba(255,214,10,0.1); border: 1px solid rgba(255,214,10,0.14);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-apple-yellow">Pending</p>
                <p class="text-2xl font-bold text-apple-yellow mt-1">{{ $scheduleStats['pending'] ?? 0 }}</p>
            </div>
            <i class="fas fa-clock text-xl text-apple-yellow/50"></i>
        </div>
    </a>
    
    <a href="{{ route('auto-post.index', ['tab' => 'schedules', 'schedule_status' => 'processing']) }}" 
       class="rounded-apple-lg p-4 transition-apple hover:bg-white/5 {{ request('schedule_status') === 'processing' ? 'ring-2 ring-apple-blue' : '' }}"
    style="background: rgba(10,132,255,0.1); border: 1px solid rgba(10,132,255,0.14);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-apple-blue">Processing</p>
                <p class="text-2xl font-bold text-apple-blue mt-1">{{ $scheduleStats['processing'] ?? 0 }}</p>
            </div>
            <i class="fas fa-spinner fa-spin text-xl text-apple-blue/50"></i>
        </div>
    </a>
    
    <a href="{{ route('auto-post.index', ['tab' => 'schedules', 'schedule_status' => 'completed']) }}" 
       class="rounded-apple-lg p-4 transition-apple hover:bg-white/5 {{ request('schedule_status') === 'completed' ? 'ring-2 ring-apple-green' : '' }}"
    style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.14);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-apple-green">Completed</p>
                <p class="text-2xl font-bold text-apple-green mt-1">{{ $scheduleStats['completed'] ?? 0 }}</p>
            </div>
            <i class="fas fa-check-circle text-xl text-apple-green/50"></i>
        </div>
    </a>
</div>

{{-- Actions Bar --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('auto-post.index', ['tab' => 'schedules', 'schedule_status' => 'failed']) }}" 
           class="inline-flex items-center px-4 py-2.5 rounded-apple text-sm font-medium transition-apple {{ request('schedule_status') === 'failed' ? 'bg-apple-red text-white' : 'bg-dark-bg-tertiary auto-soft-field text-dark-text-secondary hover:bg-white/5' }}">
            <i class="fas fa-exclamation-triangle mr-2"></i>Failed ({{ $scheduleStats['failed'] ?? 0 }})
        </a>
    </div>
    
    <div class="flex items-center gap-3">
        <a href="{{ route('auto-post.schedules.create') }}" 
           class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-blue-700 transition-apple">
            <i class="fas fa-plus mr-2"></i>Jadwal Manual
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
    <input type="hidden" name="tab" value="schedules">
    @if(request('schedule_status'))
        <input type="hidden" name="schedule_status" value="{{ request('schedule_status') }}">
    @endif

    <input type="text"
           name="schedule_search"
           value="{{ request('schedule_search') }}"
           placeholder="Cari judul topic..."
           class="px-4 py-2.5 rounded-apple text-sm bg-dark-bg-tertiary auto-soft-field text-white placeholder-dark-text-tertiary focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">

    <input type="date"
           name="schedule_date_from"
           value="{{ request('schedule_date_from') }}"
           class="px-4 py-2.5 rounded-apple text-sm bg-dark-bg-tertiary auto-soft-field text-white focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">

    <input type="date"
           name="schedule_date_to"
           value="{{ request('schedule_date_to') }}"
           class="px-4 py-2.5 rounded-apple text-sm bg-dark-bg-tertiary auto-soft-field text-white focus:border-apple-blue focus:ring-1 focus:ring-apple-blue">

    <div class="flex items-center gap-2">
        <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-apple text-sm font-medium bg-dark-bg-tertiary auto-soft-field text-white hover:bg-white/5 transition-apple">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        <a href="{{ route('auto-post.index', ['tab' => 'schedules', 'schedule_status' => request('schedule_status')]) }}"
           class="inline-flex items-center px-4 py-2.5 rounded-apple text-sm font-medium bg-dark-bg-tertiary auto-soft-field text-dark-text-secondary hover:bg-white/5 transition-apple">
            Reset
        </a>
    </div>
</form>

{{-- Bulk Actions --}}
<form method="POST" action="{{ route('auto-post.schedules.bulk-action') }}" class="mb-6 flex flex-col md:flex-row md:items-center gap-3" x-data>
    @csrf
    <input type="hidden" name="scope" value="filtered">
    <input type="hidden" name="schedule_status" value="{{ request('schedule_status') }}">
    <input type="hidden" name="schedule_search" value="{{ request('schedule_search') }}">
    <input type="hidden" name="schedule_date_from" value="{{ request('schedule_date_from') }}">
    <input type="hidden" name="schedule_date_to" value="{{ request('schedule_date_to') }}">

    <div class="text-xs" style="color: rgba(235,235,245,0.6);">Aksi cepat untuk data terfilter:</div>
    <div class="flex flex-wrap items-center gap-2">
        <button type="submit" name="action" value="process_pending" class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-medium bg-apple-blue text-white hover:bg-blue-700 transition-apple" @click.prevent="if(confirm('Proses semua jadwal pending pada hasil filter saat ini?')) $el.closest('form').submit()">
            <i class="fas fa-play mr-1.5"></i>Proses Pending
        </button>
        <button type="submit" name="action" value="retry_failed" class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(255,159,10,0.16); color: rgba(255,159,10,1); border: 1px solid rgba(255,159,10,0.2);" @click.prevent="if(confirm('Retry semua jadwal failed pada hasil filter saat ini?')) $el.closest('form').submit()">
            <i class="fas fa-rotate-right mr-1.5"></i>Retry Failed
        </button>
        <button type="submit" name="action" value="cancel_pending" class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-medium" style="background: rgba(255,69,58,0.14); color: rgba(255,69,58,1); border: 1px solid rgba(255,69,58,0.18);" @click.prevent="if(confirm('Batalkan semua jadwal pending pada hasil filter saat ini?')) $el.closest('form').submit()">
            <i class="fas fa-ban mr-1.5"></i>Batalkan Pending
        </button>
    </div>
</form>

{{-- Schedules Table --}}
<div class="bg-dark-bg-tertiary rounded-apple-lg auto-soft-card overflow-hidden">
    @if(isset($schedules) && $schedules->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="auto-soft-divider-bottom">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Topic</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Scheduled For</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Article</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody class="auto-soft-divide-y">
                    @foreach($schedules as $schedule)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-white">
                                    {{ Str::limit(optional($schedule->topic)->title ?? 'Unknown Topic', 40) }}
                                </div>
                                @if(!$schedule->topic)
                                    <div class="text-xs mt-1" style="color: rgba(255,159,10,0.85);">
                                        Topic tidak ditemukan (mungkin sudah dihapus)
                                    </div>
                                @elseif(method_exists($schedule->topic, 'trashed') && $schedule->topic->trashed())
                                    <div class="text-xs mt-1" style="color: rgba(255,159,10,0.85);">
                                        Topic terarsip (soft deleted)
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-dark-text-secondary">
                                @php
                                    $scheduledDate = data_get($schedule, $scheduleDateColumn ?? 'scheduled_at');
                                @endphp
                                {{ $scheduledDate ? \Illuminate\Support\Carbon::parse($scheduledDate)->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => 'rgba(255,214,10,0.15)', 'color' => 'rgba(255,214,10,1)', 'icon' => 'clock'],
                                        'processing' => ['bg' => 'rgba(10,132,255,0.15)', 'color' => 'rgba(10,132,255,1)', 'icon' => 'spinner fa-spin'],
                                        'completed' => ['bg' => 'rgba(52,199,89,0.15)', 'color' => 'rgba(52,199,89,1)', 'icon' => 'check-circle'],
                                        'failed' => ['bg' => 'rgba(255,69,58,0.15)', 'color' => 'rgba(255,69,58,1)', 'icon' => 'times-circle'],
                                    ];
                                    $cfg = $statusConfig[$schedule->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-apple" style="background: {{ $cfg['bg'] }}; color: {{ $cfg['color'] }};">
                                    <i class="fas fa-{{ $cfg['icon'] }} mr-1.5"></i>
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($schedule->article)
                                    <a href="{{ route('articles.edit', $schedule->article) }}" 
                                       class="text-sm text-apple-blue hover:underline">
                                        {{ Str::limit($schedule->article->title, 30) }}
                                    </a>
                                @else
                                    <span class="text-dark-text-tertiary text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($schedule->status === 'pending')
                                        <form action="{{ route('auto-post.schedules.process-now', $schedule) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Proses jadwal ini sekarang?')) $el.submit()">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-apple text-dark-text-secondary hover:text-apple-blue hover:bg-apple-blue/10 transition-colors" title="Process now">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('auto-post.schedules.destroy', $schedule) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Batalkan jadwal ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-apple text-dark-text-secondary hover:text-apple-red hover:bg-apple-red/10 transition-colors">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($schedule->status === 'failed')
                                        <form action="{{ route('auto-post.schedules.retry', $schedule) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Retry jadwal failed ini?')) $el.submit()">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-apple text-dark-text-secondary hover:text-apple-blue hover:bg-apple-blue/10 transition-colors" title="Retry">
                                                <i class="fas fa-rotate-right"></i>
                                            </button>
                                        </form>
                                        <button onclick="showError(this)"
                                                data-error-message="{{ e($schedule->error_message ?? 'Unknown error') }}"
                                                class="p-2 rounded-apple text-dark-text-secondary hover:text-apple-orange hover:bg-apple-orange/10 transition-colors">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($schedules->hasPages())
            <div class="px-4 py-3 auto-soft-divider-top">
                {{ $schedules->appends(['tab' => 'schedules', 'schedule_status' => request('schedule_status'), 'schedule_search' => request('schedule_search'), 'schedule_date_from' => request('schedule_date_from'), 'schedule_date_to' => request('schedule_date_to')])->links() }}
            </div>
        @endif
    @else
        <div class="p-8 text-center">
            <i class="fas fa-calendar-alt text-4xl text-dark-text-tertiary mb-3"></i>
            <p class="text-dark-text-secondary mb-4">Belum ada jadwal posting</p>
            <a href="{{ route('auto-post.schedules.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-blue-700 transition-apple">
                <i class="fas fa-plus mr-2"></i>Buat Jadwal Pertama
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function showError(buttonEl) {
    const message = buttonEl?.dataset?.errorMessage || 'Unknown error';
    alert('Error Details:\n\n' + message);
}
</script>
@endpush
