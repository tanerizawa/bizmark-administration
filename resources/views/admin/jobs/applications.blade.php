@extends('layouts.app')

@section('title', $vacancy->title . ' - Applications')

@section('content')
@php
    $statusLabels = [
        'pending' => 'Pending',
        'reviewed' => 'Direview',
        'interview' => 'Interview',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
    ];

    $statusColors = [
        'pending' => ['bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,1)'],
        'reviewed' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)'],
        'interview' => ['bg' => 'rgba(175,82,222,0.15)', 'text' => 'rgba(175,82,222,1)'],
        'accepted' => ['bg' => 'rgba(48,209,88,0.15)', 'text' => 'rgba(48,209,88,1)'],
        'rejected' => ['bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
        ['label' => $vacancy->title, 'url' => route('admin.jobs.show', $vacancy->id)],
        ['label' => 'Applications']
    ]" />

    {{-- Header with Tabs --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-60 h-60 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">{{ $vacancy->title }}</h1>
                    <p class="text-sm mt-1" style="color: rgba(235,235,245,0.7);">
                        {{ $vacancy->applications_count }} Total Applications
                    </p>
                </div>
                <a href="{{ route('admin.jobs.create') }}" class="btn-primary-sm">
                    <i class="fas fa-plus mr-2"></i>New Job
                </a>
            </div>

            {{-- Tab Navigation --}}
            <x-job-tabs :vacancy="$vacancy" active-tab="applications" />
        </div>
    </section>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-lg p-4">
        <form method="GET" action="{{ route('admin.jobs.applications', $vacancy->id) }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search by name, email, phone..." 
                       class="input-apple w-full">
            </div>
            
            <select name="status" class="input-apple min-w-[150px]">
                <option value="">All Status</option>
                @foreach($statusLabels as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-secondary-sm">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            @if(request('search') || request('status'))
                <a href="{{ route('admin.jobs.applications', $vacancy->id) }}" class="btn-secondary-sm">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>
    </section>

    {{-- Applications Table --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden">
        @if($applications->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(235,235,245,0.1);">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Candidate
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Contact
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Applied
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Status
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color: rgba(235,235,245,0.1);">
                        @foreach($applications as $application)
                            <tr class="hover:bg-white/5 transition-apple">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" style="background: rgba(10,132,255,0.2);">
                                            {{ substr($application->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white">{{ $application->full_name }}</p>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                                {{ $application->education_level ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm text-white">{{ $application->email }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $application->phone }}</p>
                                </td>
                                <td class="px-4 py-3 text-sm" style="color: rgba(235,235,245,0.8);">
                                    {{ $application->created_at->format('d M Y') }}
                                    <span class="block text-xs" style="color: rgba(235,235,245,0.5);">
                                        {{ $application->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                                          style="background: {{ $statusColors[$application->status]['bg'] }}; color: {{ $statusColors[$application->status]['text'] }};">
                                        {{ $statusLabels[$application->status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.recruitment.pipeline.show', $application->id) }}" class="btn-secondary-sm">
                                            <i class="fas fa-stream mr-1"></i>Pipeline
                                        </a>
                                        <a href="{{ route('admin.applications.show', $application->id) }}" class="btn-secondary-sm">
                                            <i class="fas fa-file-alt mr-1"></i>Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3" style="border-top: 1px solid rgba(235,235,245,0.1);">
                {{ $applications->links() }}
            </div>
        @else
            <div class="p-8 text-center" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm">No applications found</p>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.jobs.applications', $vacancy->id) }}" class="text-xs text-apple-blue hover:underline mt-2 inline-block">
                        Clear filters
                    </a>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
