@props(['activeTab' => 'overview', 'vacancy'])

@php
    $tabs = [
        'overview' => [
            'label' => 'Overview',
            'icon' => 'fas fa-info-circle',
            'route' => route('admin.jobs.show', $vacancy->id),
        ],
        'applications' => [
            'label' => 'Applications',
            'icon' => 'fas fa-users',
            'route' => route('admin.jobs.applications', $vacancy->id),
            'badge' => $vacancy->applications_count ?? 0,
        ],
        'pipeline' => [
            'label' => 'Pipeline',
            'icon' => 'fas fa-stream',
            'route' => route('admin.jobs.pipeline', $vacancy->id),
        ],
        'tests' => [
            'label' => 'Tests',
            'icon' => 'fas fa-clipboard-check',
            'route' => route('admin.jobs.tests', $vacancy->id),
        ],
        'interviews' => [
            'label' => 'Interviews',
            'icon' => 'fas fa-calendar-alt',
            'route' => route('admin.jobs.interviews', $vacancy->id),
        ],
        'settings' => [
            'label' => 'Settings',
            'icon' => 'fas fa-cog',
            'route' => route('admin.jobs.edit', $vacancy->id),
        ],
    ];
@endphp

<div class="tabs-apple-container mb-6">
    <div class="tabs-apple">
        @foreach($tabs as $key => $tab)
            <a href="{{ $tab['route'] }}" 
               class="tab-item {{ $activeTab === $key ? 'active' : '' }}"
               @if($activeTab === $key) aria-current="page" @endif>
                <i class="{{ $tab['icon'] }} mr-2"></i>
                <span>{{ $tab['label'] }}</span>
                @if(isset($tab['badge']) && $tab['badge'] > 0)
                    <span class="tab-badge">{{ $tab['badge'] }}</span>
                @endif
            </a>
        @endforeach
    </div>
</div>

<style>
.tabs-apple-container {
    border-bottom: 1px solid rgba(235,235,245,0.1);
    margin-left: -1.5rem;
    margin-right: -1.5rem;
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.tabs-apple {
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.tabs-apple::-webkit-scrollbar {
    display: none;
}

.tab-item {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.25rem;
    color: rgba(235,235,245,0.6);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    font-size: 0.875rem;
    white-space: nowrap;
    cursor: pointer;
}

.tab-item:hover {
    color: rgba(235,235,245,0.9);
    background: rgba(255,255,255,0.03);
}

.tab-item.active {
    color: rgba(10,132,255,1);
    border-bottom-color: rgba(10,132,255,1);
}

.tab-item i {
    font-size: 0.875rem;
}

.tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.25rem;
    height: 1.25rem;
    padding: 0 0.375rem;
    background: rgba(10,132,255,0.2);
    color: rgba(10,132,255,1);
    border-radius: 0.625rem;
    font-size: 0.6875rem;
    font-weight: 600;
    line-height: 1;
}

.tab-item.active .tab-badge {
    background: rgba(10,132,255,0.3);
}

@media (max-width: 768px) {
    .tab-item {
        padding: 0.75rem 1rem;
        font-size: 0.8125rem;
    }
    
    .tab-item span {
        display: none;
    }
    
    .tab-item i {
        margin-right: 0 !important;
    }
}
</style>
