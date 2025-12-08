@extends('layouts.app')

@section('title', 'AI Settings - Recent Changes')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>Recent Changes - AI Settings</h5>
                            <p class="text-sm mb-0">Audit trail of all setting modifications</p>
                        </div>
                        <a href="{{ route('admin.ai-settings.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Settings
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Time Filter -->
                    <div class="mb-4">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 1]) }}" 
                               class="btn btn-sm {{ $days == 1 ? 'btn-primary' : 'btn-outline-primary' }}">
                                Last 24h
                            </a>
                            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 7]) }}" 
                               class="btn btn-sm {{ $days == 7 ? 'btn-primary' : 'btn-outline-primary' }}">
                                Last 7 days
                            </a>
                            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 30]) }}" 
                               class="btn btn-sm {{ $days == 30 ? 'btn-primary' : 'btn-outline-primary' }}">
                                Last 30 days
                            </a>
                            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 90]) }}" 
                               class="btn btn-sm {{ $days == 90 ? 'btn-primary' : 'btn-outline-primary' }}">
                                Last 90 days
                            </a>
                        </div>
                    </div>

                    @if($changes->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No changes recorded in the last {{ $days }} days.
                    </div>
                    @else
                    <!-- Changes Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Time</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Setting</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Change</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Changed By</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reason</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($changes as $change)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-xs font-weight-bold">{{ $change->created_at->format('M d, Y') }}</span>
                                            <span class="text-xxs text-secondary">{{ $change->created_at->format('H:i:s') }}</span>
                                            <span class="text-xxs text-secondary">{{ $change->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-xs font-weight-bold">{{ $change->setting->key ?? $change->key }}</span>
                                            <span class="text-xxs text-secondary">{{ $change->setting->category ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($change->old_value)
                                            <span class="text-xxs text-danger">
                                                <i class="fas fa-minus-circle"></i> Old: {{ \Str::limit($change->old_value, 30) }}
                                            </span>
                                            @endif
                                            <span class="text-xxs text-success">
                                                <i class="fas fa-plus-circle"></i> New: {{ \Str::limit($change->new_value, 30) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-gradient-primary">
                                                <span class="text-xs text-white">{{ substr($change->changed_by_name, 0, 2) }}</span>
                                            </div>
                                            <span class="text-xs font-weight-bold">{{ $change->changed_by_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs">{{ $change->reason ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-xs font-monospace">{{ $change->ip_address }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $changes->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
